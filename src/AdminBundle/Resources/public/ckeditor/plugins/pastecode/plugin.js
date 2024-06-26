﻿/**
 * @license Licensed under the terms of any of the following licenses at your choice:
 * 
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *    (See Appendix A)
 * 
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *    (See Appendix B)
 * 
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *    (See Appendix C)
 * 
 */

/**
 * @fileOverview Paste as code plugin.
 */

(function() {
	// The pastecode command definition.
	var pasteCodeCmd = {
		// Snapshots are done manually by editable.insertXXX methods.
		canUndo: false,
		async: true,

		exec: function( editor ) {
			editor.getClipboardData({ title: editor.lang.pastecode.title }, function( data ) {
				// Do not use editor#paste, because it would start from beforePaste event.
				//data && editor.fire( 'paste', { type: 'html', dataValue: data.dataValue } );
				editor.insertHtml( data.dataValue );

				editor.fire( 'afterCommandExec', {
					name: 'pastecode',
					command: pasteCodeCmd,
					returnValue: !!data
				});
			});
		}
	};

	// Register the plugin.
	CKEDITOR.plugins.add( 'pastecode', {
		requires: 'clipboard',
		lang: ['en', 'ru', 'uk'],
		icons: 'pastecode,pastecode-ltl,pastecode-rtl',
		init: function( editor ) {
			var commandName = 'pastecode';

			editor.addCommand( commandName, new CKEDITOR.dialogCommand( 'pastecode', {
				//allowedContent : required,
				//requiredContent : required
			} ) );

			editor.ui.addButton && editor.ui.addButton( 'PasteCode', {
				label: editor.lang.pastecode.button,
				command: commandName,
				toolbar: 'clipboard,40'
			});

			CKEDITOR.dialog.add( 'pastecode', function( editor )
			{
				return {
					title : editor.lang.pastecode.title,
					minWidth : 350,
					minHeight : 300,
					contents : [
						{
							id : 'general',
							label : editor.lang.pastecode.code,
							elements : [
								{
									type : 'textarea',
									id : 'contents',
									label : editor.lang.pastecode.code,
                                    inputStyle: 'height: 100%; white-space: pre-wrap',
									//cols: 140,
									rows: 20,
									validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.pastecode.notEmpty ),
									required : true,
									commit : function()
									{
										//element.setHtml( this.getValue() );
										editor.insertHtml( this.getValue() );
									}
								},
								{
									id: 'video_select',
									type: 'button',
									label: editor.lang.pastecode.select_label,
									title: editor.lang.pastecode.select_title,
									style: 'display: block;',
									filebrowser: {
										action: 'Browse',
										url: editor.config.filebrowserVideoManagerListUrl,
										target: 'general:contents',
										onSelect : function(code) {
											var dialog = this.getDialog();
											dialog.getContentElement('general','contents').setValue(code);
											document.getElementById(dialog.getButton('ok').domId).click();
										}
									}
								}
							]
						}
					],
					onOk : function()
					{
						/*
						if ( this.insertMode )
							editor.insertElement( this.pre );

						this.commitContent( this.pre );
						*/
						this.commitContent();
					}
				};
			} );

			editor.on( 'paste', function( evt ) {
				// Do NOT overwrite if HTML format is explicitly requested.
				// This allows pastefromword dominates over pastecode.
				/*
				if ( evt.data.type != 'html' )
					evt.data.type = 'text';
				*/
			});

			editor.on( 'pasteState', function( evt ) {
				editor.getCommand( commandName ).setState( evt.data );
			});
		}
	});
})();
