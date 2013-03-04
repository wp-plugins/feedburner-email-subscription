<?php
/**
 * Feedburner Email Subscription
 * 
 * @package Zourbuth
 * @subpackage Function 
 * Give your biggest fans another way to keep up with your content feed by placing an email subscription widget on your site.
 * This widget support shortcode for the short description.
 * For another improvement, you can drop email to zourbuth@gmail.com or visit http://zourbuth.com/feedburner-email-subscription
**/

	
/**
 * Outputs the widget based on the arguments input through the widget controls.
 * @since 0.6.0
 */
function proc_feedburner_email_subscription( $args ) { 
	// Set up the default form values
	$defaults = array(
		'feed'		=> '',
		'text'		=> __( 'Your email here', 'feedburner-email-subscription' ),
		'submit'	=> __( 'Subscribe', 'feedburner-email-subscription' ),
	);

	// Merge the user-selected arguments with the defaults
	$args = wp_parse_args( (array) $args, $defaults );
	extract( $args );
	?>
	<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $feed; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
		<p>
			<input type="text" value="<?php echo $text; ?>" onfocus="if(this.value=='<?php echo $text; ?>')this.value='';" onblur="if(this.value=='')this.value='<?php echo $text; ?>';" name="email">
			<input type="submit" name="imageField" value="<?php echo $submit; ?>" alt="Submit" />
			<input type="hidden" value="<?php echo $feed; ?>" name="uri"/>
			<input type="hidden" name="loc" value="en_US" />			
		</p>
	</form>
	<?php
}

?>