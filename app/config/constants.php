<?php  if ( ! defined('__SITE_PATH')) exit('No direct script access allowed');

define('_FB_APP_ID', '534956693276758');
define('_FB_APP_SECRET', '2e23283d32b30b1c53e978b68d32c40b');

define('_TB_PREFIX', $config->config_values['database_master']['db_prefix']);

// --- -------------------------------------------------------------------------------------------------- ---//
// --- CMS TABLE ---//

define('TB_MAIN_CATS', _TB_PREFIX . 'main_category');
define('TB_USER', _TB_PREFIX . 'user');
define('TB_USER_GROUP', _TB_PREFIX . 'user_group');
define('TB_CONTENTS', _TB_PREFIX . 'content');
define('TB_CONTENT_CATS', _TB_PREFIX . 'content_cat');
define('TB_PRODUCTS', _TB_PREFIX . 'product');
define('TB_PRODUCT_CATS', _TB_PREFIX . 'product_cat');
define('TB_GALLERY', _TB_PREFIX . 'gallery');
// define('TB_CONFIGURES', _TB_PREFIX . 'configure');
// define('TB_CONFIGURE_CATS', _TB_PREFIX . 'configure_cat');
define('TB_CONFIGURE_SYSTEM', _TB_PREFIX . 'configure');
define('TB_CONFIGURE_SYSTEM_GROUP', _TB_PREFIX . 'configure_group');
define('TB_LANGUAGE', _TB_PREFIX . 'language');
define('TB_MEMBER', _TB_PREFIX . 'member');
define('TB_CONTACT', _TB_PREFIX . 'contact');

define('TB_URL_ALIAS', _TB_PREFIX . 'url_alias');

// --- -------------------------------------------------------------------------------------------------- ---//
// --- PAGE MODULE TABLE ---//

define('TB_PAGE_CONTENT', _TB_PREFIX . 'page_content');
define('TB_PAGE_CONTENT_LN', _TB_PREFIX . 'page_content_ln');
define('TB_PAGE_CONTENT_OPTIONS', _TB_PREFIX . 'page_content_options');
define('TB_PAGE_CATEGORY', _TB_PREFIX . 'page_category');
define('TB_PAGE_CATEGORY_LN', _TB_PREFIX . 'page_category_ln');
define('TB_PAGE_CATEGORY_PATH', _TB_PREFIX . 'page_category_path');
define('TB_PAGE_CONFIGURE', _TB_PREFIX . 'page_configure');
define('TB_PAGE_GALLERY', _TB_PREFIX . 'page_gallery');

// --- -------------------------------------------------------------------------------------------------- ---//
// --- EXAMPLE MODULE TABLE ---//

define('TB_EX_USER', _TB_PREFIX . '_ex_user');
define('TB_EX_ADVERTISING', _TB_PREFIX . '_ex_advertising');
define('TB_EX_CONTENT', _TB_PREFIX . '_ex_content');
define('TB_EX_CONTENT_CAT', _TB_PREFIX . '_ex_content_cat');
define('TB_EX_CONTENT_CAT_PATH', _TB_PREFIX . '_ex_content_cat_path');
define('TB_EX_PRODUCT', _TB_PREFIX . '_ex_product');
define('TB_EX_PRODUCT_CAT', _TB_PREFIX . '_ex_product_cat');


// define('TB_OPTIONS', _TB_PREFIX . 'options');
// define('TB_OPTIONS_LN', _TB_PREFIX . 'options_ln');
// define('TB_HTML', _TB_PREFIX . 'html');
// define('TB_HTML_LN', _TB_PREFIX . 'html_ln');

// define('TB_CONFIGURE_MODULE', _TB_PREFIX . 'configure_mod');
// define('TB_COMMENT', _TB_PREFIX . 'comment');



// define('TB_MEMBER_DETAIL', _TB_PREFIX . 'member_detail');


// define('CONFIG_FACEBOOK_GROUP_ID', '6');
// define('CONFIG_CALTEX2013_GROUP_ID', '7');
// define('CODE_CALTEX2013_GIFT_QUANTUM_1', 'caltex2013_gift_quatum_1');
// define('CODE_CALTEX2013_GIFT_QUANTUM_2', 'caltex2013_gift_quatum_2');
// define('CODE_CALTEX2013_LIMIT_GIFT_PER_USER', 'caltex2013_limit_gift_per_user');
// define('CODE_CALTEX2013_LIMIT_WIN_IN_EVENT', 'caltex2013_limit_win_in_event');





/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

