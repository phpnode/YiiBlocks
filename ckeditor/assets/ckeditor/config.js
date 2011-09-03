/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config ) {
        config.startupFocus = false;
        config.height = '40em';
        config.protectedSource.push(/<\?[\s\S]*?\?>/g); /* Protect PHP Code from being stripped when moving to source mode */
        config.extraPlugins = 'codemirror';
        config.resize_enabled = false;
        config.toolbarCanCollapse = false; /* Remove the collapsing button of the toolbar */
        config.toolbar_Large = [
        ['Italic', 'Bold', 'Underline', 'Strike', '-', 'BulletedList', 'JustifyCenter', '-', 'Link', 'SpecialChar', 'BGColor', 'TextColor', '-', 'Undo', 'Redo', '-', 'Source']
        ];
        config.toolbar_Small = [
        ['Italic', 'Bold', 'Underline', 'Strike', '-', 'BulletedList', 'JustifyCenter', '-', 'Link', 'SpecialChar', 'BGColor', 'TextColor', '-', 'Undo', 'Redo', '-', 'Source']
        ];
        config.toolbar_Standard = [
	                           ['Styles','Format','Font','FontSize'],
	                           '/',
	                           ['Bold','Italic','Underline','StrikeThrough','-','Undo','Redo','-','Cut','Copy','Paste','Find','Replace','-','Outdent','Indent','-','Print'],
	                           '/',
	                           ['NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	                           ['Image','Table','-','Link','Flash','Smiley','TextColor','BGColor','Source']
	                        ] ;
	
		config.toolbar_Full =
		[
		    
		    ['Templates','Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
		    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		    '/',
		    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
		    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		    ['Link','Unlink','Anchor'],
		    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
		    '/',
		    ['Styles','Format','Font','FontSize'],
		    ['TextColor','BGColor'],
		    ['Maximize', 'ShowBlocks','-','Source']
		];

		config.toolbar_Basic =
		[
		    ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
		];
        config.toolbar = "Full";
        config.fillEmptyBlocks = false;
        config.enterMode = CKEDITOR.ENTER_BR; /* Enter key means br not p */
        config.shiftEnterMode = CKEDITOR.ENTER_P; /* Paragraphs are now made by pressing shift and enter together instead */
};
