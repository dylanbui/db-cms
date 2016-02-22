<?php

class Base_Gallery extends Base_BaseModel
{
	protected $_table_name = TB_GALLERY;
	protected $_primary_key = 'id';	
	
	var $configure_mod = array();
	
	function Base_Gallery($configure_mod)
	{
		parent::__construct();
		$this->configure_mod = $configure_mod;
	}
	
	function view($page, $pageid, $order_by = " order_id,id", $limit="")
	{
// 		return $this->query("SELECT * FROM ".$this->prefix."gallery WHERE image<>'' and page='".addslashes($page)."' AND pageid=".intval($pageid)." ORDER BY $orderby $limit");
		return $this->query("SELECT * FROM ".TB_GALLERY." WHERE image<>'' and page='".addslashes($page)."' AND pageid = ".intval($pageid)." ORDER BY $order_by $limit");
	}		
	
	function counts($page, $pageid)
	{		
// 		return $this->db->mysql_results("SELECT count(*) FROM ".$this->prefix."gallery WHERE image<>'' and page='".addslashes($page)."' AND pageid=".intval($pageid));
		$result = $this->query("SELECT count(*) AS TotalRow FROM ".TB_GALLERY." WHERE image<>'' and page='".addslashes($page)."' AND pageid=".intval($pageid));
		$obj = $result->fetch(PDO::FETCH_OBJ);
		return $obj->TotalRow;
	}
		

}

?>