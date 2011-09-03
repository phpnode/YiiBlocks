/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview The "widget" plugin.
 *
 */

(function()
{
	var widgetReplaceRegex = /(<widget(.*?)>(.*?)<\/widget>)/ig;
	function createFakeElement( editor, realElement )
	{
		var fakeElement = editor.createFakeParserElement( realElement, 'cke_widget', 'widget', true ),
			fakeStyle = fakeElement.attributes.style || '';

		var width = realElement.attributes.width,
			height = realElement.attributes.height;

		if ( typeof width != 'undefined' )
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'width:' + cssifyLength( width ) + ';';

		if ( typeof height != 'undefined' )
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'height:' + cssifyLength( height ) + ';';
		console.log(fakeElement);
		return fakeElement;
	}
	CKEDITOR.plugins.add( 'widget',
	{
		requires : [ 'dialog' ],
		lang : [ 'en', 'he' ],
		init : function( editor )
		{
			var lang = editor.lang.widget;

			editor.addCommand( 'createwidget', new CKEDITOR.dialogCommand( 'createwidget' ) );
			editor.addCommand( 'editwidget', new CKEDITOR.dialogCommand( 'editwidget' ) );
			editor.addCss(
				'img.cke_widget' +
				'{' +
					'background-image: url(' + CKEDITOR.getUrl( this.path + 'widget.gif' ) + ');' +
					'background-position: center center;' +
					'background-repeat: no-repeat;' +
					'border: 1px solid #a9a9a9;' +
					'width: 80px;' +
					'height: 80px;' +
				'}' 
				);

			// If the "menu" plugin is loaded, register the menu items.
			
			editor.ui.addButton( 'CreateWidget',
			{
				label : lang.toolbar,
				command :'createwidget',
				icon : this.path + 'widget.gif'
			});

			if ( editor.addMenuItems )
			{
				editor.addMenuGroup( 'widget', 20 );
				editor.addMenuItems(
					{
						editwidget :
						{
							label : lang.edit,
							command : 'editwidget',
							group : 'widget',
							order : 1,
							icon : this.path + 'widget.gif'
						}
					} );

				if ( editor.contextMenu )
				{
					
					editor.contextMenu.addListener( function( element, selection )
					{
						if ( element && element.is( 'img' ) && !element.isReadOnly()
								&& element.data( 'cke-real-element-type' ) == 'widget' )
							return { editwidget : CKEDITOR.TRISTATE_OFF };
					});
				}
			}

			editor.on( 'doubleclick', function( evt )
				{
					if ( CKEDITOR.plugins.widget.getSelectedWidget( editor ) )
						evt.data.dialog = 'editwidget';
				});

			editor.addCss(
				'.cke_widget' +
				'{' +
					'background-color: #ffff00;' +
					( CKEDITOR.env.gecko ? 'cursor: default;' : '' ) +
				'}'
			);
			
			editor.on( 'contentDom', function()
				{
					editor.document.getBody().on( 'resizestart', function( evt )
						{
							if ( editor.getSelection().getSelectedElement().data( 'cke-widget' ) )
								evt.data.preventDefault();
						});
				});

			CKEDITOR.dialog.add( 'createwidget', this.path + 'dialogs/widget.js' );
			CKEDITOR.dialog.add( 'editwidget', this.path + 'dialogs/widget.js' );
		},
		afterInit : function( editor )
		{
			var dataProcessor = editor.dataProcessor,
				dataFilter = dataProcessor && dataProcessor.dataFilter,
				htmlFilter = dataProcessor && dataProcessor.htmlFilter;

			if ( dataFilter )
			{
				dataFilter.addRules(
					{
						elements :
						{
							
							'widget' : function( element )
							{
								var attributes = element.attributes, i,
									classId = attributes.classid && String( attributes.classid ).toLowerCase();
								
								for (i in element.children) {
									if (element.children.hasOwnProperty(i)) {
										
									}
								}
								return createFakeElement( editor, element );
							}
						
							
						}
					});
			}

			if ( htmlFilter )
			{
				htmlFilter.addRules(
				{
					elements :
					{
						'widget' : function( element )
						{
							
							if ( element.attributes && element.attributes[ 'data-cke-widget' ] ) {
								delete element.name;
							}
						}
						
					}
				});
			}
		}
	});
})();

CKEDITOR.plugins.widget =
{
	createWidget : function( editor, oldElement, text, isGet )
	{
		var element = new CKEDITOR.dom.element( 'widget', editor.document );
		element.setAttributes(
			{
				contentEditable		: 'false',
				'data-cke-widget'	: 1,
				'class'			: 'cke_widget'
			}
		);

		text && element.setText( text );

		if ( isGet )
			return element.getOuterHtml();

		if ( oldElement )
		{
			if ( CKEDITOR.env.ie )
			{
				element.insertAfter( oldElement );
				// Some time is required for IE before the element is removed.
				setTimeout( function()
					{
						oldElement.remove();
						element.focus();
					}, 10 );
			}
			else
				element.replace( oldElement );
		}
		else
			editor.insertElement( element );

		return null;
	},

	getSelectedWidget : function( editor )
	{
		
		var node = editor.getSelection().getSelectedElement();
		while( node && !( node.type == CKEDITOR.NODE_ELEMENT && node.data( 'cke-real-element-type' ) == 'widget' ) ) {
			node = node.getParent();
		}
		return node;
	}
};
