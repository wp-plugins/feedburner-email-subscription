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

	$html  = '';
	$html .= "<form action='http://feedburner.google.com/fb/a/mailverify' method='post' target='popupwindow' onsubmit='window.open('http://feedburner.google.com/fb/a/mailverify?uri=$feed', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true'>
		<p>
			<input type='text' value='$text' onfocus='if(this.value=='$text')this.value='';' onblur='if(this.value=='')this.value='$text';' name='email'>
			<input class='btn' type='submit' name='imageField' value='$submit' alt='Submit' />
			<input type='hidden' value='$feed' name='uri'/>
			<input type='hidden' name='loc' value='en_US' />			
		</p>
	</form>";

	if ( !current_theme_supports('automatic-feed-links') )
		return $html;

	$rss = array(
		/* translators: Separator between blog name and feed type in feed links */
		'separator'	=> _x('&raquo;', 'feed link'),
		/* translators: 1: blog title, 2: separator (raquo) */
		'feedtitle'	=> __('%1$s %2$s Feed'),
		/* translators: %s: blog title, 2: separator (raquo) */
		'comstitle'	=> __('%1$s %2$s Comments Feed'),
	);

	if( isset( $args['posts_feed_link'] ) )
		$html .= '<a class="feed-link" title="' . esc_attr(sprintf( $rss['feedtitle'], get_bloginfo('name'), $rss['separator'] )) . '" href="' . get_feed_link() . '"><img alt="i" src="' . FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'images/rss.png" />Posts <abbr title="Really Simple Syndication">RSS</abbr></a>';
	
	if( isset( $args['comments_feed_link'] ) )
		$html .= '<a class="feed-link" title="' . esc_attr(sprintf( $rss['comstitle'], get_bloginfo('name'), $rss['separator'] )) . '" href="' . get_feed_link( 'comments_' . get_default_feed() ) . '"><img alt="i" src="' . FEEDBURNER_EMAIL_SUBSCRIPTION_URL . 'images/rss.png" />Comments <abbr title="Really Simple Syndication">RSS</abbr></a>';

	return $html;
}

?>