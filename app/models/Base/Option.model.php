<?php

class Base_Option extends Base_BaseModel
{
	protected $_table_name = TB_OPTIONS;
	protected $_primary_key = 'id';	
	
	var $configure_mod = array();

	function Base_Option($configure_mod){
		parent::__construct();
		$this->configure_mod = $configure_mod;
	}
	
	function get($id = 0)
	{
// 		Khong biet duoc bien $info		
		$result =  $this->query("SELECT c.*,ln.* FROM ".TB_OPTIONS." c, ".TB_OPTIONS_LN." ln WHERE c.id = ln.id AND ln.ln = '".$this->setLang('options',$info['type'],$this->configure_mod)."' AND c.id = ".intval($id));
		$data = $result->fetch();
		return $data;
	}
	
	function info($id = 0)
	{
		$result =  $this->query("SELECT c.* FROM ".TB_HTML." c WHERE c.id = ".intval($id));
		$data = $result->fetch();
		//$result->cache();
		return $data;
	}
	
	function view($type = 0, $orderby = NULL, $cond = 1)
	{
		if(!$orderby) 
			$orderby = $this->configure_mod['options'][$type]['sort_order'];
		$sql_order = $orderby?" ORDER BY $orderby":"";
		
		$result =  $this->query("SELECT c.*,ln.* FROM ".TB_OPTIONS." c, ".TB_OPTIONS_LN." ln WHERE c.active=1 and c.type=".$type." and c.id = ln.id AND ln.ln = '".$this->setLang('options',$type,$this->configure_mod)."' AND ".$cond." $sql_order");
		return $result;
	}
	
	function counts($type = 0, $cond = 1)
	{
		$result = $this->db->mysql_results("SELECT count(c.id) AS TotalRow FROM ".TB_OPTIONS." c, ".TB_OPTIONS_LN." ln WHERE c.active=1 and c.type=".$type." and c.id = ln.id AND ln.ln = '".$this->setLang('options',$type,$this->configure_mod)."' AND ".$cond);
		$obj = $result->fetch(PDO::FETCH_OBJ);
		return $obj->TotalRow;			
	}
	
	function viewByContent($type = 0, $orderby = NULL, $cond=1)
	{
		if(!$orderby) 
			$orderby = $this->configure_mod['options'][$type]['sort_order'];
		$sql_order = $orderby?" ORDER BY $orderby":"";
		
		$result =  $this->query("SELECT c.*,ln.*,co.content_id FROM ".TB_OPTIONS." c inner join ".TB_OPTIONS_LN." ln on (c.id = ln.id) left join ".TB_CONTENT_OPTIONS." co on (co.options_id=c.id) WHERE c.active=1 and c.type=".$type." AND ln.ln = '".$this->setLang('options',$type,$this->configure_mod)."' AND ".$cond." $sql_order");
		return $result;
	}
	
	
	function load($content_id = 0, $options_id = 0, $options_type = 0,$page = 'content')
	{
		$cond = " `page` = '$page'";
		if($content_id) 
			$cond .= " AND content_id = ".intval($content_id);
		if($options_id) 
			$cond .= "  AND options_id = ".intval($options_id);
		if($options_type) 
			$cond .= " AND  options_type = ".intval($options_type);
		$result = $this->query("SELECT * FROM ".TB_CONTENT_OPTIONS." WHERE $cond");
		return $result;
	}	
	
}




?>