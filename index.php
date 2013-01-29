<?php
 /*
	Plugin Name: Feedburner Email Subscription
	Plugin URI: http://zourbuth.com/archives/498/feedburner-email-subscription-wordpress-plugin/
	Description: Give your biggest fans another way to keep up with your content feed by placing a <a href="http://www.feedburner.com/" alt="Feedburner" title="Feedburner">Feedburner</a> email subscription widget on your site. This widget will follow your theme stylesheet.
	Version: 1.2
	Author: zourbuth
	Author URI: http://zourbuth.com
	License: Under GPL2
*/
 
/*  
	Copyright 2011 zourbuth (email : zourbuth@gmail.com)

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

/* Launch the plugin. */
add_action( 'plugins_loaded', 'zframe_feedburner_email_subscription_plugins_loaded' );

/* Initializes the plugin and it's features. */
function zframe_feedburner_email_subscription_plugins_loaded() {

	/* Set constant path to the members plugin directory. */
	define( 'FEEDBURNER_EMAIL_SUBSCRIPTION_DIR', plugin_dir_path( __FILE__ ) );

	/* Set constant path to the members plugin directory. */
	define( 'FEEDBURNER_EMAIL_SUBSCRIPTION_URL', plugin_dir_url( __FILE__ ) );

	/* Loads and registers the new widgets. */
	add_action( 'widgets_init', 'zframe_feedburner_email_subscription_init' );

}


/* Register the extra widgets. Each widget is meant to replace or extend the current default  */
function zframe_feedburner_email_subscription_init() {

	/* Load plugin file. */
	require_once( FEEDBURNER_EMAIL_SUBSCRIPTION_DIR . 'feedburner-email-subscription.php' );
	require_once( FEEDBURNER_EMAIL_SUBSCRIPTION_DIR . 'feedburner-email-subscription-shortcode.php' );
	require_once( FEEDBURNER_EMAIL_SUBSCRIPTION_DIR . 'feedburner-email-subscription-widget.php' );

	/* Register widget. */
	register_widget( 'Feedburner_Email_Subscription' );
}

?>