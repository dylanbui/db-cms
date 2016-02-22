<?php

namespace App\Model\Page;

class Configure extends Model
{
	protected $_table_name = TB_PAGE_CONFIGURE;
	protected $_primary_key = 'id';
	
	function Page_Configure()
	{
		parent::__construct();
	}

    // -- DONT USE --
//	function configure_mod()
//	{
//		$configure_mod = array();
//
//		$result = $this->query("/*qc=on*/ SELECT * FROM ".TB_CONFIGURE_MODULE." WHERE 1 ORDER BY `module`,typeid");
//
//		$rs = $result->fetch(PDO::FETCH_ASSOC);
//		while($rs)
//		{
//			$data = unserialize($rs['data']);
//			$order_default = !empty($data['sort_default'])?$data['sort_default']:'order_id';
//
//			if (!empty($data['sort_default_order']) && $data['sort_default_order'] == 'DESC')
//				$order_default .= " ".'DESC';
//			else
//				$order_default .= " ".'ASC';
//
//			$sort_order = $order_default;
//			if(!empty($data['sort_order']))
//				$sort_order .= ",".$data['sort_order'];
//
//			$catsort_order = $order_default;
//			if(!empty($data['catsort_order'])) $catsort_order .= ",".$data['catsort_order'];
//
//			$configure_mod[$rs['module']][$rs['typeid']] = array(
//					'languages'=>intval($data['languages']),
//					'sort_order'=>$sort_order,
//					'catsort_order'=>$catsort_order,
//			);
//			$rs = $result->fetch();
//		}
//// 		$result->cache();
//
//		$result = $this->query("SELECT * FROM ".TB_LANGUAGE." WHERE active = 1 ORDER BY order_id DESC");
//		$result = $result->fetchAll(PDO::FETCH_ASSOC);
//
//		$config_langs = array();
//
//		$languages = array();
//		foreach ($result as $row)
//		{
//			if($row['is_default'] == 1)
//				$config_langs['default_lang'] = $row['code'];
//
//			$languages[$row['code']] = $row;
//		}
//
//		$config_langs['languages'] = $languages;
//
//		// Detect single language
//		$config_langs['single_lang'] = 1;
//		if (count($result) > 1)
//			$config_langs['single_lang'] = 0;
//
//		$configure_mod['configure_languages'] = $config_langs;
//
//		return $configure_mod;
//	}
	
	function loadInputSupportType($name ,$selected ,$status = false ,$params = '')
	{
		$type = array (
				'input'=>'Input',
				'checkbox'=>'Check Box',
				'radio'=>'Radio',
				'selectbox'=>'Select Box',
				'textarea'=>'Textarea',
				'tinymce'=>'TextEditor Advance',
				'simplemce'=>'TextEditor Simple',
				'simplemce1'=>'TextEditor Simple 1',
				'simplemce2'=>'TextEditor Simple 2',
				'simplemce3'=>'TextEditor Simple 3',
		);
		
		if($status) 
			$type['status'] = 'As status field';
		
		$str = '<select name="'.$name.'" '.($params?$params:'').'>';
		foreach($type as $key => $val) 
			$str .= '<option value="'.$key.'" '.($key == $selected?'selected':'').'>'.$val.'</option>';
		$str .= '</select>';
		return $str;
	}

	function loadCategoryMainFields()
	{
		return $this->fields(TB_PAGE_CATEGORY,array());
	}
	
	function loadCategoryLnFields()
	{
		return $this->fields(TB_PAGE_CATEGORY_LN,array());
	}	

	function loadContentMainFields()
	{
		return $this->fields(TB_PAGE_CONTENT,array('id','type','cat_id','user_id','visited','active','sort_order','create_at','last_update'));		
	}
	
	function loadContentLnFields()
	{
		return $this->fields(TB_PAGE_CONTENT_LN,array('id','ln','ln_icon','ln_image','create_at','last_update'));
	}
	
	function fields($table, $arrSubFields = array())
	{
		$arrTemp = array();
//		$fields_query = $this->query("SHOW FULL FIELDS FROM ". $table);
//		$result = $fields_query->fetchAll(PDO::FETCH_ASSOC);
        $result = $this->runQuery("SHOW FULL FIELDS FROM ". $table);
		foreach ($result as $row)
		{
			if($row['Comment'] == 'system_display' || $row['Comment'] == '')
				$arrTemp[] = $row;
// 			if (!in_array($row['Field'], $arrSubFields))
// 				$arrTemp[] = $row;
		}
		return $arrTemp;
	}
	
	function truncatePageModule()
	{
		// Truncate gallery data
		$this->runQuery("TRUNCATE TABLE ".TB_PAGE_CONFIGURE);
		$this->clearAllPageModuleData();
	}
	
	function clearAllPageModuleData()
	{
		// Truncate gallery data
		$this->runQuery("TRUNCATE TABLE ".TB_PAGE_GALLERY);
		
		// Truncate category data
		$this->runQuery("TRUNCATE TABLE ".TB_PAGE_CATEGORY_LN);
		$this->runQuery("TRUNCATE TABLE ".TB_PAGE_CATEGORY);
		
		// Truncate content data		
		$this->runQuery("TRUNCATE TABLE ".TB_PAGE_CONTENT_LN);
		$this->runQuery("TRUNCATE TABLE ".TB_PAGE_CONTENT);
		
		// Delete all upload images data
		$files = glob(__UPLOAD_DATA_PATH.'*'); // get all file names
		foreach($files as $file) // iterate files
		{ 
			if(is_file($file))
				unlink($file); // delete file
		}
		
		// Delete all gallery images data		
		$files = glob(__UPLOAD_GALLERY_PATH.'*'); // get all file names
		foreach($files as $file) // iterate files
		{
			if(is_file($file))
				unlink($file); // delete file
		}				
	}	
	
}

?>