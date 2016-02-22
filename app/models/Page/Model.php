<?php

namespace App\Model\Page;

class Model extends \App\Lib\Core\Model
{
	var $lang;
		
	function __construct()
	{
		parent::__construct();
	}

	function setLang($module = 0, $typeid = 0, $configure_mod)
	{
		if($configure_mod[$module][$typeid]['languages'])
			return $configure_mod['default_lang'];
		return $configure_mod['default_global_lang'];		
	}
	
	function setCharset($charset = "UTF8")
	{
		$this->query("SET NAMES '$charset'");
	}
	

}

?>