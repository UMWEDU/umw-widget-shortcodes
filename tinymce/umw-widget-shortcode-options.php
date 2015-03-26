<?php
/**
 * Displays the administration window for the Visual Editor plugin
 * @package WordPress
 * @subpackage UMW Widget Shortcodes
 * @version 0.1.10
 * @author cgrymala
 */

if( !defined( 'ABSPATH' ) )
	/** @ignore */
	require_once( realpath( '../../../../' ) . '/wp-load.php' );

global $wp_registered_widgets;
$widgets = wp_get_sidebars_widgets();
if( !array_key_exists( 'umw-widget-shortcodes', $widgets ) )
	wp_die( 'The Widget Shortcodes sidebar does not appear to exist. Please ensure that this plugin is installed and activated properly.', 'Fatal Error' );

$widgets = $widgets['umw-widget-shortcodes'];

header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title>
<?php _e('Insert Widget Shortcode') ?>
</title>
<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js?ver=3223"></script>
<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-includes/js/tinymce/utils/mctabs.js?ver=327-1235"></script>
<?php
wp_admin_css( 'wp-admin', true );
?>
<script type="text/javascript" src="js/shortcode-options-list.src.js"></script>
</head>
<body id="umwWS_dialog">
<form action="#" onsubmit="umwWSDialog.update(); return false;">
<div class="panel_wrapper">
	<div id="general_panel" class="panel current" style="overflow-y: auto;">
		<fieldset>
			<legend><?php _e( 'Which widget do you want to display?' ) ?></legend>
			<ul class="mce_list">
<?php
	$wct = 0;
	foreach( $widgets as $wid ) {
		$widget_info = $wp_registered_widgets[$wid];
		/*print( "\n<!-- Widget Info:\n" );
		var_dump( $widget_info );
		print( "\n-->\n" );*/
		
		$widget_title = '';
		
		if ( array_key_exists( 'callback', $widget_info ) && is_object( $widget_info['callback'][0] ) ) {
			$cbobj = $widget_info['callback'][0];
			$widget_instance_id = ( array_key_exists( 'params', $widget_info ) && isset( $widget_info['params'][0] ) && isset( $widget_info['params'][0]['number'] ) ) ? $widget_info['params'][0]['number'] : false;
			if ( false !== $widget_instance_id ) {
				$widget_options = $cbobj->get_settings();
				/*print( "\n<!-- Widget options:\n" );
				var_dump( $widget_options );
				print( "\n-->\n" );*/
				if ( isset( $widget_options[$widget_instance_id] ) && array_key_exists( 'title', $widget_options[$widget_instance_id] ) ) {
					$widget_title = sprintf( ' <strong>%s</strong>', $widget_options[$widget_instance_id]['title'] );
				}
			}
		}
?>
				<li class="<?php echo $wct%2 ? 'odd' : 'even' ?>"><input type="radio" name="widget_id" id="widget_id_<?php echo $wid ?>" value="<?php echo $wid ?>" /> <label for="widget_id_<?php echo $wid ?>"><?php printf( '[%1$s]%2$s <em>(%3$s)</em>', $widget_info['name'], $widget_title, $wid ) ?></label><br class="clear" /></li>
<?php
		$wct++;
	}
?>
			</ul>
			<br style="clear: both;"/>
		</fieldset>
		<fieldset>
			<legend><?php _e( 'Would you like to show the widget title?' ) ?></legend>
            <input type="checkbox" name="show_title" id="show_title" value="1"/>
            <label for="show_title"><?php _e( 'Yes' ) ?>
		</fieldset>
	</div>
</div>
<div class="mceActionPanel" style="padding-bottom: 10px;">
  <div style="float: left; padding-left: 10px;">
    <input type="button" id="cancel" name="cancel" value="<?php _e('Cancel'); ?>" title="<?php _e('Cancel'); ?>" onclick="tinyMCEPopup.close();" />
  </div>
  <div style="float: right; padding-right: 10px;">
    <input type="submit" name="insert" id="insert" value="<?php _e('Insert'); ?>" title="<?php _e('Insert calendar shortcode'); ?>"/>
  </div>
  <br style="clear: both;" />
</div>
</form>
</body>
</html>