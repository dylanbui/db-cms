<?php

class ExModule_CategoryController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		$this->listAction();		
	}	
	
	public function listAction() 
	{
		$this->oView->box_title = "Phân Loại Câu Hỏi";
		$this->oView->box_action = "Danh sách";
		
		$objCat = new Ex_ContentCats();
		$this->oView->rsCats = $objCat->getRowset(NULL, NULL,"sort_order ASC");
		
		$this->oView->arrMenuTree = $objCat->loadMenuTree();
		
		$this->renderView('ex-module/category/list');
	}
	
	public function addAction()
	{
		$this->oView->box_title = "Phân Loại Câu Hỏi";
		$this->oView->box_action = "Thêm mới";
		$this->oView->link_url = site_url('ex-module/category/add');
		
		$objCat = new Ex_ContentCats();
		
		if ($this->oInput->isPost())
		{
			$data['active'] = $this->oInput->post('active',0);
			$data['sort_order'] = $this->oInput->post('sort_order',0);
			
			$data["name"] = $this->oInput->post("name");
			$data["parent_id"] = $this->oInput->post("parent_id");
			$data['slug_title'] = $this->oInput->post("slug_title");
			
			$data['create_at'] = now_to_mysql();
			$last_id = $objCat->insert($data);
			
// 			$slug_title = $this->oInput->post("slug_title");
// 			if (!empty($slug_title))
// 			{
// 				$objUrlAlias = new Base_UrlAlias();
// 				$objUrlAlias->replaceUrlAlias(array("query"=>'ex_category_id='.$last_id, "keyword"=>$slug_title));				
// 			}
			
			redirect("ex-module/category/list");
		}
		
		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml("parent_id", 0);		

		$this->renderView('ex-module/category/_form');
	}
	
	public function editAction($cat_id)
	{
		$this->oView->box_title = "Phân Loại Câu Hỏi";
		$this->oView->box_action = "Chỉnh sửa";
		$this->oView->link_url = site_url('ex-module/category/edit/'.$cat_id);
		$this->oView->cat_id = $cat_id;
		
		$objCat = new Ex_ContentCats();
		
		if ($this->oInput->isPost())
		{
			$data['active'] = $this->oInput->post('active');
			$data['sort_order'] = $this->oInput->post('sort_order');
			
			$data['name'] = $this->oInput->post("name");
			$data["parent_id"] = $this->oInput->post("parent_id");			
			$data['slug_title'] = $this->oInput->post("slug_title");
			
			$objCat->update($cat_id,$data);
			
// 			$slug_title = $this->oInput->post("slug_title");
// 			if (!empty($slug_title)) 
// 			{
// 				$objUrlAlias = new Base_UrlAlias();
// 				$objUrlAlias->replaceUrlAlias(array("query"=>'ex_category_id='.$cat_id, "keyword"=>$slug_title));
// 			}			
		}
		
		$rowCat = $objCat->get($cat_id);
		$this->oView->rowCat = $rowCat;

		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml("parent_id", $rowCat['parent_id'], $cat_id);
		
		$this->renderView('ex-module/category/_form');
	}
	
	public function deleteAction($cat_id)
	{
		// TODO : Check validate and delete image resource
		
		$objCat = new Ex_ContentCats();
		$objCat = $objCat->delete($cat_id);
				
		redirect("ex-module/category/list");
	}

}
