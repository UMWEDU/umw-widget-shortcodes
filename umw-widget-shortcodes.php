<?php
/*
Plugin Name: UMW Widget Shortcodes
Plugin URI: http://www.umw.edu/
Description: Allows any modern WordPress widget to be used via shortcode within a post or theme file
Version: 0.2
Author: Curtiss Grymala
Author URI: http://umw.edu/
License: GPL2

Widget button used under the CC Attribution-Share Alike license (http://creativecommons.org/licenses/by-sa/2.5/deed.en)
Widget button initially retrieved from http://en.wikipedia.org/wiki/File:Widget_icon.png
*/

add_action( 'plugins_loaded', 'init_umw_widget_shortcodes' );
function init_umw_widget_shortcodes() {
	if( !class_exists( 'umw_widget_shortcodes' ) ) {
		/**
		 * Make sure the class is defined before we try to invoke it
		 */
		if( file_exists( dirname( __FILE__ ) . '/classes/class-umw-widget-shortcodes.php' ) )
			require_once( dirname( __FILE__ ) . '/classes/class-umw-widget-shortcodes.php' );
		elseif( file_exists( dirname( __FILE__ ) . '/umw-widget-shortcodes/classes/class-umw-widget-shortcodes.php' ) )
			require_once( dirname( __FILE__ ) . '/umw-widget-shortcodes/classes/class-umw-widget-shortcodes.php' );
		else
			return;
	}
	
	global $umwWidgetShortcodes;
	$umwWidgetShortcodes = new umw_widget_shortcodes();
}
?>