<?php

namespace App\Model\Page;

use App\Model\Base\UrlAlias;

class Content extends Model
{
	protected $_table_name = TB_PAGE_CONTENT;
	protected $_primary_key = 'id';
	
	public $_ln_code;
	
	// --- Use for front end ---//
	function getListContentData($page_code, $cat_id = NULL, $offset = 0, $items_per_page = 0, $order_by = NULL)
	{
		$rowPageConf = $this->getPageRow($page_code);
		return $this->getListDisplay($rowPageConf['id'], $this->_ln_code, $cat_id, $offset, $items_per_page, $order_by);
	}
	
	function getRowContentData($content_id)
	{
		return $this->getRowContent($content_id, $this->_ln_code);
	}
	
	function insertContentData($page_code, $data)
	{
		$rowPageConf = $this->getPageRow($page_code);
		$arrMainField['sort_order'] = '';
		$arrMainField['active'] = 1;
		// 		$arrMainField['user_id'] = '';
		$arrLnField[$this->_ln_code] = $data;
	
		return $this->insertContent($rowPageConf['id'], $arrMainField ,$arrLnField);
// 		Array
// 		(
// 				[sort_order] =>
// 				[active] => 1
// 				[user_id] => 1
// 		)

// 		LN

// 		Array
// 		(
// 				[en] => Array
// 				(
// 						[name] => thieu nien
// 						[email] => thiennien@yahoo.com
// 						[phonenumber] => 0123456789
// 						[address] => 50 nhat tao
// 				)
// 		)
	}
	
	function updateContentData($content_id, $data)
	{
		$arrMainField['active'] = 1;
		// 		$arrMainField['user_id'] = '';
	
		$arrLnField[$this->_ln_code] = $data;
	
		$this->updateContent($content_id, $arrMainField ,$arrLnField);
	}
    // --- END : Use for front end ---//
	
	private function getPageRow($page_code)
	{
		$objPageConf = new Configure();
		$rowPageConf = $objPageConf->getRow("code = ?",array($page_code));
	
		if (empty($rowPageConf))
		{
			print_r("Khong ton tai Page");
			exit();
		}
	
		return $rowPageConf;
	}

    function getListDisplay($page_id, $ln_code, $cat_id, $where = NULL, $offset = 0, $items_per_page = 0, $order_by = NULL)
	{
		$sql = "SELECT pr.id ,pr.uniqid ,pr.visited ,pr.active ,pr.sort_order ,ci.name ,ci.last_update FROM ".TB_PAGE_CONTENT. " as pr INNER JOIN ".TB_PAGE_CONTENT_LN." as ci ";
		$sql .= " ON pr.id = ci.id ";
		$sql .= " WHERE pr.page_id = ? AND ci.ln = ?";
        $sql .= !empty($where) ? " AND ({$where})" : '';

		$params = array($page_id, $ln_code);
		if(!empty($cat_id))
		{
			$sql .= " AND pr.cat_id = ?";
			$params[] = $cat_id;
		}
		
		if(!empty($order_by))
			$sql .= " ORDER BY {$order_by}";
				
		if ($items_per_page > 0)
			$sql .= " LIMIT {$offset},{$items_per_page}";

		return $this->runQuery($sql, $params);
	}

    public function getContentTotalRow($page_id, $ln_code, $cat_id, $where = NULL)
    {
        $sql = "SELECT COUNT(*) AS TotalRow FROM ".TB_PAGE_CONTENT. " as pr INNER JOIN ".TB_PAGE_CONTENT_LN." as ci ";
        $sql .= " ON pr.id = ci.id ";
        $sql .= " WHERE pr.page_id = ? AND ci.ln = ?";
        $sql .= !empty($where) ? " AND ({$where})" : '';

        $params = array($page_id, $ln_code);
        if(!empty($cat_id))
        {
            $sql .= " AND pr.cat_id = ?";
            $params[] = $cat_id;
        }

        $row = $this->runQueryGetFirstRow($sql, $params);
        return $row['TotalRow'];
    }
	
