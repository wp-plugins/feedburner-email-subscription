<?php 
/* 
	Feedburner Email Subscription Template
	@since 1.3.4

	Copyright 2015 zourbuth (email : zourbuth@gmail.com)

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


class Feedburner_Email_Subscription_Template {

	// Setup class vars
	var $prefix;
	var $textdomain;

	
	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.3.4
	 */		
	function __construct() {
		$this->textdomain = FEEDBURNER_EMAIL_SUBSCRIPTION_TEXTDOMAIN;
		add_action( 'fes_form_template_default', array( &$this, 'template_default' ) );
	}
	
	
	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.3.4
	 */				
	public static function form_attributes( $args ) {
		$atts = array();
		$atts['class'] = 'fes fes-' . $args['template'];
		$atts['action'] = 'http://feedburner.google.com/fb/a/mailverify';
		$atts['method'] = 'post';

		switch ( $args['form_open'] ) {
			case 'popup':
				$atts['target'] = 'popupwindow';
				$atts['onsubmit'] = 'window.open( \'http://feedburner.google.com/fb/a/mailverify?uri='. $args['feed_title'] . '\', \'popupwindow\', \'scrollbars=yes,width=550,height=520\');return true';
				break;
			
			case 'new':
				$atts['target'] = '_blank';
				break;
			
			case 'current':
				break;			
		}

		$attributes = '';
		foreach( apply_filters ( 'fes_form_attributes', $atts, $args ) as $k => $v )
			$attributes .= $k.'="'.$v.'" ';

		return $attributes;
	}
	
	
	/**
	 * Default Template
	 * @since 1.3.4
	 */		
	function template_default( $args ) {	
		echo '<form '. $this->form_attributes( $args ) .'>';
		
			$onfocus = 'if(this.value==\''. $args['text'] .'\')this.value=\'\';" onblur="if(this.value==\'\')this.value=\''. $args['text'] .'\'';
			
			if( isset( $args['bootstrap_3'] ) ) :				
				echo '<div class="form-group">';
					echo '<label class="sr-only"><span class="screen-reader-text">'. __( 'Email Subscription', $this->textdomain ) .'</span></label>';
					echo '<input class="form-control search-field" type="text" value="'. $args['text'] .'" onfocus="'. $onfocus .'" name="email" />';
				echo '</div>';
				echo '<button type="submit" class="btn btn-default btn-submit">'. $args['submit'] .'</button>';		
			else :
				echo '<p>';
					echo '<input type="text" value="'. $args['text'] .'" onfocus="'. $onfocus .'" name="email" />';
					echo '<input class="btn" type="submit" value="'. $args['submit'] .'" />';
				echo '</p>';
			endif;
			
			echo '<input type="hidden" value="'. $args['feed_title'] .'" name="uri" />';
			echo '<input type="hidden" name="loc" value="en_US" />';		
		echo '</form>';
	}
	
} new Feedburner_Email_Subscription_Template();