/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.filebrowserBrowseUrl = 'ckeditor/plugins/uploadwidget/imgbrowser.php';
	config.filebrowserBrowseUrl = 'ckeditor/plugins/uploadwidget/imgupload.php';	
	config.extraPlugins = 'imageuploader';
		
};

CKEDITOR.plugins.add( 'imageuploader', {
    init: function( editor ) {
        editor.config.filebrowserUploadUrl = 'ckeditor/plugins/uploadwidget/imgupload.php';
        editor.config.filebrowserBrowseUrl = 'ckeditor/plugins/uploadwidget/imgbrowser.php';
    }
});	 