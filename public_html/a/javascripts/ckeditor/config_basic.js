/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config
	
	// CKEDITOR.config.toolbar = [
	//    ['Styles','Format','Font','FontSize'],
	//    '/',
	//    ['Bold','Italic','Underline','StrikeThrough','-','Undo','Redo','-','Cut','Copy','Paste','Find','Replace','-','Outdent','Indent','-','Print'],
	//    '/',
	//    ['NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	//    ['Image','Table','-','Link','Flash','Smiley','TextColor','BGColor','Source']
	// ] ;	
	
	
	config.toolbar_Forum =
	[
	    { name: 'riga1',      items : ['Bold','Italic','Underline','-','TextColor','BGColor','-',
	                                 'NumberedList','BulletedList','-',
	                                 'HorizontalRule','SpecialChar','-','Link','Unlink','-',
	                                  'Undo','Redo'] },
	    '/',
	    { name: 'riga2',      items : ['Outdent','Indent','-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Maximize'] }
	];

	config.toolbar = 'Forum';
	
    config.filebrowserBrowseUrl = 'ckeditor/kcfinder/browse.php?type=files';
    config.filebrowserImageBrowseUrl = 'ckeditor/kcfinder/browse.php?type=images';
    config.filebrowserFlashBrowseUrl = 'ckeditor/kcfinder/browse.php?type=flash';
    config.filebrowserUploadUrl = 'ckeditor/kcfinder/upload.php?type=files';
    config.filebrowserImageUploadUrl = 'ckeditor/kcfinder/upload.php?type=images';
    config.filebrowserFlashUploadUrl = 'ckeditor/kcfinder/upload.php?type=flash';	
	
};

