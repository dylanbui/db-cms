<?php

class Base_ConfigureModule extends Base_BaseModel
{
	protected $_table_name = TB_CONFIGURE_MODULE;
	protected $_primary_key = 'id';
	
	function Base_ConfigureModule()
	{
		parent::__construct();
	}

	function configure_mod()
	{
		$configure_mod = array();
				
		$result = $this->query("/*qc=on*/ SELECT * FROM ".TB_CONFIGURE_MODULE." WHERE 1 ORDER BY `module`,typeid");
		
		$rs = $result->fetch(PDO::FETCH_ASSOC);
		while($rs)			
		{
			$data = unserialize($rs['data']);
			$order_default = !empty($data['sort_default'])?$data['sort_default']:'order_id';
			
			if (!empty($data['sort_default_order']) && $data['sort_default_order'] == 'DESC')
				$order_default .= " ".'DESC';
			else 
				$order_default .= " ".'ASC';
			 			
			$sort_order = $order_default;
			if(!empty($data['sort_order'])) 
				$sort_order .= ",".$data['sort_order'];
			
			$catsort_order = $order_default;
			if(!empty($data['catsort_order'])) $catsort_order .= ",".$data['catsort_order'];
			
			$configure_mod[$rs['module']][$rs['typeid']] = array(
					'languages'=>intval($data['languages']),
					'sort_order'=>$sort_order,
					'catsort_order'=>$catsort_order,
			);
			$rs = $result->fetch();
		}
// 		$result->cache();

		$result = $this->query("SELECT * FROM ".TB_LANGUAGE." WHERE active = 1 ORDER BY order_id DESC");
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$config_langs = array();
		
		$languages = array();
		foreach ($result as $row)
		{
			if($row['is_default'] == 1)
				$config_langs['default_lang'] = $row['code'];
			
			$languages[$row['code']] = $row;
		}
		
		$config_langs['languages'] = $languages;
		
		// Detect single language
		$config_langs['single_lang'] = 1;
		if (count($result) > 1)
			$config_langs['single_lang'] = 0;
	
		$configure_mod['configure_languages'] = $config_langs;
		
		return $configure_mod;
	}
	

}

?>