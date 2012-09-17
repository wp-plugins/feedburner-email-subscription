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
function proc_feedburner_email_subscription( $feed_title ) {
	?>
		<form id="feedburner-zframefeedburner" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $feed_title; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">

		<p><input type="text" value="Your email here" onfocus="if(this.value=='Your email here')this.value='';" onblur="if(this.value=='')this.value='Your email here';" id="email-zframefeedburner" name="email">

		<input type="hidden" value="<?php echo $feed_title; ?>" name="uri"/>
		<input type="hidden" name="loc" value="en_US"/>
		<label>
			<input type="submit" id="imageField" name="imageField" value="Subscribe" alt="Submit">
		</label></p>
		</form>
	<?php
}

?>