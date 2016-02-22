<?php

namespace App\Model\Base;

class Group extends Model
{
	protected $_table_name = TB_USER_GROUP;
	protected $_primary_key = 'id';	
	
	var $configure_mod = array();
	var $columns = "c.*,ln.*";
	
	public function __construct()
	{
		parent::__construct();
	}	
	
	function getAclFromGroup($str_group_id)
	{
		$rs = $this->getRowset("id IN ({$str_group_id})");
		
		$arrAcl = array();
		
		foreach ($rs as $row)
		{
			$arrTemp = unserialize($row['acl_resources']);
			if (empty($arrTemp))
				continue;

			$arrAcl = \App\Helper\ExArray::arrayMergeRecursiveDistinct($arrAcl,$arrTemp);
		}
		return $arrAcl;		
	}
	
// 	function Base_Group($configure_mod = NULL)
// 	{
// 		parent::__construct();
// 		$this->configure_mod = $configure_mod;
// 	}
			
// 	function view($type = 0, $cond = 1, $start = 0, $limit = 0, $orderby = NULL)
// 	{
// 		if(!$orderby) 
// 			$orderby = $this->configure_mod['content'][$type]['catsort_order'];
// 		$sql_order = $orderby?" ORDER BY $orderby":"";
// 		$type = is_array($type) ? " AND c.type in (".implode(",",$type).")" : " AND c.type = ".intval($type);		
// // 		$result = $this->query("SELECT ".$this->columns." FROM ".$this->prefix."category c,".$this->prefix."category_ln ln WHERE c.active = 1 AND c.id = ln.id AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' $type AND $cond $sql_order".($limit?" LIMIT $start,$limit":""));		
// 		$result = $this->query("SELECT ".$this->columns." FROM ".TB_CATEGORY." c,".TB_CATEGORY_LN." ln WHERE c.active = 1 AND c.id = ln.id AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' $type AND $cond $sql_order".($limit?" LIMIT $start,$limit":""));		
// 		return $result;
// 	}
	
// 	function counts($type= 0,$parentid = -1,$cond = 1)
// 	{		
// 		if($type>=0) 
// 			$cond .=" AND c.type = ".intval($type);
// 		if($parentid>=0) 
// 			$cond .= " AND c.parentid = ".intval($parentid);
				
// // 		$result=$this->db->mysql_results("SELECT count(c.id) FROM ".$this->prefix."category c,".$this->prefix."category_ln ln WHERE c.active = 1 AND c.id = ln.id AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' AND $cond ");
// 		$result=$this->query("SELECT count(c.id) FROM ".TB_CATEGORY." c,".TB_CATEGORY_LN." ln WHERE c.active = 1 AND c.id = ln.id AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' AND $cond ");		
// 		return $result;
// 	}
	
// 	function get($id)
// 	{		
// // 		$result = $this->query("SELECT ".$this->columns." FROM ".$this->prefix."category c inner join ".$this->prefix."category_ln ln on (c.id = ln.id) WHERE ln.ln = '".$this->lang."' AND c.id = ".intval($id));
// 		$result = $this->query("SELECT ".$this->columns." FROM ".TB_CATEGORY." c inner join ".TB_CATEGORY_LN." ln on (c.id = ln.id) WHERE ln.ln = '".$this->lang."' AND c.id = ".intval($id));		
// 		$data = $result->fetch();	
// 		return $data;
// 	}
	
// 	function getbyname_url($name_url)
// 	{		
// // 		$result = $this->query("SELECT ".$this->columns." FROM ".$this->prefix."category c inner join ".$this->prefix."category_ln ln on (c.id = ln.id) WHERE ln.ln = '".$this->lang."' AND ln.name_url = '".addslashes($name_url)."'");
// 		$result = $this->query("SELECT ".$this->columns." FROM ".TB_CATEGORY." c inner join ".TB_CATEGORY_LN." ln on (c.id = ln.id) WHERE ln.ln = '".$this->lang."' AND ln.name_url = '".addslashes($name_url)."'");		
// 		$data = $result->fetch();	
// 		return $data;
// 	}

	

}

?>