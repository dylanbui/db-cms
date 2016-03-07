<?php  if ( ! defined('__SITE_PATH')) exit('No direct script access allowed');

	// Load config files. Global config file
    require __SITE_PATH.'/vendor/autoload.php';

	require __SITE_PATH.'/app/libraries/Core/Psr4Autoloader.php';
	// instantiate the loader
 	$loader = new Psr4Autoloader();
 	// register the base directories for the namespace prefix
 	
 	$loader->addNamespace(__CONTROLLER_NAMESPACE, __APP_PATH.'/controllers'); 	
 	$loader->addNamespace('App\Lib', __SITE_PATH.'/app/libraries'); 	
 	$loader->addNamespace('App\Model', __SITE_PATH.'/app/models');
 	$loader->addNamespace('App\Helper', __SITE_PATH.'/app/helpers');
 	// register the autoloader
 	$loader->register();

    // Create configure object
    $config = \App\Lib\Core\Config::getInstance();
    $config->load(__SITE_PATH.'/app/config/config.php');

	define('APPLICATION_ENV', $config->config_values['application']['application_env']); 
	// set the timezone
	date_default_timezone_set($config->config_values['application']['timezone']);	

    require __SITE_PATH . '/app/config/constants.php';   

	/*** set error handler level to E_WARNING ***/
//	\App\Lib\Core\ErrorHandler::register();
    \App\Lib\Core\ExceptionHandler::register();

	// Load language
	// -- Cach nay chi de tham khao --
	$lang = $config->config_values['application']['language'];
	if(!empty($lang))
	{
		$file = __APP_PATH . '/lang/' . strtolower($lang) . '.lang.php';
		if(file_exists($file))
		{
			include $file;
			if (!function_exists('class_alias')) {
			    function class_alias($original, $alias) {
			        eval('abstract class ' . $alias . ' extends ' . $original . ' {}');
			    }
			}
		}
		else 
			throw new Exception("File not found : {$file}");
		// alias the lang class
		class_alias($lang,'Lang');
	/* -------------- */
	}