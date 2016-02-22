<?php

class Base_Content extends Base_BaseModel
{
	protected $_table_name = TB_CONTENT;
	protected $_primary_key = 'id';	
	
	var $configure_mod = array();
	var $columns = "c.*,ln.*";
	
	function Base_Content($configure_mod)
	{
		parent::__construct();
		$this->configure_mod = $configure_mod;
	}
	
	function view($type = -1, $cond = 1, $start = 0, $limit = 0, $orderby = NULL)
	{
		if(!$orderby) 
			$orderby = $this->configure_mod['content'][$type]['sort_order'];
		$sql_order = $orderby ? " ORDER BY $orderby" : "";
		$cond_type = 1;
		
		if(is_array($type)) 
			$cond_type = " c.type IN (".implode(',',$type).")"; 
		elseif($type>0) 
			$cond_type = " c.type = ".intval($type);
		
		$result = $this->query("SELECT ".$this->columns." FROM ".TB_CONTENT." c,".TB_CONTENT_LN." ln WHERE c.active = 1 AND $cond_type AND c.id = ln.id AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' AND $cond $sql_order".($limit?" LIMIT $start,$limit":""));
		return $result;
	}
		
	function counts($type = -1,$cond = 1)
	{		
		$cond_type = 1;
		
		if(is_array($type)) 
			$cond_type = " c.type IN (".implode(',',$type).")"; 
		elseif($type>0) 
			$cond_type = " c.type = ".intval($type);
		
// 		return $this->db->mysql_results("SELECT count(c.id) as total FROM ".$this->prefix."content c,".$this->prefix."content_ln ln WHERE c.active = 1 AND $cond_type AND c.id = ln.id AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' AND $cond ");
		$result = $this->query("SELECT count(c.id) as total FROM ".TB_CONTENT." c,".TB_CONTENT_LN." ln WHERE c.active = 1 AND $cond_type AND c.id = ln.id AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' AND $cond ");
		$obj = $result->fetch(PDO::FETCH_OBJ);
		return $result->total;		 
	}
	
	function getContentID($type = -1, $cond = 1)
	{
		$cond_type = 1;
		
		if(is_array($type)) 
			$cond_type = " c.type IN (".implode(',',$type).")"; 
		elseif($type>0) 
			$cond_type = " c.type = ".intval($type);
		
		$sql = "SELECT  ".$this->columns."  FROM ".TB_CONTENT." c,".TB_CATEGORY_LN." ln WHERE c.active = 1 AND $cond_type AND c.id = ln.id AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' AND $cond ";
		return $sql;
	}
	
	function get($id = 0, $type = 0)
	{
		$cond_type = 1;
		
		if(is_array($type)) 
			$cond_type = " c.type IN (".implode(',',$type).")"; 
		elseif($type>0) 
			$cond_type = " c.type = ".intval($type);
		
		$result = $this->query("SELECT c.*,ln.* FROM ".TB_CONTENT." c left join ".TB_CONTENT_LN." ln on (c.id = ln.id) WHERE c.active = 1  AND ln.ln = '".$this->lang."' AND c.id = ".intval($id)." and ".$cond_type);		
		$data =  $result->fetch();			
		return $data;
	}	
	
	function options($content_id = 0, $opt_type = 0, $page='content')
	{
		$cond = "co.content_id = ".intval($content_id);
		$cond .= " AND co.options_type = ".intval($opt_type);
		
		if($page) 
			$cond .= " AND co.page = '".$page."'";
		
		$result = $this->query("SELECT o.*,ln.* FROM ".TB_CONTENT_OPTIONS." co,".TB_OPTIONS." o,".TB_OPTIONS_LN." ln WHERE o.id = co.options_id AND o.id = ln.id AND ln.`ln` = '".$this->setLang('options',$opt_type,$this->configure_mod)."' AND $cond");
		
		$options = array();
		$rs = $result->fetch();
		while($rs){
			$options[$rs['id']] = $rs;
			$rs = $result->fetch();
		}
		return $options;
	}
	
	function viewAndCate($type = -1, $cond = 1, $start = 0, $limit = 0, $orderby = NULL)
	{
		if(!$orderby) 
			$orderby = $this->configure_mod['content'][$type]['sort_order'];
		$sql_order = $orderby?" ORDER BY $orderby":"";
		$cond_type = 1;
		
		if(is_array($type)) 
			$cond_type = " c.type IN (".implode(',',$type).")"; 
		elseif($type>=0) 
			$cond_type = " c.type = ".intval($type);
		
// 		return $this->query("SELECT c.*,ln.* FROM ".$this->prefix."content c inner join ".$this->prefix."content_ln ln on (c.id = ln.id and c.active = 1) WHERE $cond_type AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' AND $cond $sql_order".($limit?" LIMIT $start,$limit":""));
		return $this->query("SELECT c.*,ln.* FROM ".TB_CONTENT." c inner join ".TB_CONTENT_LN." ln on (c.id = ln.id and c.active = 1) WHERE $cond_type AND ln.ln = '".$this->setLang('content',$type,$this->configure_mod)."' AND $cond $sql_order".($limit?" LIMIT $start,$limit":""));		
	}
	
	function updateNumView($id)
	{
// 		return $this->db->query("UPDATE ".$this->prefix."content SET visited = visited+1 WHERE id = ".intval($id));
		return $this->query("UPDATE ".TB_CONTENT."content SET visited = visited+1 WHERE id = ".intval($id));
	}
	
	function checkData($data, $tablename, $prefix = "or")
	{
		$cond = "";
		$comas = "";
		
		foreach($data as $key=>$val)
		{
			$cond .= $comas." `".$key."` = '".addslashes($val)."'";
			$comas = $prefix;
		}
		
		$result = $this->query("SELECT * FROM "._TB_PREFIX.$tablename." WHERE $cond LIMIT 1");
		return $result->fetch();
	}
	
	// 	TODO : Chua co sua lai	
	function insert($data, $tablename)
	{
		$bak_table = $this->_table_name;
		$this->_table_name = _TB_PREFIX.$tablename;
		$result = $this->insert($data);
		$this->_table_name = $bak_table;
		return $result;
	}
	
// 	TODO : Chua co sua lai
	function update($id,$data, $tablename)
	{
// 		$result =  $this->db->update($this->prefix.$tablename,$data," id = ".intval($id));
// 		return $result;
	}
		

}

?>