<?php  if ( ! defined('__SITE_PATH')) exit('No direct script access allowed');
// --- -------------------------------------------------------------------------------------------------- ---//
// --- APPLICATION ---//

$config['application']['application_env'] 			= "development"; // Define application environment => 'production'; 'staging'; 'test'; 'development';
$config['application']['default_uri'] 				= "site-index/home/index";
$config['application']['admin_default_uri'] 		= "home/dashboard/show";
$config['application']['admin_header_title'] 		= "FLATY Admin";
$config['application']['admin_footer_title'] 		= "2015 © FLATY Admin Template.";
// $config['application']['display_error_404'] 		= FALSE;
// $config['application']['error_reporting'] 			= 0; // Neu = 0 : Khong hien thi bat cu thong bao nao
// $config['application']['error_reporting'] 			= E_ALL; // Neu = 0 : Khong hien thi bat cu thong bao nao
// $config['application']['error_reporting'] 			= E_ALL ^ E_DEPRECATED; // Hien thi thong bao tat ca cac loi tru cac ham DEPRECATED
$config['application']['language'] 					= "en";
$config['application']['timezone'] 					= "Asia/Ho_Chi_Minh";
$config['application']['currency'] 					= "USD";
$config['application']['config_compression'] 		= 5; //; config_compression = 0 -> 9
$config['application']['show_benchmark']			= TRUE;
$config['application']['enable_seo_url'] 			= TRUE;

// --- -------------------------------------------------------------------------------------------------- ---//
// --- MASTER DATABASE ---//

//$config['database_master']['db_driver'] 			= "pdo"; // mysqli
$config['database_master']['db_driver'] 			= "Pdo"; // MySqli
$config['database_master']['db_hostname'] 			= "localhost";
$config['database_master']['db_name'] 				= "db-cms";
$config['database_master']['db_username'] 			= "root";
$config['database_master']['db_password'] 			= "";
$config['database_master']['db_port'] 				= 3306;
$config['database_master']['db_prefix'] 			= "z__";

// --- -------------------------------------------------------------------------------------------------- ---//
// --- SLAVE DATABASE ---//

$config['database_slave']['db_driver'] 				= "pdo"; // mysqli
$config['database_slave']['db_hostname'] 			= "localhost";
$config['database_slave']['db_name'] 				= "none-db";
$config['database_slave']['db_username'] 			= "root";
$config['database_slave']['db_password'] 			= "";
$config['database_slave']['db_port'] 				= 3306;
$config['database_slave']['db_prefix'] 				= "z__";

// --- -------------------------------------------------------------------------------------------------- ---//
// --- SESSION ---//

$config['session']['match_ip'] 						= FALSE;
$config['session']['match_fingerprint'] 			= TRUE;
$config['session']['match_token'] 					= FALSE;
$config['session']['session_name'] 					= "simple_mvc_session";
$config['session']['cookie_path'] 					= "/";
$config['session']['cookie_domain'] 				= NULL;
$config['session']['cookie_secure'] 				= NULL;
$config['session']['cookie_httponly'] 				= NULL;
$config['session']['regenerate'] 					= 300;
$config['session']['expiration'] 					= 7200;
$config['session']['gc_probability'] 				= 100;
$config['session']['session_database'] 				= TRUE; //FALSE;
$config['session']['table_name'] 					= $config['database_master']['db_prefix']."sessions";
$config['session']['primary_key'] 					= "session_id";

// --- -------------------------------------------------------------------------------------------------- ---//
// --- MAIL ---//

// -- Tam khong dung thang nay do da luu trong bang configure --
$config['mail']['mailer_type'] 						= "system";
$config['mail']['smtp_auth'] 						= TRUE;
$config['mail']['smtp_server'] 						= "smtp.gmail.com";
$config['mail']['smtp_secure'] 						= "ssl"; // secure transfer enabled REQUIRED for Gmail : tls
$config['mail']['smtp_port'] 						= 465;
$config['mail']['smtp_timeout'] 					= 30;
$config['mail']['smtp_html_content'] 				= TRUE;
$config['mail']['smtp_usr'] 						= "dylanmobiledev@gmail.com";
$config['mail']['smtp_psw'] 						= "akpoomydoyopmaug";
$config['mail']['smtp_from_email'] 					= "buivantienduc@gmail.com";
$config['mail']['smtp_from_name'] 					= "Duc Bui";
$config['mail']['smtp_reply_email'] 				= "buivantienduc@gmail.com";
$config['mail']['smtp_reply_name'] 					= "Duc Bui";
$config['mail']['smtp_debug'] 					    = 0; // debugging: 1 = errors and messages, 2 = messages only

// --- -------------------------------------------------------------------------------------------------- ---//
// --- LOGGER ---//

$config['logging']['log_level'] 					= 200;
$config['logging']['log_handler'] 					= "file";
$config['logging']['log_file'] 						= "/tmp/ngukho.log";

/*
|--------------------------------------------------------------------------
| CACHE Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| [__SITE_PATH]/cache/ folder.  Use a full server path with trailing slash.
|
*/

$config['cache']['cache_path'] 						= '/public_html/cache/';
$config['cache']['life_time'] 						= 60;

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
|
|
*/

// $config['routes'][''] 								= "";
// $config['routes']['(:any)'] = "router/$1";
// $config['routes']['products/(:any)'] = "category/$1";
// $config['routes']['products/([a-z]+)/(\d+).html'] = "$1/abc_$2";
// $config['routes']['links/([a-zA-Z0-9_-]+)'] = "site/index/links/$1";

$config['routes']['links/chuyen-tieng-viet/(:num)'] = "site-index/index/links-item";
$config['routes']['links/chuyen-tieng-viet'] = "site-index/index/links-item";

$config['routes']['links/(:any)'] = "site-index/index/links/$1";
$config['routes']['load/(:any)-post(:num).htm'] = "site-index/index/load-router/$1/$2";
$config['routes']['load/(:any)-post(:num).html'] = "site-index/index/load-router/$1/$2";


// $config['routes']['links/(.*?)'] = "site/index/links/$1";
 
// $config['routes']['journals'] 						= "blogs";
// $config['routes']['blog/joe'] 						= "blogs/users/34";
// $config['routes']['product/(:any)/(:any)'] 			= "catalog/product_lookup/$1/$2";
// $config['routes']['product/(:num)'] 				= "catalog/product_lookup_by_id/$1";


return $config;