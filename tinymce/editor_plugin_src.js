/**
 * editor_plugin_src.js
 *
 * Copyright 2010, University of Mary Washington
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 *
 * Implements the TinyMCE plugin for the Widget shortcode insertion
 * This is just a source file (for development purposes). The minified version of this file
 * 		is the one that is actually used by the plugin
 * @see editor_plugin.js
 * @package WordPress
 * @subpackage UMW Widget Shortcodes
 * @version 0.2
 */

(function() {
	tinymce.create('tinymce.plugins.umw_widget_short_Plugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceUMWws', function() {
				ed.windowManager.open({
					file : url + '/umw-widget-shortcode-options.php',
					width : 570 + parseInt(ed.getLang('example.delta_width', 0)),
					height : 570 + parseInt(ed.getLang('example.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register example button
			ed.addButton('umw_widget_short', {
				title : 'Insert a widget',
				cmd : 'mceUMWws'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('umwws', n.nodeName == 'IMG');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'UMW Widget Shortcodes plugin',
				author : 'Curtiss Grymala',
				authorurl : 'http://umw.edu/',
				infourl : 'http://umw.edu/',
				version : "0.1.10"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('umw_widget_short', tinymce.plugins.umw_widget_short_Plugin);
})();