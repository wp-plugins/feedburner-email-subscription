<?php
/**
	Feedburner Email Subscription Widget

	Give your biggest fans another way to keep up with your content feed by placing an email subscription widget on your site.
	This widget support shortcode for the short description.
	For another improvement, you can drop email to zourbuth@gmail.com

	Copyright 2013 zourbuth (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Feedburner_Email_Subscription extends WP_Widget {

	// Setup class vars
	var $prefix;
	var $textdomain;

	
	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.0
	 */		
	function __construct() {
	
		$this->prefix = 'feedburner-email-subscription';
		$this->textdomain = 'feedburner-email-subscription';	

		// Load the widget stylesheet for the widgets screen
		add_action( 'load-widgets.php', array(&$this, 'load_widget') );
		
		// Set up the widget options
		$widget_options = array(
			'classname' => $this->prefix,
			'description' => esc_html__( '[+] Give your biggest fans another way to keep up with your content feed by placing an email subscription widget on your site.', $this->textdomain )
		);

		// Set up the widget control options
		$control_options = array(
			'width' => 525,
			'height' => 350,
			'id_base' => $this->prefix
		);

		// Create the widget
		$this->WP_Widget( $this->prefix, esc_attr__( 'Feedburner Email Subscription', $this->textdomain ), $widget_options, $control_options );
		
		add_action('wp_ajax_fes_load_utility', array(&$this, 'fes_load_utility') );
				
		if ( is_active_widget( false, false, $this->id_base ) && ! is_admin() ) {
			wp_enqueue_style("feedburner-email-subscription", FEEDBURNER_EMAIL_SUBSCRIPTION_URL . "css/widget.css");
		}
	}

	
	/**
	 * Register the style or script, push the widget stylesheet widget.css into widget admin page
	 * @since 1.0
	 */	
	function load_widget() {
		wp_enqueue_style( 'feedburner-email-subscription-admin', FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'css/admin.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'feedburner-email-subscription-dialog', FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'js/jquery.dialog.js' );
		wp_localize_script( 'feedburner-email-subscription-dialog', 'fes', array(
			'nonce'		=> wp_create_nonce( 'fes-nonce' ),  // generate a nonce for further checking below
			'action'	=> 'fes_load_utility'
		));
	}
	
	
	/**
	 * Outputs another item
	 * @since 1.2.2
	 */
	function fes_load_utility() {
		// Check the nonce and if not isset the id, just die.
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'fes-nonce' ) )
			die();

		$ch = curl_init('http://marketplace.envato.com/api/edge/new-files-from-user:zourbuth,codecanyon.json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data);
		
		$i = 0; $html = '';
		if( $data ) {
			
			$html .= '<p><strong>Our Premium Plugins</strong></p>';
			$html .= '<ul class="envato-market">'; $i = 0;
			foreach( $data->{'new-files-from-user'} as $key => $value ) {
				if( $i < 15 ) {
					$html .= '<li class="market-item">';
						$html .= '<a href="'.$value->url.'?ref=zourbuth"><img src="'.$value->thumbnail.'"></a>';
					$html .= '</li>';
					$i++;
				}
			}
			$html .= '</ul>';
		}
		echo $html;
		exit;
	}
	
	
	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 1.0
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		echo $before_widget;
		
		// If a title was input by the user, display it
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		// Print intro text if exist
		if ( !empty( $instance['intro_text'] ) )
			echo '<p class="'. $this->id . '-intro-text intro-text">' . $instance['intro_text'] . '</p>';		
		
		// Set up the arguments
		$params = array(
			'feed' 		=> isset( $instance['feed_title'] ) ? $instance['feed_title'] : '',
			'text'		=> isset( $instance['text'] ) ? $instance['text'] : '',
			'submit' 	=> isset( $instance['submit'] ) ? $instance['submit'] : '',
		); 
		
		// Display the feedburner
		proc_feedburner_email_subscription( $params );
		
		// Print outro text if exist
		if ( !empty( $instance['outro_text'] ) )
			echo '<p class="'. $this->id . '-outro-text outro-text">' . $instance['outro_text'] . '</p>';
		
		echo $after_widget;
	}

	
	/**
	 * Update action for widget
	 * @since 1.0
	 */	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;
		$instance['feed_title'] = strip_tags( $new_instance['feed_title'] );
		$instance['text'] 		= strip_tags( $new_instance['text'] );
		$instance['submit'] 	= strip_tags( $new_instance['submit'] );
		$instance['intro_text'] = $new_instance['intro_text'];
		$instance['outro_text'] = $new_instance['outro_text'];
		
		return $instance;
	}	

	
	/**
	 * Displays the widget control options in the Widgets admin screen
	 * @since 1.0
	 */		
	function form( $instance ) {

		// Set up the default form values
		$defaults = array(
			'title' 		=> __( 'Email Subscription', $this->textdomain ),
			'text' 			=> __( 'Your email here', $this->textdomain ),
			'submit' 		=> __( 'Subscribe', $this->textdomain ),
			'feed_title'	=> '',
			'intro_text'	=> '',
			'outro_text' 	=> ''
		);

		// Merge the user-selected arguments with the defaults
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div id="<?php echo $this->id . '-wrapper'; ?>" class="totalControls">
			<div class="zframe-widget-controls columns-2">
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label>					
					<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
					<span class="controlDesc"><?php _e('Give the widget a title, leave empty for no title.', $this->textdomain ); ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'feed_title' ); ?>"><?php _e( 'Your Feedburner Title', $this->textdomain ); ?></label>
					<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'feed_title' ); ?>" name="<?php echo $this->get_field_name( 'feed_title' ); ?>" value="<?php echo esc_attr( $instance['feed_title'] ); ?>" />
					<span class="controlDesc"><?php _e('Example: zourbuth', $this->textdomain ); ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Input Text', $this->textdomain ); ?></label>					
					<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" value="<?php echo esc_attr( $instance['text'] ); ?>" />
					<span class="controlDesc"><?php _e('The email input text.', $this->textdomain ); ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'submit' ); ?>"><?php _e( 'Submit Button Text', $this->textdomain ); ?></label>					
					<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="<?php echo esc_attr( $instance['submit'] ); ?>" />
					<span class="controlDesc"><?php _e('The form submit button text.', $this->textdomain ); ?></span>
				</p>				
				<p>
					<label for="<?php echo $this->get_field_id( 'intro_text' ); ?>"><?php _e('Intro Text', $this->textdomain ) ?></label>
					<textarea name="<?php echo $this->get_field_name( 'intro_text' ); ?>" id="<?php echo $this->get_field_id( 'intro_text' ); ?>" rows="4" class="widefat"><?php echo htmlentities($instance['intro_text']); ?></textarea>
					<span class="controlDesc"><?php _e('This field support shortcodes and HTML.', $this->textdomain ); ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'outro_text' ); ?>"><?php _e('Outro Text', $this->textdomain ) ?></label>
					<textarea name="<?php echo $this->get_field_name( 'outro_text' ); ?>" id="<?php echo $this->get_field_id( 'outro_text' ); ?>" rows="4" class="widefat"><?php echo htmlentities($instance['outro_text']); ?></textarea>
					<span class="controlDesc"><?php _e('This field support shortcodes and HTML.', $this->textdomain ); ?></span>
				</p>			
				<p>
					Please give rating to <a href="http://wordpress.org/extend/plugins/feedburner-email-subscription/">Feedburner Email Subscription Widget</a> and visit <a href="http://zourbuth.com/archives/498/feedburner-email-subscription-wordpress-plugin/">zourbuth.com</a> for more informations.
				</p>
				
			</div>
			
			<div class="zframe-widget-controls columns-2 column-last">
				<div class="fesEm"></div>
				<p>
					<strong>Need Custom Code or Customization for lower cost?</strong>&nbsp;
					Please feel free to send mail to <a href="mailto:zourbuth@gmail.com">zourbuth@gmail.com</a>
				</p>
				<p>
					<?php _e( 'Like my work? Please consider to ', $this->textdomain ); ?><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W6D3WAJTVKAFC" title="Donate"><?php _e( 'donate', $this->textdomain ); ?></a>.	
				</p>
				<p>
					Subscribe to zourbuth by <a href="http://feedburner.google.com/fb/a/mailverify?uri=zourbuth&amp;loc=en_US">email</a><br />
					<small>&copy; Copyright <a href="http://zourbuth.com">zourbuth</a> <?php echo date('Y'); ?></small>.
				</p>
			</div>
			
			<div style="clear:both;">&nbsp;</div>
			<script type='text/javascript'>
				jQuery(document).ready(function($){
					$("#<?php echo $this->id . '-wrapper'; ?> .fesEm").fesLoadUtility();
				});
			</script>
		</div>		
	<?php
	}
}
?>