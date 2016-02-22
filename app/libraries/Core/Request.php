<?php

namespace App\Lib\Core;

final class Request 
{
	// protected $file;
	protected $class;
	protected $method;
	// protected $dir_template;
	protected $moduleNamespace;

    // -- Default value --
    protected $module = 'index';
	protected $controller = 'index';
	protected $action = 'index';
    protected $args = array();

	public function __construct($route  = 'index/index/index', $args = array())
	{
		$this->parseUri($route);
		
		$this->moduleNamespace = __CONTROLLER_NAMESPACE.'\\'.$this->upperCamelcase($this->module).'\\';
		$this->class = $this->moduleNamespace.$this->upperCamelcase($this->controller).'Controller';		
		$this->method = $this->lowerCamelcase($this->action).'Action';
		$this->args = array_merge($this->args,$args);
	}
	
	private function parseUri($route)
	{
//		$config = Config::getInstance();
		
		// removes the trailing slash
//		$route = preg_replace("/\/$/", '', $route);
// 		/this/that/theother/ => this/that/theother
		$route = trim($route, '/');
		
		// get the default uri
//		if(empty($route))
//			$route = $config->config_values['application']['default_uri'];
			
//		$path = '';
		$parts = explode('/', str_replace('../', '', $route));

        $module = array_shift($parts);
        if(empty($module))
            return;
        $this->module = $module;

        $controller = array_shift($parts);
        if(empty($controller))
            return;
        $this->controller = $controller;

        $action = array_shift($parts);
        if(empty($action))
            return;
        $this->action = $action;

        $this->args = $parts;

        // -- Tam thoi dong de kiem tra ham --
		
//		$i = 0;
//		foreach ($parts as $part)
//		{
////			$path .= $part;
//			if($i == 0)
//			{
////				$this->module = $path;
//                $this->module = $part;
////				$path .= '/';
//				array_shift($parts);
//				$i++;
//				continue;
//			}
//			$this->controller = $part;
//			array_shift($parts);
//			break;
//		}
//
//		// Neu controller la rong . Route co dang [module]/
//		if(empty($this->controller))
//		{
//			$this->controller = 'index';
//		}
//
//		$method = array_shift($parts);
//
//		if ($method) {
//			$this->action = $this->method = $method;
//		} else {
//			$this->action = $this->method = 'index';
//		}
//
//		$this->args = $parts;
	}
	
	// public function getFile() {
	// 	return $this->file;
	// }
	
	public function getClass() {
		return $this->class;
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	// public function getDirTemplate() {
	// 	return $this->dir_template;
	// }
	
	// public function getFileTemplate() {
	// 	return $this->dir_template  . '/' . $this->method . '.phtml';
	// }

    public function setArgs($args) {
        $this->args = $args;
    }

	public function getArgs() {
		return $this->args;
	}
	
	public function getRouter()	{
		return "{$this->module}/{$this->controller}/$this->action";		
	}
	
	public function getModule() {
		return $this->module;
	}

	public function getController() {
		return $this->controller;
	}

	public function getAction() {
		return $this->action;
	}

    // -- Fixed DucBui : 24/11/2015  --
    public static function staticRun($request)
    {
        if(!$request instanceof Request)
            $request = new Request($request);

        return $request->run();
    }

    public function run()
    {
        $class  = $this->getClass();
        $method = $this->getMethod();
        $args   = $this->getArgs();

        try {
            $rc = new \ReflectionClass($class);
            if($rc->isSubclassOf('\App\Lib\Core\BaseController'))
            {
                $controller = $rc->newInstance();
                $classMethod = $rc->getMethod($method);
                return $classMethod->invokeArgs($controller,$args);
            }
            else {
            	throw new \Exception("abstract class BaseController must be extended");
            }
        }
        catch (\ReflectionException $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

	//// underscored to upper-camelcase 
	//// e.g. "this_method_name" -> "ThisMethodName" 
	private function upperCamelcase($string)
	{
//		return preg_replace('/(?:^|-)(.?)/e',"strtoupper('$1')",$string);
        // -- User for php 5.6 -> 7 --
        return preg_replace_callback(
            '/(?:^|-)(.?)/',
            function($match) { return strtoupper($match[1]); },
            $string
        );
    }

	//// underscored to lower-camelcase 
	//// e.g. "this_method_name" -> "thisMethodName" 
	private function lowerCamelcase($string)
	{
//		return preg_replace('/-(.?)/e',"strtoupper('$1')",$string);
        // -- User for php 5.6 -> 7 --
        return preg_replace_callback(
            '/-(.?)/',
            function($match) { return strtoupper($match[1]); },
            $string
        );
	}	

	// camelcase (lower or upper) to hyphen 
	// e.g. "thisMethodName" -> "this_method_name" 
	// e.g. "ThisMethodName" -> "this_method_name"
	// Of course these aren't 100% symmetric.  For example...
	//  * this_is_a_string -> ThisIsAString -> this_is_astring
	//  * GetURLForString -> get_urlfor_string -> GetUrlforString 
	private function camelcaseToHyphen($string)
	{
//		return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1-$2", $string));
        // -- User for php 5.6 -> 7 --
        return preg_replace_callback(
            '/([^A-Z])([A-Z])/',
            function($match) { return $match[1].'-'.$match[2]; },
            $string
        );

	}

}
?>