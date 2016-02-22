<?php

namespace App\Model\Page;

class CategoryPath extends Model 
{
	protected $_table_name = TB_PAGE_CATEGORY_PATH;
	protected $_primary_key = 'cat_id';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getCatPath($cat_id)
	{
		// MySQL Hierarchical Data Closure Table Pattern
		return $this->getRowset("cat_id = ?",array($cat_id),"`level` ASC");
	}
	
	public function insertCatPath($parent_id, $cat_id)
	{
		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;
		$rs = $this->getRowset("cat_id = ?",array($parent_id),"`level` ASC"); 
	
		foreach ($rs as $result) 
		{
			$data['cat_id'] = $cat_id;
			$data['path_id'] = $result['path_id'];
			$data['level'] = $level;
			$this->insert($data);
			$level++;
		}
	
		$data['cat_id'] = $cat_id;
		$data['path_id'] = $cat_id;
		$data['level'] = $level;
		$this->insert($data);
	}
	
	public function updateCatPath($parent_id, $cat_id)
	{
		// MySQL Hierarchical Data Closure Table Pattern
		$rs = $this->getRowset("path_id = ?",array($cat_id),"`level` ASC");
		
		if (!empty($rs)) 
		{
			foreach ($rs as $category_path) 
			{
				// Delete the path below the current one
				$this->deleteWithCondition("cat_id = ? AND level < ?", array($category_path['cat_id'], $category_path['level']));
				$path = array();
		
				// Get the nodes new parents
				$rows = $this->getRowset("cat_id = ?", array($parent_id), "level ASC");
		
				foreach ($rows as $result) {
					$path[] = $result['path_id'];
				}
		
				// Get whats left of the nodes current path
				$rows = $this->getRowset("cat_id = ?", array($category_path['cat_id']), "level ASC");
				
				foreach ($rows as $result) {
					$path[] = $result['path_id'];
				}
		
				// Combine the paths with a new level
				$level = 0;
				foreach ($path as $path_id) 
				{
					$this->runQuery("REPLACE INTO `" . TB_PAGE_CATEGORY_PATH . "` SET cat_id = '" . (int)$category_path['cat_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");
					$level++;
				}
			}
		} else 
		{
			// Delete the path below the current one
			$this->deleteWithCondition("cat_id = ?", array($cat_id));
			
			$rs = $this->getRowset("cat_id = ?",array($parent_id),"`level` ASC");
            $level = 0;
			foreach ($rs as $result)
			{
				$data['cat_id'] = $cat_id;
				$data['path_id'] = $result['path_id'];
				$data['level'] = $level;
				$this->insert($data);
				$level++;
			}
			
			$this->runQuery("REPLACE INTO `" . TB_PAGE_CATEGORY_PATH . "` SET cat_id = '" . (int)$cat_id . "', `path_id` = '" . (int)$cat_id . "', level = '" . (int)$level . "'");
		}		
		
	}	
	
	
	
	
}

?>