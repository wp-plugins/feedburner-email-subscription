<?php
/**
 * zFrame - Feedburner Email Subscription Widget
 * 
 * @package zFrame
 * @subpackage Classes 
 * Give your biggest fans another way to keep up with your content feed by placing an email subscription widget on your site.
 * This widget support shortcode for the short description.
 * For another improvement, you can drop email to zourbuth@gmail.com or visit http://zourbuth.com/feedburner-email-subscription-widget
**/

class Feedburner_Email_Subscription extends WP_Widget {

	/** Prefix for the widget. **/
	var $prefix = '';

	/** Textdomain for the widget. **/
	var $textdomain;

	/** Set up the widget's unique name, ID, class, description, and other options. **/
	function __construct() {

		/* load the widget stylesheet for the widgets screen. */
		add_action( 'load-widgets.php', array(&$this, 'zframe_fesw_style') );
		
		/* Set up the widget options. */
		$widget_options = array(
			'classname' => 'feedburner-email-subscription',
			'description' => esc_html__( '[+] Give your biggest fans another way to keep up with your content feed by placing an email subscription widget on your site.', $this->textdomain )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width' => 525,
			'height' => 350,
			'id_base' => "{$this->prefix}feedburner-email-subscription"
		);

		/* Create the widget. */
		$this->WP_Widget( "{$this->prefix}feedburner-email-subscription", esc_attr__( 'Feedburner Email Subscription', $this->textdomain ), $widget_options, $control_options );
		
		if ( is_active_widget(false, false, $this->id_base) && !is_admin() ) {
			wp_enqueue_style("feedburner-email-subscription", FEEDBURNER_EMAIL_SUBSCRIPTION_URL . "css/widget.css");
		}
	}

	/* Register the style or script, push the widget stylesheet widget.css into widget admin page*/
	function zframe_fesw_style() {
		wp_enqueue_style( 'zfesw-admin', FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'css/admin.css', false, 0.7, 'screen' );
	}
	
	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/** Set up the arguments **/
		$args = array(
			'feed_title'	=>	intval( $instance['feed_title'] ),
			'intro_text' 		=>	$instance['intro_text'],
			'outro_text' 		=>	$instance['intro_text'],
		); 
		
		echo $before_widget;
		
		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Print intro text if exist */
		if ( !empty( $instance['intro_text'] ) )
			echo '<p class="'. $this->id . '-intro-text">' . $instance['intro_text'] . '</p>';
		
		/* Display the feedburner. */
		proc_feedburner_email_subscription( $instance['feed_title'] );
		
		/* Print outro text if exist */
		if ( !empty( $instance['outro_text'] ) )
			echo '<p class="'. $this->id . '-outro_text">' . $instance['outro_text'] . '</p>';
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;
		$instance['feed_title'] = strip_tags( $new_instance['feed_title'] );
		$instance['intro_text'] = $new_instance['intro_text'];
		$instance['outro_text'] = $new_instance['outro_text'];
		
		return $instance;
	}	

	/** Displays the widget control options in the Widgets admin screen. **/
	function form( $instance ) {

		/** Set up the default form values. **/
		$defaults = array(
			'title' => '',
			'feed_title' => '',
			'intro_text' => '',
			'outro_text' => ''
		);

		/** Merge the user-selected arguments with the defaults. **/
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div class="zframe-widget-controls columns-2">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'feed_title' ); ?>"><?php _e( 'Your Feedburner Title', $this->textdomain ); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'feed_title' ); ?>" name="<?php echo $this->get_field_name( 'feed_title' ); ?>" value="<?php echo esc_attr( $instance['feed_title'] ); ?>" />
				<span class="controlDesc"><?php _e('Example: zourbuth', $this->textdomain ); ?></span>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'intro_text' ); ?>"><?php _e('Intro Text', $this->textdomain ) ?></label><br />
				<textarea name="<?php echo $this->get_field_name( 'intro_text' ); ?>" id="<?php echo $this->get_field_id( 'intro_text' ); ?>" rows="4" class="widefat"><?php echo htmlentities($instance['intro_text']); ?></textarea>
				<span class="controlDesc"><?php _e('This field support shortcodes and HTML.', $this->textdomain ); ?></span>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'outro_text' ); ?>"><?php _e('Outro Text', $this->textdomain ) ?></label><br />
				<textarea name="<?php echo $this->get_field_name( 'outro_text' ); ?>" id="<?php echo $this->get_field_id( 'outro_text' ); ?>" rows="4" class="widefat"><?php echo htmlentities($instance['outro_text']); ?></textarea>
				<span class="controlDesc"><?php _e('This field support shortcodes and HTML.', $this->textdomain ); ?></span>
			</p>			
			<p>
				Please give rating to <a href="http://wordpress.org/extend/plugins/feedburner-email-subscription-widget/">Feedburner Email Subscription Widget</a> and visit <a href="http://zourbuth.com/feedburner-email-subscription-widget/">zourbuth.com</a> for more informations.
				<?php _e( 'Like my work? Please consider to ', $this->textdomain ); ?><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W6D3WAJTVKAFC" title="Donate"><?php _e( 'donate', $this->textdomain ); ?></a>.<br /><br />			
			</p>
			
		</div>
		
		<div class="zframe-widget-controls columns-2 column-last">
			<?php
				require_once( FEEDBURNER_EMAIL_SUBSCRIPTION_DIR . 'envato-marketplaces-fes.php' );
				$envato = new Envato_Marketplaces_Fes();
				$envato->display_thumbs('zourbuth', 'codecanyon', 12);
			?>
			<p>
				<strong>Need Custom Code or Customization for lower cost?</strong><br />
				Please feel free to send mail to <a href="mailto:zourbuth@gmail.com">zourbuth@gmail.com</a>
			</p>
			<p>
				Subscribe to zourbuth by <a href="http://feedburner.google.com/fb/a/mailverify?uri=zourbuth&amp;loc=en_US">email</a><br />
				<small>&copy; Copyright <a href="http://zourbuth.com">zourbuth</a> 2012</small>.
			</p>
		</div>
		
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}
?>