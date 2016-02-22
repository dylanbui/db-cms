<?php

class Base_Language extends Base_BaseModel
{
	protected $_table_name = TB_LANGUAGE;
	protected $_primary_key = 'id';	
	
	var $configure_mod = array();

	function Base_Language($configure_mod)
	{
		parent::__construct();
		$this->configure_mod = $configure_mod;
	}
	
	function view($cond = 1)
	{
		return $this->query("SELECT * FROM ".TB_LANGUAGE." WHERE $cond ORDER BY order_id");
	}

}




?>