<?php

	// define the site path __SITE_PATH : c:\xampp\htdocs\adv_mvc
	define ('__SITE_PATH', realpath(dirname(dirname(dirname(__FILE__)))));
	// __SITE_URL : /adv_mvc/
// 	define ('__SITE_URL', str_replace('public_html/','', dirname(str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME'])).'/'));

    $tmp = str_replace('public_html/',"",$_SERVER['SCRIPT_NAME']);
    define ('__SITE_URL', str_replace(basename($tmp),"",$tmp));


	// __BASE_URL : /adv_mvc/admin/
 	define ('__BASE_URL', __SITE_URL);
	// Co thu muc public_html 	
// 	define ('__PUBLIC_HTML', dirname(__SITE_URL).'public_html/');
    define ('__PUBLIC_HTML', str_replace('//', '/', dirname(__SITE_URL).'/'));

 	// ---- Khong Thay Doi ---- //
 	define ('__ASSET_URL', __PUBLIC_HTML.'assets/');
 	define ('__TEMPLATE_URL', __PUBLIC_HTML.'a/flaty_template/');
 	
 	define ('__IMAGE_URL', __PUBLIC_HTML.'a/images/');
 	define ('__CSS_URL', __PUBLIC_HTML.'a/stylesheets/');
 	define ('__JS_URL', __PUBLIC_HTML.'a/javascripts/');

	// Tam thoi bo wa 	
//  	define ('__PUBLIC_JS_URL', __ASSET_URL.'js/');
//  	define ('__PUBLIC_IMG_URL', __ASSET_URL.'images/');
//  	define ('__PUBLIC_CSS_URL', __ASSET_URL.'css/');
 	
	// the application directory path 
	define ('__APP_PATH', __SITE_PATH.'/admin');	
	define ('__VIEW_PATH', __APP_PATH.'/views');	
	define ('__LAYOUT_PATH', __SITE_PATH.'/app/layouts');
//	define ('__CONFIG_PATH', __SITE_PATH.'/app/config');
	
	define ('__UPLOAD_DATA_PATH', __SITE_PATH.'/public_html/data/upload/');	
	define ('__UPLOAD_DATA_URL', __PUBLIC_HTML . 'data/upload/');
	
	define ('__UPLOAD_GALLERY_PATH', __UPLOAD_DATA_PATH . 'gallery/');
	define ('__UPLOAD_GALLERY_URL', __UPLOAD_DATA_URL . 'gallery/');

//    echo "<pre>";
//    print_r($_SERVER);
//    echo "</pre>";
//
//    $const = get_defined_constants(true);
//    echo "<pre>";
//    print_r($const['user']);
//    echo "</pre>";
//    exit();

	define ('__CONTROLLER_NAMESPACE', 'Admin\Controller');	
		
	/*** include the helper ***/
 	// $_autoload_helpers = array('form','admin_func','array');
 	$lang = NULL;
 	$config = NULL;
	
	require __SITE_PATH . '/admin/startup.php';

    $config->config_values['application']['default_uri'] = "common/home/login";
	
 	/*** a new registry object ***/
 	$registry = new \App\Lib\Core\Registry();
 	
 	// Session
 	$oSession = new \App\Lib\Session();
 	$registry->oSession = $oSession;
 	
 	$configSystem = new \App\Model\Base\ConfigureSystem();
 	$configure_mod = $configSystem->getConfigureData();
 	$configure_mod['default_global_lang'] = $lang;
 	$registry->oConfigureSystem = $configure_mod; 	
 	
	// Response
	$response = new \App\Lib\Core\Response();
	$response->addHeader('Content-Type: text/html; charset=utf-8');
	$registry->oResponse = $response; 
 	
	// Config
	$registry->oConfig = $config;

	// Input
	$input = new \App\Lib\Input();
	$registry->oInput = $input;	
	
	$view = new \App\Lib\Core\View('admin');
    $view->setTemplateDir(__VIEW_PATH);
    $view->setLayoutDir(__LAYOUT_PATH);
	$registry->oView = $view;

	// Auth
	$registry->oAuth = new \App\Lib\Auth($registry);
	
	// Initialize the FrontController
	$front = \App\Lib\Core\FrontController::getInstance();
	$front->setRegistry($registry);
	
	/*
		// Cau hinh cho cac action nay chay dau tien 
	*/

	$front->addPreRequest(new \App\Lib\Core\Request('common/common/check-login'));
	$front->addPreRequest(new \App\Lib\Core\Request('common/common/check-permission'));

	// Run dispatch()
	$front->dispatch();

	// Output
	$response->output();