<?php

class Base_Html extends Base_BaseModel
{
	protected $_table_name = TB_HTML;
	protected $_primary_key = 'id';	
	
	var $configure_mod = array();

	function Base_Html($configure_mod)
	{
		parent::__construct();
		$this->configure_mod = $configure_mod;
	}
	
	function get($id=0)
	{		
		$result = $this->query("SELECT ln.*, c.* FROM ".TB_HTML." c, ".TB_HTML_LN." ln WHERE c.id = ln.id AND ln.ln = '".$this->setLang('html',$id,$this->configure_mod)."' AND c.id = ".intval($id));
		$data  = $result->fetch();
		return $data;
	}
			

}

?>