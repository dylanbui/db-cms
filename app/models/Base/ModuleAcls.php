<?php

namespace App\Model\Base;

class ModuleAcls {
	
	/**
	 * the filepath key
	 *
	 * @var string
	 */
	protected $_moduleKey = 'admin';
	
	/**
	 * the parsed site setting file
	 *
	 * @var Array object
	 */
	protected $_acls;
	
	/**
	 * loads the site settings file.
	 * if $pathToSettingsFile is set then it will load this file
	 * if not it defaults to the core settings file
	 *
	 * @param string $pathToSettingsFile
	 */
	public function __construct($acls_file_path = null)
	{
		$this->_acls = require_once($acls_file_path);
	}
	
	/**
	 * returns the current site settings as a stdClass object
	 * note that while this seems redundant (simpleXml object to a stdClass object) this has the
	 * advantage of handling the typecasting
	 *
	 * @return stdClass object
	 */
	public function toObject()
	{
// 		$obj = new stdClass();
// 		foreach ($this->_xml as $k => $v) {
// 			$obj->$k = (string)$v;
// 		}
// 		return $obj;
	}
	
	public function getModuleAcls()
	{
		return $this->_acls;
	}	
	
	
}

?>