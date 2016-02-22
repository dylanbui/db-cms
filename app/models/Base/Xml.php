<?php

class Base_Xml {
	
	/**
	 * the filepath key
	 *
	 * @var string
	 */
	protected $_moduleKey = 'admin';
	
	/**
	 * the parsed site setting file
	 *
	 * @var simpleXml object
	 */
	protected $_xml;
	
	/**
	 * loads the site settings file.
	 * if $pathToSettingsFile is set then it will load this file
	 * if not it defaults to the core settings file
	 *
	 * @param string $pathToSettingsFile
	 */
	public function __construct($moduleKey = null)
	{
		parent::__construct();
		if ($moduleKey !== null) {
			$this->_moduleKey = $moduleKey;
		}
	
		//        if (!$this->fileExists($this->_moduleKey)) {
		//            //create file
		//            $xml = new SimpleXMLElement('<settings/>');
		//            $this->saveXml($this->_moduleKey, $xml);
		//        }
		$this->_xml = $this->open($this->_moduleKey);
	}
	
	/**
	 * set the specified value
	 *
	 * @param string $key
	 * @param string $value
	 */
	//    public function set($key, $value)
	//    {
	//        $this->_xml->$key = $value;
	//    }
	
	/**
	 * save the site settings
	 *
	 */
	//    public function save()
	//    {
	//        $this->saveXml($this->_moduleKey, $this->_xml);
	//    }
	
	/**
	 * get the specified value
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return (string)$this->_xml->$key;
	}
	
	/**
	 * returns the current site settings as an associative array
	 *
	 * @return array
	 */
	public function toArray()
	{
		foreach ($this->_xml as $k => $v) {
			$array[$k] = (string)$v;
		}
		return $array;
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
		$obj = new stdClass();
		foreach ($this->_xml as $k => $v) {
			$obj->$k = (string)$v;
		}
		return $obj;
	}
	
	public function toXml()
	{
		return $this->_xml;
	}	
	
	/**
	 * @todo cache is throwing a notice about a node not existing at E_STRICT
	 *
	 *
	 * @param unknown_type $filename
	 * @param unknown_type $useCache
	 * @return unknown
	 */
// 	public function open($filename, $useCache = false)
// 	{
// 		$cache = $this->_getCache();
// 		$cacheKey = $this->_getcacheKey($filename);
// 		if ($useCache && $xml = $cache->load($cacheKey)) {
// 			return $xml;
// 		} else {
// 			$where[] = $this->_db->quoteInto('tags = ?', $filename);
// 			$row = $this->fetchRow($where);
// 			if (!empty($row->data)) {
// 				$xml = simplexml_load_string($row->data);
// 				if ($useCache) {
// 					$cache->save($xml, $cacheKey);
// 				}
// 				return $xml;
// 			}
// 		}
// 	}
	
	
	
}

?>