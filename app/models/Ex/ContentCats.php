<?php

namespace App\Model\Ex;

use App\Lib\Core\Model;

class ContentCats extends Model 
{
	protected $_table_name = TB_EX_CONTENT_CAT;
	protected $_primary_key = 'id';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function insert($values)
	{
		$data = $values;
		unset($values['slug_title']);
		$last_id = parent::insert($values);
		
		if (!empty($data['slug_title'])) {
			$this->replaceUrlAlias($last_id, $data['slug_title']);
		}
		
		$objCatPath = new Ex_ContentCatsPath();
		$objCatPath->insertCatPath($data['parent_id'], $last_id);
		
		return $last_id;
	}
	
	public function update($id, $values)
	{
		$data = $values;
		unset($values['slug_title']);
		$result = parent::update($id, $values);
		
		if (!empty($data['slug_title'])) {
			$this->replaceUrlAlias($id, $data['slug_title']);
		}
		
		$objCatPath = new Ex_ContentCatsPath();
// 		$objCatPath->insertCatPath($data['parent_id'], $id);
		$objCatPath->updateCatPath($data['parent_id'], $id);
				
		return $result;
	}
	
	public function loadMenuTree($parent_id = 0)
	{
		$arrMenuTree = array();
	
		$sql = "SELECT * FROM ".TB_EX_CONTENT_CAT;
		$sql .= " WHERE parent_id = ? AND active = 1";
		$sql .= " ORDER BY `sort_order` ASC";
	
        $rsSubMenu = $this->runQuery($sql, array($parent_id));
	
		foreach ($rsSubMenu as $row)
		{
			$row['sub_menus'] = $this->loadMenuTree($row['id']);
			$arrMenuTree[] = $row;
		}
	
		return $arrMenuTree;
	}	
	
	private function replaceUrlAlias($id ,$slug_title)
	{
		$objUrlAlias = new Base_UrlAlias();
		$objUrlAlias->replaceUrlAlias(array("query"=>'ex_category_id='.$id, "keyword"=>$slug_title));
				
// 		$row = $this->getRow('query = ?',array($data['query']));
// 		if (empty($row))
// 			$this->insert($data);
// 		else
// 			$this->updateWithCondition("query = '".$data['query']."'",array("keyword"=>$data['keyword']));
	}
	

	public function loadMenuTreeOptionHtml($control_name ,$selected_id ,$reject_sub_menu_id = -1)
	{
		$str_html = "<select tabindex='1' name='{$control_name}' id='{$control_name}' data-placeholder='Choose a Category' class='span6'>";
	
		if(is_null($selected_id))
			$str_html .= "<option value=''>Please select...</option>";
		$sel = $selected_id == 0 ? "selected='selected'" : "";
		$str_html .= "<option value='0' {$sel}>ROOT</option>";
	
		$sql = "SELECT * FROM ".TB_EX_CONTENT_CAT;
		$sql .= " WHERE parent_id = 0 AND active = 1";
		$sql .= " ORDER BY `sort_order` ASC";
	
        $rsSubMenu = $this->runQuery($sql);
	
		$counter = 1;
		$padding = 20*$counter;
	
		foreach ($rsSubMenu as $row)
		{
			if ($row["id"] != $reject_sub_menu_id)
			{
				$sel = null;
				if ($row["id"] == $selected_id)
					$sel = "selected='selected'";

				$str_html .= "<option style='padding-left: {$padding}px' value='".$row["id"]."' {$sel}>|--&nbsp;".h($row["name"])."</option>";
				$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id ,$row ,$counter + 1);
			}
		}
        $str_html .= "</select>";
        return $str_html;
	}
	
	private function recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id = -1 ,$rowMenu = NULL, $counter)
	{
		$padding = 20*$counter;
		$str_html = "";
	
		$sql = "SELECT * FROM ".TB_EX_CONTENT_CAT;
		$sql .= " WHERE parent_id = ? AND active = 1";
		$sql .= " ORDER BY `sort_order` ASC";
	
        $rsSubMenu = $this->runQuery($sql, array($rowMenu['id']));
	
		foreach ($rsSubMenu as $row)
		{
			if ($row["id"] != $reject_sub_menu_id)
			{
				$sel = NULL;
				if ($row["id"] == $selected_id)
					$sel = "selected='selected'";
	
				$str_html .= "<option style='padding-left: {$padding}px' value='".$row["id"]."' {$sel}>|--&nbsp;".h($row["name"])."</option>";
				$str_html .= $this->recur_loadMenuTreeOptionHtml($selected_id ,$reject_sub_menu_id ,$row ,$counter + 1);
			}
		}
		return $str_html;
	}	
	
	
}

?>