	function getRowContent($content_id, $ln_code)
	{
		$sql = "SELECT pr.* ,ci.* FROM ".TB_PAGE_CONTENT. " as pr INNER JOIN ".TB_PAGE_CONTENT_LN." as ci ";
		$sql .= " ON pr.id = ci.id ";
		$sql .= " WHERE pr.id = ? AND ci.ln = ?";
        return $this->runQueryGetFirstRow($sql, array($content_id, $ln_code));
	}
	
	function getRowDataContent($content_id)
	{
		$rowContent = $this->get($content_id);
        if (empty($rowContent))
            return null;
		
		$sql = "SELECT ci.* FROM ".TB_PAGE_CONTENT_LN." as ci WHERE ci.id = ?";
        $arr = $this->runQuery($sql, array($content_id));
        if (empty($arr))
            return null;
		
		$rowLnContent = array();
		foreach ($arr as $row)
			$rowLnContent["{$row['ln']}"] = $row;

		$data = array();
		$data['main_field'] = $rowContent;
		$data['ln_field'] = $rowLnContent;
		return $data;
	}	

	function insertContent($page_id, $arrMainField ,$arrLnField)
	{
		// --- Enable SEO URL ---//
		$enable_seo_url = 0;
		if(isset($arrMainField['enable_seo_url']))
		{
			$enable_seo_url = $arrMainField['enable_seo_url'];
			unset($arrMainField['enable_seo_url']);			
		}
		
		$arrMainField['page_id'] = $page_id;
		$arrMainField['uniqid'] = create_uniqid(10);
		$arrMainField['create_at'] = now_to_mysql();
		// TODO : sort_order
		$id = $this->insert($arrMainField);
		
		// Update sort_order
		if(trim($arrMainField['sort_order'] == ''))
		{
			$data['sort_order'] = $id;
			$this->update($id, $data);
		}
		
		$this->_table_name = TB_PAGE_CONTENT_LN;
		$objUrlAlias = new UrlAlias();
		foreach ($arrLnField as $ln => $arrData)
		{
			// --- Create SLUG name ---//
			if (!empty($arrData['name']) && $enable_seo_url == 1)
			{
				$slug_title = \App\Helper\Text::strToUrl($arrData['name']).'.html'; // Tao slug name
				$objUrlAlias->replaceUrlAlias(array("query"=>'page_content_id='.$id.'&page_ln='.$ln, "keyword"=>$slug_title));
			}
			// --------------------------
			
			$arrData['id'] = $id;
			$arrData['ln'] = $ln;
			$arrData['create_at'] = now_to_mysql();
			$this->insert($arrData);
		}
		$this->_table_name = TB_PAGE_CONTENT;
		
		return $id;
	}
	
	function updateContent($content_id, $arrMainField ,$arrLnField)
	{
		// --- Enable SEO URL ---//
		$enable_seo_url = 0;
		if(isset($arrMainField['enable_seo_url']))
		{
			$enable_seo_url = $arrMainField['enable_seo_url'];
			unset($arrMainField['enable_seo_url']);			
		}
				
		// TODO : sort_order
		$this->update($content_id, $arrMainField);
	
		$this->_table_name = TB_PAGE_CONTENT_LN;
		$objUrlAlias = new UrlAlias();
		foreach ($arrLnField as $ln => $arrData)
		{
			// --- Create SLUG name ---//
			if (!empty($arrData['name']) && $enable_seo_url == 1)
			{
				$slug_title = \App\Helper\Text::strToUrl($arrData['name']).'.html'; // Tao slug name
				$objUrlAlias->replaceUrlAlias(array("query"=>'page_content_id='.$content_id.'&page_ln='.$ln, "keyword"=>$slug_title));
			}
			// --------------------------
			
			$this->updateWithCondition("id = ? AND ln = ?", array($content_id, $ln),$arrData);
		}
		$this->_table_name = TB_PAGE_CONTENT;
	}
	
	function deleteContent($content_id)
	{
        // -- Delete slave records --
		$sql = "DELETE FROM ".TB_PAGE_CONTENT_LN;
		$sql .= " WHERE id = ?";

		$this->runQuery($sql, array($content_id));
//        echo $this->countAffected()
        // -- Delete master records --
		return $this->delete($content_id);
	}



}

?>