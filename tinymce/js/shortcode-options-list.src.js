/**
 * Handles JavaScript for the shortcode editor window
 * This is just the source (developer) version of this document. The minified version
 * 		of the document is actually used by the plugin
 * @see shortcode-options-list.js
 * @package WordPress
 * @subpackage UMW Widget Shortcodes
 * @version 0.2
 */
var umwWSDialog = {
	umwWSobj : {},
	init : function() {
		var f = document.forms[0], ed = tinyMCEPopup.editor, e, b;
		
		this.process_shortcode();
		
		var tmp = ( 'widget_id' in this.umwWSobj ) ? this.umwWSobj.widget_id : '';
		if( typeof( f.widget_id ) !== 'undefined' ) {
			for( var i=0; i<f.widget_id.length; i++ ) {
				if( f.widget_id[i].value == tmp ) {
					f.widget_id[i].checked = true;
				}
			}
		}
		
		var tmp = ( 'show_title' in this.umwWSobj ) ? this.umwWSobj.show_title : 0;
		if( typeof( f.show_title ) !== 'undefined' ) {
			if( this.umwWSobj.show_title == 1 || this.umwWSobj.show_title == 'true' ) {
				f.show_title.checked = true;
			}
		}
	},
	update : function() {
		var f = document.forms[0], ed = tinyMCEPopup.editor, e, b;

		tinyMCEPopup.restoreSelection();
		e = ed.dom.getParent(ed.selection.getNode(), 'P');
		
		var widget_id = '';
		if( typeof( f.widget_id ) !== 'undefined' ) {
			for( var i=0; i < f.widget_id.length; i++ ) {
				if( f.widget_id[i].checked ) {
					widget_id = f.widget_id[i].value;
				}
			}
		}
		
		var show_title = 0;
		if( typeof( f.show_title ) !== 'undefined' ) {
			if( f.show_title.checked ) {
				show_title = 1;
			}
		}
		
		tinyMCEPopup.execCommand("mceBeginUndoLevel");

		if( this.umwWSobj !== 'undefined' && 'widget_id' in this.umwWSobj ) {
			// Replace the old shortcode with the the new
			var findwhat = '[umw_widget id="' + this.umwWSobj.widget_id + '"' + ( ( 'show_title' in this.umwWSobj ) ? ' title=' + this.umwWSobj.show_title : '') + ']';
			
			var rep = '[umw_widget id="' + widget_id + '"' + ( ( show_title == 1 ) ? ' title=1' : '') + ']';
			
			var tmpCont = ed.getContent().replace(findwhat,rep);
			ed.setContent(tmpCont);
		}
		else {
			ed.execCommand('mceInsertContent',false,'[umw_widget id="' + widget_id + '"' + ( ( show_title == 1 ) ? ' title=1' : '') + ']');
		}
		
		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
	},
	process_shortcode : function() {
		var f = document.forms[0], ed = tinyMCEPopup.editor;

		if ( e = ed.dom.getParent( ed.selection.getNode(), 'P' ) ) {
			if( ( umwwspos = e.innerHTML.indexOf( '[umw_widget' ) ) != -1 ) {
				console.log( 'Preparing to parse the shortcode ' + e.innerHTML );
				var umwwstext = e.innerHTML.substr(umwwspos);
				umwwstext = umwwstext.substr(0,(umwwstext.indexOf(']')));
				umwwstext = umwwstext.split(' ');
				console.log( umwwstext );
				var umwwsTmp = [];
				for( var i=0; i<umwwstext.length; i++ ) {
					var tmptext = umwwstext[i];
					if( tmptext.indexOf('=') == -1 ) {
						if( i > 0 ) {
							tmptext = umwwsTmp.pop() + ' ' + umwwstext[i];
						}
					}
					umwwsTmp.push( tmptext );
				}
				umwwstext = umwwsTmp; umwwsTmp = null;
				umwwstext.shift();
				for(var i=0; i<umwwstext.length; i++) {
					var umwwsTmp = umwwstext[i].split('=');
					var umwwsKey = umwwsTmp.shift();
					console.log( 'Preparing to look at the value of ' + umwwsKey );
					if( umwwsKey == 'id' ) {
						umwwsKey = 'widget_id';
					} else if( umwwsKey == 'title' ) {
						umwwsKey = 'show_title';
					}
					var umwwsVal = umwwsTmp.join('=');
					if(umwwsVal.indexOf('"') == 0) {
						umwwsVal = umwwsVal.substr(1,(umwwsVal.length - 2));
					}
					this.umwWSobj[umwwsKey] = umwwsVal;
					console.log( 'We just set the value of ' + umwwsKey + ' to ' + umwwsVal );
				}
			}
		}
	}
}
tinyMCEPopup.onInit.add(umwWSDialog.init, umwWSDialog);
