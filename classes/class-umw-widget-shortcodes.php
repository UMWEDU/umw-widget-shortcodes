<?php
/**
 * Class definitions and methods for the umw-widget-shortcodes WordPress plugin
 */
if ( ! class_exists( 'umw_widget_shortcodes' ) ) {
	/**
	 * Define the umw_widget_shortcodes class
	 */
	class umw_widget_shortcodes {
		
		/**
		 * Set up the default arguments for the shortcodes sidebar
		 */
		var $default_args = array( 
			'name'			=> 'Shortcodes Widget Area',
			'id'            => 'umw-widget-shortcodes',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>' 
		);
		
		/**
		 * Create the umw_widget_shortcodes object
		 * @uses add_action() to register the shortcodes sidebar
		 * @uses add_shortcode() to register the umw_widget shortcode
		 */
		function __construct() {
			add_action( 'admin_init', array( &$this, 'register_sidebar' ) ); 
			add_shortcode( 'umw_widget', array( &$this, 'do_widget' ) );
			
			if ( ! class_exists( 'WP_Widget' ) )
				require_once( ABSPATH . WPINC . '/widgets.php' );
			
			// init process for MCS button control
			$this->add_buttons();
			add_action( 'admin_print_styles', array( $this, 'print_admin_styles' ) );
		}
		
		/**
		 * Register the sidebar
		 * @uses umw_widget_shortcodes::$default_args
		 */
		function register_sidebar() {
			if ( function_exists('register_sidebar') ) {
				register_sidebar( $this->default_args ); 
			}
		}
		
		/**
		 * Render the selected widget via the shortcodes
		 */
		function do_widget( $atts ) {
			global $wp_registered_widgets;
			if ( ! isset( $atts['title'] ) )
				$atts['title'] = false;
			
			$title = $atts['title'] === 'true' || $atts['title'] === 1 || $atts['title'] === '1' ? true : false;
			
			extract( shortcode_atts( array(
				'sidebar' => 'Shortcodes',
				'id' => '',
			), $atts ) );
			
			if ( empty( $id ) || ! array_key_exists( $id, $wp_registered_widgets ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
					error_log( '[UMW Widget Shortcodes] The ID appears to be empty' );
				return false;
			}
			
			$widget = $wp_registered_widgets[$id];
			
			if ( ! array_key_exists( 'callback_wl_redirect', $widget ) && ! array_key_exists( 'callback', $widget ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
					error_log( '[UMW Widget Shortcodes] The callback does not seem to exist in the widget' );
				return false;
			}
			
			$output = '';
			
			if ( array_key_exists( 'callback', $widget ) && is_array( $widget['callback'] ) && is_object( $widget['callback'][0] ) && method_exists( $widget['callback'][0], $widget['callback'][1] ) ) {
				$callback = $widget['callback'];
			} else if ( array_key_exists( 'callback_wl_redirect', $widget ) && is_array( $widget['callback_wl_redirect'] ) && is_object( $widget['callback_wl_redirect'][0] ) && method_exists( $widget['callback_wl_redirect'][0], $widget['callback_wl_redirect'][1] ) ) {
				$callback = $widget['callback_wl_redirect'];
			} else if ( array_key_exists( 'callback', $widget ) && ! is_array( $widget['callback'] ) && function_exists( $widget['callback'] ) ) {
				$callback = $widget['callback'];
			} else if ( array_key_exists( 'callback_wl_redirect', $widget ) && ! is_array( $widget['callback_wl_redirect'] ) && function_exists( $widget['callback_wl_redirect'] ) ) {
				$callback = $widget['callback_wl_redirect'];
			} else {
				$callback = new WP_Error( 'deprecated-widget', sprintf( __( 'The widget with an ID of %s uses deprecated functions to display the widget. Therefore, it was not rendered by the UMW Widget Shortcodes plugin.' ), $id ) );
			}
			
			if ( ! is_wp_error( $callback ) ) {
				
				if ( array_key_exists( 'params', $widget ) && is_array( $widget['params'] ) && array_key_exists( 'number', $widget['params'][0] ) ) {
					$widget_id = $widget['params'][0]['number'];
				} else {
					$widget_id = explode( '-', $id );
					$widget_id = array_pop( $widget_id );
				}
				
				if ( ! is_numeric( $widget_id ) ) {
					$widget_id = 1;
				}
				
				$args = $this->default_args;
				if ( ! $title )
					$args['before_title'] = str_replace( '>', ' style="display: none">', $args['before_title'] );
				
				/**
				 * Copied from wp-includes/widgets.php dynamic_sidebar() function
				 */
				$classname_ = '';
				foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
					if ( is_string($cn) )
						$classname_ .= '_' . $cn;
					elseif ( is_object($cn) )
						$classname_ .= '_' . get_class($cn);
				}
				$classname_ = ltrim($classname_, '_');
				if ( ! empty($args['before_widget'] ) ) 
					$args['before_widget'] = sprintf($args['before_widget'], $id, $classname_);
				else $args['before_widget'] = '';
				if ( empty( $args['after_widget'] ) ) $args['after_widget'] = '';
				/* dynamic_sidebar() copy */
				
				ob_start();
				if ( is_array( $callback ) )
					call_user_method( $callback[1], $callback[0], $args, $widget_id );
				else
					call_user_func( $callback, $args, $widget_id );
				$output .= ob_get_clean();
			} else {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
					error_log( '[UMW Widget Shortcodes]: ' . $callback->get_error_message() );
				$output .= "\n<!-- " . $callback->get_error_message() . " -->\n";
			}
			
			return $output;
		}
		
		/**
		 * Add some CSS to style our TinyMCE button
		 */
		function print_admin_styles() {
			print '<style type="text/css" id="umw-widget-short-icon-styles">.wp_themeSkin span.mce_umw_widget_short { background: url(' . plugins_url( '/tinymce/img/widget-icon.png', dirname( __FILE__ ) ) . ') no-repeat 0 -20px; } .wp_themeSkin span.mce_umw_widget_short:hover { background-position: 0 0; }</style>'; 
			return;
		}
		
		/**
		 * Add a new button to the TinyMCE visual editor
		 */
		function add_buttons() {
		   // Don't bother doing this stuff if the current user lacks permissions
		   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			 return;
		 
		   // Add only in Rich Editor mode
		   if ( get_user_option('rich_editing') == 'true') {
			 add_filter('mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			 add_filter('mce_buttons_2', array( $this, 'register_button' ) );
		   }
		}
		
		/**
		 * Register the TinyMCE plugin button
		 */
		function register_button($buttons) {
			/*print( "\n<!-- Preparing to add the umw_widget_short MCE button. -->\n" );
			print( "\n<!-- The current buttons array looks like:\n" . print_r( $buttons, true ) . "\n-->\n" );*/
			array_push($buttons, 'umw_widget_short');
			/*print( "\n<!-- The updated buttons array looks like:\n" . print_r( $buttons, true ) . "\n-->\n" );*/
			return $buttons;
		}
		
		/**
		 * Load the TinyMCE plugin : editor_plugin.js (wp2.5)
		 */
		function add_tinymce_plugin($plugin_array) {
			/*print( "\n<!-- Preparing to add the umw_widget_short MCE plugin. -->\n" );
			print( "\n<!-- The current plugin array looks like:\n" . print_r( $plugin_array, true ) . "\n-->\n" );*/
			$plugin_array['umw_widget_short'] = plugins_url( '/tinymce/editor_plugin_src.js', dirname( __FILE__ ) );
			/*print( "\n<!-- The updated plugin array looks like:\n" . print_r( $plugin_array, true ) . "\n-->\n" );*/
			return $plugin_array;
		}
	 
	}
}
?>