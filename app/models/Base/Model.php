<?php

namespace App\Model\Base;

class Model extends \App\Lib\Core\Model
{
	var $lang;
		
	// function Base_BaseModel()
	public function __construct()
	{
		parent::__construct();
	}

// 	function Model(){
// 		$this->db = new DB($GLOBALS['cfg']['server'],$GLOBALS['cfg']['port'],$GLOBALS['cfg']['usr'],$GLOBALS['cfg']['psw'],$GLOBALS['cfg']['name']);
// 		$this->lang = $GLOBALS['controller']->lang;
// 		$this->prefix = $GLOBALS['cfg']['prefix'];
// 		//$this->db->cache = _ROOT.'data/cache/';
// 	}
	
// 	function setCache($dircache){
// 		$this->db->cache = $dircache;
// 	}
	
// 	function query($sql){
// 		$db = new DB($GLOBALS['cfg']['server'],$GLOBALS['cfg']['port'],$GLOBALS['cfg']['usr'],$GLOBALS['cfg']['psw'],$GLOBALS['cfg']['name']);
// 		$db->cache = $this->db->cache;
// 		return $db->query($sql);
// 	}
	
	function setLang($module = 0, $typeid = 0, $configure_mod)
	{
// 		if($configure_mod[$module][$typeid]['languages']) 
// 			return $configure_mod['default_lang'];
// 		return $this->lang;

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