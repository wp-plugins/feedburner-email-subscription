<?php
/**
	FeedBurner Email Subscription Widget

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
			'width' => 420,
			'height' => 350,
			'id_base' => $this->prefix
		);

		// Create the widget
		$this->WP_Widget( $this->prefix, __( 'FeedBurner Email Subscription', $this->textdomain ), $widget_options, $control_options );
		
		add_action('wp_ajax_fes_load_utility', array(&$this, 'fes_load_utility') );
				
		if ( is_active_widget( false, false, $this->id_base ) && ! is_admin() ) {
			add_action( 'wp_head', array( &$this, 'print_head' ), 2 );
			wp_enqueue_style( 'fes', FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'css/styles.css' );
		}
	}

	
	/**
	 * Print custom styles and scripts for the active widget
	 * @since 1.2.8
	 */	
	function print_head() {
		foreach ( $this->get_settings() as $k => $v ) 		
			if ( ! empty( $v['customstylescript'] ) )
				echo $v['customstylescript'];
	}

	
	/**
	 * Register the style or script, push the widget stylesheet widget.css into widget admin page
	 * @since 1.0
	 */	
	function load_widget() {
		wp_enqueue_style( "{$this->prefix}-dialog", FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'css/dialog.css' );
		wp_enqueue_script( "{$this->prefix}-dialog", FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'js/jquery.dialog.js', array( 'jquery' ) );
		wp_enqueue_script( "{$this->prefix}-dialog-custom", FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'js/jquery.custom.js', array( 'jquery' ) );
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

		$response = wp_remote_get( 'http://marketplace.envato.com/api/edge/collection:4204349.json' );
		$body = wp_remote_retrieve_body( $response );		
		
		if ( is_wp_error( $body ) )
			exit();
		
		$data = json_decode( $body );

		$html = '<p><strong>'. __( 'Our Premium Plugins', $this->textdomain ) .'</strong></p>';
		foreach( $data->{'collection'} as $key => $value )
			$html .= '<a href="'.$value->url.'?ref=zourbuth"><img src="'.$value->thumbnail.'"></a>&nbsp;';

		echo $html;
		exit();
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
		
		// Generate the form
		$args = wp_parse_args( (array) $instance, fes_default_arguments() ); 
		do_action( 'fes_form_template_' . $args['template'], $args );
		
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
		$instance['feed_title'] 		= strip_tags( $new_instance['feed_title'] );
		$instance['text'] 				= strip_tags( $new_instance['text'] );
		$instance['submit'] 			= strip_tags( $new_instance['submit'] );
		$instance['template'] 			= strip_tags( $new_instance['template'] );
		$instance['posts_feed_link']	= isset( $new_instance['posts_feed_link'] ) ? 1 : 0;
		$instance['comments_feed_link']	= isset( $new_instance['comments_feed_link'] ) ? 1 : 0;
		$instance['remove_css']			= isset( $new_instance['remove_css'] ) ? 1 : 0;
		$instance['bootstrap_3']		= isset( $new_instance['bootstrap_3'] ) ? 1 : 0;
		$instance['intro_text'] 		= $new_instance['intro_text'];
		$instance['outro_text'] 		= $new_instance['outro_text'];
		$instance['customstylescript'] 	= $new_instance['customstylescript'];
		$instance['toggle_active'] 		= $new_instance['toggle_active'];
		
		return $instance;
	}	

	
	/**
	 * Displays the widget control options in the Widgets admin screen
	 * @since 1.0
	 */		
	function form( $instance ) {

		$tabs = array( 
			__( 'General', $this->textdomain ),  
			__( 'Template', $this->textdomain ),
			__( 'Advanced', $this->textdomain ),
			__( 'Supports', $this->textdomain )
		);

		$_form_open = array(
			'popup'		=> __( 'Pop up window', $this->textdomain ),
			'new'		=> __( 'Open in new window', $this->textdomain ),
			'current'	=> __( 'Open in current window', $this->textdomain )
		); 

		$_templates = apply_filters( 'fes_templates', array(
			'default'	=> __( 'Default', $this->textdomain )
		)); 

		// Merge the user-selected arguments with the defaults
		$instance = wp_parse_args( (array) $instance, fes_default_arguments() ); 
		
		?>

		<div class="pluginName">FeedBurner Email Subscription<span class="pluginVersion"><?php echo FEEDBURNER_EMAIL_SUBSCRIPTION_VERSION; ?></span></div>
		<div id="cp-<?php echo $this->id ; ?>" class="totalControls tabbable tabs-left">
			<ul class="nav nav-tabs">
				<?php foreach ($tabs as $key => $tab ) : ?>
					<li class="fes-<?php echo $key; ?> <?php echo $instance['toggle_active'][$key] ? 'active' : '' ; ?>"><?php echo $tab; ?><input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo $instance['toggle_active'][$key]; ?>" /></li>
				<?php endforeach; ?>							
			</ul>
			
			<ul class="tab-content" style="min-height:350px;">
				<li class="tab-pane <?php if ( $instance['toggle_active'][0] ) : ?>active<?php endif; ?>">
					<ul>			
						<li>
							<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label>					
							<span class="controlDesc"><?php _e('Give the widget a title, leave empty for no title.', $this->textdomain ); ?></span>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'feed_title' ); ?>"><?php _e( 'Feed Address', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e('Copy and paste your Feedburner feed here.', $this->textdomain ); ?></span>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'feed_title' ); ?>" name="<?php echo $this->get_field_name( 'feed_title' ); ?>" value="<?php echo esc_attr( $instance['feed_title'] ); ?>" />							
							<span class="controlDesc"><?php _e('Example: <strong>zourbuth</strong> for <a target="_blank" href="http://feeds.feedburner.com/zourbuth">http://feeds.feedburner.com/zourbuth</a> feed address.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Input Text', $this->textdomain ); ?></label>					
							<span class="controlDesc"><?php _e('The email input text.', $this->textdomain ); ?></span>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" value="<?php echo esc_attr( $instance['text'] ); ?>" />							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'submit' ); ?>"><?php _e( 'Submit Button Text', $this->textdomain ); ?></label>			
							<span class="controlDesc"><?php _e('The form submit button text.', $this->textdomain ); ?></span>							
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="<?php echo esc_attr( $instance['submit'] ); ?>" />							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'posts_feed_link' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['posts_feed_link'], true ); ?> id="<?php echo $this->get_field_id( 'posts_feed_link' ); ?>" name="<?php echo $this->get_field_name( 'posts_feed_link' ); ?>" />&nbsp;<?php _e( 'Display posts feed link', $this->textdomain ); ?></label>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'comments_feed_link' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['comments_feed_link'], true ); ?> id="<?php echo $this->get_field_id( 'comments_feed_link' ); ?>" name="<?php echo $this->get_field_name( 'comments_feed_link' ); ?>" />&nbsp;<?php _e( 'Display comments feed link', $this->textdomain ); ?></label>
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][1] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template', $this->textdomain ); ?></label>		
							<span class="controlDesc"><?php _e( 'The subscription form template. Some templates may override this widget settings.', $this->textdomain ); ?></span>						
							<select id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>">
								<?php foreach ( $_templates as $k => $val ) { ?>
									<option value="<?php echo $k; ?>" <?php selected( $instance['template'], $k ); ?>><?php echo $val; ?></option>
								<?php } ?>
							</select>
						</li>						
						<li>
							<label for="<?php echo $this->get_field_id( 'form_open' ); ?>"><?php _e( 'Form Open', $this->textdomain); ?></label>
							<span class="controlDesc"><?php _e( 'Select the form open methode while submitting.', $this->textdomain ); ?></span>
							<select id="<?php echo $this->get_field_id( 'form_open' ); ?>" name="<?php echo $this->get_field_name( 'form_open' ); ?>">
								<?php foreach ( $_form_open as $k => $val ) { ?>
									<option value="<?php echo $k; ?>" <?php selected( $instance['form_open'], $k ); ?>><?php echo $val; ?></option>
								<?php } ?>
							</select>
						</li>						
						<li>
							<label for="<?php echo $this->get_field_id( 'remove_css' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['remove_css'], true ); ?> id="<?php echo $this->get_field_id( 'remove_css' ); ?>" name="<?php echo $this->get_field_name( 'remove_css' ); ?>" />&nbsp;<?php _e( 'Remove plugin CSS?', $this->textdomain ); ?></label>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'bootstrap_3' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['bootstrap_3'], true ); ?> id="<?php echo $this->get_field_id( 'bootstrap_3' ); ?>" name="<?php echo $this->get_field_name( 'bootstrap_3' ); ?>" />&nbsp;<?php _e( 'Use Twitter Bootstrap 3?', $this->textdomain ); ?></label>
						</li>						
					</ul>
				</li>
				
				<li class="tab-pane <?php if ( $instance['toggle_active'][2] ) : ?>active<?php endif; ?>">
					<ul>				
						<li>
							<label for="<?php echo $this->get_field_id( 'intro_text' ); ?>"><?php _e('Intro Text', $this->textdomain ) ?></label>
							<span class="controlDesc"><?php _e('This field support shortcodes and HTML.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'intro_text' ); ?>" id="<?php echo $this->get_field_id( 'intro_text' ); ?>" rows="2" class="widefat"><?php echo htmlentities($instance['intro_text']); ?></textarea>							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'outro_text' ); ?>"><?php _e('Outro Text', $this->textdomain ) ?></label>
							<span class="controlDesc"><?php _e('This field support shortcodes and HTML.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'outro_text' ); ?>" id="<?php echo $this->get_field_id( 'outro_text' ); ?>" rows="2" class="widefat"><?php echo htmlentities($instance['outro_text']); ?></textarea>							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('customstylescript'); ?>"><?php _e( 'Custom Script & Stylesheet', $this->textdomain ) ; ?></label>
							<span class="controlDesc"><?php _e( 'Use this box for additional widget CSS style of custom javascript. This widget selector is: ', $this->textdomain ); ?><?php echo '<tt>#' . $this->id . '</tt>'; ?></span>
							<textarea name="<?php echo $this->get_field_name( 'customstylescript' ); ?>" id="<?php echo $this->get_field_id( 'customstylescript' ); ?>" rows="3" class="widefat code"><?php echo htmlentities($instance['customstylescript']); ?></textarea>
						</li>						
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][3] ) : ?>active<?php endif; ?>">
					<ul>
						<li>							
							<div class="fesem"></div>
						</li>
						<li>
							<strong>Need Custom Code or Customization for lower cost?</strong>&nbsp;
							Please feel free to send mail to <a href="mailto:zourbuth@gmail.com">zourbuth@gmail.com</a>
						</li>
						<li>
							Please give rating to <a href="http://wordpress.org/extend/plugins/feedburner-email-subscription/">FeedBurner Email Subscription Widget</a> and visit <a href="http://zourbuth.com/archives/498/feedburner-email-subscription-wordpress-plugin/">zourbuth.com</a> for more informations.
						</li>						
						<li>
							<?php _e( 'Like my work? Please consider to ', $this->textdomain ); ?><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W6D3WAJTVKAFC" title="Donate"><?php _e( 'donate', $this->textdomain ); ?></a>.	
						</li>
						<li>
							<p style="margin-bottom: 5px;"><a href="javascript: void(0)"><strong>Tweet to Get Supports</strong></a></p>
							<a href="https://twitter.com/intent/tweet?screen_name=zourbuth" class="twitter-mention-button" data-related="zourbuth">Tweet to @zourbuth</a>
							<a href="https://twitter.com/zourbuth" class="twitter-follow-button" data-show-count="false">Follow @zourbuth</a>									
						</li>
						<li>
							<span class="controlDesc"><?php _e( 'Help us to share this plugin.', $this->textdomain ); ?></span>
							<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://wordpress.org/plugins/feedburner-email-subscription/" data-text="Check out this WordPress plugin 'FeedBurner Email Subscription'">Tweet</a>
							
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
							<script>if( typeof(twttr) !== 'undefined' ) twttr.widgets.load()</script>								
						</li>						
						<li>
							Subscribe to zourbuth by <a href="http://feedburner.google.com/fb/a/mailverify?uri=zourbuth&amp;loc=en_US">email</a><br />
							<small>&copy; Copyright <a href="http://zourbuth.com">zourbuth</a> <?php echo date('Y'); ?></small>.
						</li>
					</ul>
				</li>
			</ul>
		</div>
		<script type='text/javascript'>
			jQuery(document).ready(function($){
				$(document).on( "click", ".fes-3", function(){
					$(this).fesLoadUtility();
				});	
			});
		</script>	
	<?php
	}
}
?>