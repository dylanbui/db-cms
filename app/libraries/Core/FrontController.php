<?php

/**
 *
 * @Front Controller class
 *
 * @package Core
 *
 */

namespace App\Lib\Core;

class FrontController
{

	protected $_module, $_controller, $_action, $_registry, $_curr_request;
	protected $pre_request = array();	

	public static $_instance;

	public static function getInstance()
	{
		if( ! (self::$_instance instanceof self) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct()
	{

	}
	
	public function addPreRequest($pre_request) 
	{
		$this->pre_request[] = $pre_request;
	}

	private function loadPreRouter($routes)
	{
		$uri = trim($_GET['_url'],'/');
		
		foreach ($routes as $key => $val)
		{
			// Convert wildcards to RegEx
//			$key = str_replace(array(':any', ':num'), array('.+', '[0-9]+'), $key);
            $key = str_replace(array(':other', ':any', ':num'), array('[/]{0,1}[A-Za-z0-9\-\\/\.]+', '.+', '[0-9]+'), $key);

			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri, $matches))
			{
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				$_GET['_url'] = $val;
                // -- Remove item first --
                array_shift($matches);
                $_GET['_url_params'] = $matches;

                // -- Lay cai match dau tien --
                return;
			}
		}
	}

	public function dispatch()
	{
        // -- Load URL --
        $uri = empty($_GET['_url']) ? $this->_registry->oConfig->config_values['application']['default_uri'] : $_GET['_url'];
        $_GET['_url'] = '/'.str_replace(array('//', '../'), '/', trim($uri, '/'));
        $_GET['_url_params'] = array();

		// Load pre config router
        // Loop through the route array looking for wild-cards
        if(! empty($this->_registry->oConfig->config_values['routes'])) // array();
            $this->loadPreRouter($this->_registry->oConfig->config_values['routes']);
		
		$request = NULL;
		foreach ($this->pre_request as $pre_request)
		{
            $result = $pre_request->run();
			if ($result)
			{
				$request = $result;
				break;
			}
		}
		
		if (is_null($request)) 
		{
			$request = $this->getCurrentRequest();
			
			$this->_registry->oRequest = $request;
				
			$this->_module = $request->getModule();
			$this->_controller = $request->getController();
			$this->_action = $request->getAction();
		}

        while ($request instanceof Request) {
//		while ($request) {
            $request = $request->run();
		}

        // -- Co the xu ly content html truoc khi output --
        $this->_registry->oResponse->setOutput(
            $this->_registry->oView->getContent(),
            $this->_registry->oConfig->config_values['application']['config_compression']);
	}
	
	public function getCurrentRequest()
	{
		if (empty($this->_curr_request)) {
            $this->_curr_request = new Request($_GET['_url']);
            if (!empty($_GET['_url_params']))
                $this->_curr_request->setArgs($_GET['_url_params']);
        }
		return $this->_curr_request;
	}
	
	public function getModule()
	{
		return $this->_module;
	}

	public function getController()
	{
		return $this->_controller;
	}

	public function getAction()
	{
		return $this->_action;
	}

	public function getRegistry()
	{
		return $this->_registry;
	}

	public function setRegistry($registry)
	{
		$this->_registry = $registry;
	}
} // end of class
