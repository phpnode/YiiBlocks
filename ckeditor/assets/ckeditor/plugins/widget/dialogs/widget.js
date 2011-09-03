/*
 * Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

(function()
{
	function widgetDialog( editor, isEdit )
	{

		var lang = editor.lang.widget,
			generalLabel = editor.lang.common.generalTab, fakeImage;
		return {
			title : lang.title,
			minWidth : 300,
			minHeight : 80,
			contents :
			[
				{
					id : 'info',
					label : generalLabel,
					title : generalLabel,
					elements :
					[
						{
							id : 'text',
							type : 'text',
							style : 'width: 100%;',
							label : lang.text,
							'default' : '',
							required : true,
							validate : CKEDITOR.dialog.validate.notEmpty( lang.textMissing ),
							setup : function( element )
							{
								if ( isEdit )
									this.setValue( element.getAttribute("type") );
							},
							commit : function( element )
							{
								var text = this.getValue();
								element.setAttribute("type",text);
								var fake = editor.createFakeElement(element, "cke_widget", "widget", "true");
								if (fakeImage) {
									
									fake.replace(fakeImage);
								}
								else {
									editor.insertElement(fake);
								}
							}
						}
					]
				}
			],
			onShow : function()
			{
				if ( isEdit ) {
					fakeImage = CKEDITOR.plugins.widget.getSelectedWidget( editor );
					this._element = editor.restoreRealElement(fakeImage);
				}
				
				this.setupContent( this._element );
			},
			onOk : function()
			{
				
				this.commitContent( this._element );
				delete this._element;
			}
		};
	}

	CKEDITOR.dialog.add( 'createwidget', function( editor )
		{
			return widgetDialog( editor );
		});
	CKEDITOR.dialog.add( 'editwidget', function( editor )
		{
			return widgetDialog( editor, 1 );
		});
} )();
