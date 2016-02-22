/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	//config.uiColor = '#AADC6E';
	
	
	config.toolbar_Forum =
	[
	    { name: 'riga1',      items : ['Bold','Italic','Underline','-','TextColor','BGColor','-',
	                                 'NumberedList','BulletedList','-',
	                                 'HorizontalRule','SpecialChar','-','Link','Unlink','-',
	                                  'Undo','Redo'] },
	    '/',
	    { name: 'riga2',      items : ['Outdent','Indent','-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Maximize'] }
	];
	
	config.toolbar_Basic_1 = [
	   ['Styles','Format','Font','FontSize'],
	   ['Bold','Italic','Underline','StrikeThrough','-','Undo','Redo','-','Cut','Copy','Paste','Find','Replace','-','Outdent','Indent','-','Print'],
	   '/',
	   ['NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	   ['Image','Table','-','Link','Flash','Smiley','TextColor','BGColor','Source']
	] ;	

	config.toolbar_Basic_2 = [
			{ name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },	// Defines toolbar group with name (used to create voice label) and items in 3 subgroups.
			[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],			// Defines toolbar group without name.
			'/',																					// Line break - next group will be placed in new line.
			{ name: 'basicstyles', items: [ 'Bold', 'Italic' ] }
		];
		
		
		// Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
	config.toolbar_Full = [
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
			{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
			'/',
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
			'/',
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			{ name: 'others', items: [ '-' ] }
		];
					
		
	// Default toolbar	
	config.toolbar = 'Full';		
	//CKEDITOR.basePath => http://localdomain/z-cms/public_html/admin/javascripts/ckeditor/
    config.filebrowserBrowseUrl = CKEDITOR.basePath +'kcfinder/browse.php?type=files';
    config.filebrowserImageBrowseUrl = CKEDITOR.basePath + 'kcfinder/browse.php?type=images';
    config.filebrowserFlashBrowseUrl = CKEDITOR.basePath + 'kcfinder/browse.php?type=flash';
    config.filebrowserUploadUrl = CKEDITOR.basePath + 'kcfinder/upload.php?type=files';
    config.filebrowserImageUploadUrl = CKEDITOR.basePath + 'kcfinder/upload.php?type=images';
    config.filebrowserFlashUploadUrl = CKEDITOR.basePath + 'kcfinder/upload.php?type=flash';
	
};

