<?php

class Base_Comment extends Base_BaseModel
{
	protected $_table_name = TB_COMMENT;
	protected $_primary_key = 'id';	
	
	var $configure_mod = array();

	function Base_Comment($configure_mod)
	{
		parent::__construct();
		$this->configure_mod = $configure_mod;
	}
	
	function view($page,$pageid, $parentid=0, $cond="", $orderby=" `timestamp` ASC")
	{
		$strcond = "";
		if ($pageid>0) 
			$strcond .= "c.pageid=".intval($pageid)." AND ";
		return $this->query("SELECT c.*, DATE_FORMAT(timestamp,'%d-%m-%Y %H:%i') as date FROM ".TB_COMMENT." c WHERE c.`module`='$page' AND ".$strcond." c.parent_id=".$parentid.$cond." ORDER BY ".$orderby);
	}

	function language($cond = 1)
	{
		return $this->query("SELECT * FROM ".TB_LANGUAGE." WHERE $cond ORDER BY order_id");
	}
	
	function insert($data)
	{
		return $this->insert($data);
// 		$result = $this->db->insert($this->prefix.'comment',$data);		
// 		$insert_id = $this->db->insert_id();
// 		return $insert_id;
	}
	
	function update_likes($id)
	{
		$this->query("UPDATE ".TB_COMMENT." SET likes=likes+1 WHERE id=".$id);
	}	
	
}

?>