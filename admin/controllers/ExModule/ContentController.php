<?php

class ExModule_ContentController extends BaseController
{
	
	public function __construct()
	{
		parent::__construct();
		$this->_items_per_page = 10;
	}

	public function indexAction() 
	{
		$this->listAction();		
	}	
	
	public function listAction($cat_id = 0, $offset = 0) 
	{
		$this->oView->box_title = "Danh Sách";
		$this->oView->box_action = "Danh Sách";
		
		$objCats = new Ex_ContentCats();		
		$this->oView->rsCats = $objCats->getRowset("active = 1", NULL,"sort_order ASC");

//        echo "<pre>";
//        print_r($objCats->getRowset("active = 1", NULL,"sort_order ASC"));
//        echo "</pre>";
//        exit();

		$offset = intval($offset);
		$offset = ($offset % $this->_items_per_page != 0 ? 0 : $offset);
		
		$cat_id = intval($cat_id);
		$this->oView->cat_id = $cat_id;
		
		$totalRow = 0;
		$objContent = new Ex_Contents();
		if ($cat_id > 0)
		{
			$rsContent = $objContent->getRowset("cat_id = ?", array($cat_id),"sort_order DESC",$offset,$this->_items_per_page);
			$totalRow = $objContent->getTotalRow("cat_id = ?", array($cat_id));
		} else
		{
			$rsContent = $objContent->getRowset(NULL, NULL,"sort_order DESC",$offset,$this->_items_per_page);
			$totalRow = $objContent->getTotalRow();
		}
		$this->oView->rsContent = $rsContent;
		
		$pages = new Paginator();
		$pages->current_url = site_url("ex-module/content/list/{$cat_id}/%d");
		$pages->offset = $offset;
		$pages->items_per_page = $this->_items_per_page;
		$pages->items_total = $totalRow;
		$pages->mid_range = 4;
		$pages->paginate();
		
		$this->oView->pages = $pages;
		
		$this->renderView('ex-module/content/list');
	}
	
	public function addAction()
	{
		$this->oView->box_title = "Danh Sách";
		$this->oView->box_action = "Thêm mới";
		
		$this->oView->link_url = site_url('ex-module/content/add');
		$this->oView->rowContent = array();
		
		$objCat = new Ex_ContentCats();
		$this->oView->rsCats = $objCat->getRowset();
		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml("cat_id", 0);

		if ($this->oInput->isPost())
		{
			$objContent = new Ex_Contents();
			
			$data['cat_id'] = $this->oInput->post('cat_id' ,0);
			$data['title'] = $this->oInput->post('title' ,0);
			
			$data['active'] = $this->oInput->post('active' ,0);
			$data['sort_order'] = $this->oInput->post('sort_order' ,0);
			
			$last_id = $objContent->insert($data);
			
			// --- Add Url Alias ---//
			$slug_title = $this->oInput->post("slug_title");
			if (!empty($slug_title))
			{
				$objUrlAlias = new Base_UrlAlias();
				$objUrlAlias->replaceUrlAlias(array("query"=>'ex_content_id='.$last_id, "keyword"=>$slug_title));
			}			
			
			redirect("ex-module/content/list");
		}

		$this->renderView('ex-module/content/_form');
	}
	
	public function editAction($cat_id, $content_id)
	{
		$this->oView->box_title = "Danh Sách Câu Hỏi";
		$this->oView->box_action = "Chỉnh Sửa";
		
		$this->oView->link_url = site_url('ex-module/content/edit/'.$cat_id.'/'.$content_id);
		$this->oView->content_id = $content_id;
		
		$objCat = new Ex_ContentCats();
		$this->oView->rsCats = $objCat->getRowset();
		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml("cat_id", $cat_id);		
		
		$objContent = new Ex_Contents();
		$rowContent = $objContent->get($content_id);
		$this->oView->rowContent = $rowContent;
				
		if ($this->oInput->isPost())
		{
			$data['cat_id'] = $this->oInput->post('cat_id' ,0);
			$data['active'] = $this->oInput->post('active' ,0);
			$data['sort_order'] = $this->oInput->post('sort_order' ,0);
			
			$data["title"] = $this->oInput->post("title");

			$objContent->update($content_id,$data);
			
			// --- Update Url Alias ---//
			$slug_title = $this->oInput->post("slug_title");
			if (!empty($slug_title))
			{
				$objUrlAlias = new Base_UrlAlias();
				$objUrlAlias->replaceUrlAlias(array("query"=>'ex_content_id='.$content_id, "keyword"=>$slug_title));
			}
			
			redirect("ex-module/content/list");
		}
		
		$this->renderView('ex-module/content/_form');
	}
	
	public function deleteAction($content_id)
	{
		$objContent = new Ex_Contents();
		$objContent->delete($content_id);
		redirect("ex-module/content/list");
	}

}
