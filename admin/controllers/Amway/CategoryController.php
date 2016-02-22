<?php

class Amway_CategoryController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		
	}	
	
	public function listAction() 
	{
		$this->oView->box_title = "Phân Loại Câu Hỏi";
		$this->oView->box_action = "Danh sách";
		
		$objCat = new Amway_Category();
		$this->oView->rsCats = $objCat->getRowset(NULL, NULL,"sort_order ASC");
		
		$this->renderView('amway/category/list');
	}
	
	public function addAction()
	{
		$this->oView->box_title = "Phân Loại Câu Hỏi";
		$this->oView->box_action = "Thêm mới";
		$this->oView->link_url = site_url('amway/category/add');
		
		if ($this->oInput->isPost())
		{
			$objCat = new Amway_Category();
			
			$data['active'] = $this->oInput->post('active');
			$data['sort_order'] = $this->oInput->post('sort_order');
			
			$data["title"] = $this->oInput->post("title");
			$data["description"] = $this->oInput->post("description");
			
			$data['create_at'] = now_to_mysql();
			$last_id = $objCat->insert($data);
			
			redirect("amway/category/list");
		}

		$this->renderView('amway/category/_form');
	}
	
	public function editAction($cat_id)
	{
		$this->oView->box_title = "Phân Loại Câu Hỏi";
		$this->oView->box_action = "Chỉnh sửa";
		$this->oView->link_url = site_url('amway/category/edit/'.$cat_id);
		$this->oView->cat_id = $cat_id;
		
		$objCat = new Amway_Category();
		
		if ($this->oInput->isPost())
		{
			$data['active'] = $this->oInput->post('active');
			$data['sort_order'] = $this->oInput->post('sort_order');
			
			$data["title"] = $this->oInput->post("title");
			$data["description"] = $this->oInput->post("description");
			
			$objCat->update($cat_id,$data);
		}

		$rowCat = $objCat->get($cat_id);
		$this->oView->rowCat = $rowCat;		
		
		$this->renderView('amway/category/_form');
	}
	
	public function deleteAction($cat_id)
	{
		// TODO : Check validate and delete image resource
		
		$objCat = new Amway_Category();
		$objCat = $objCat->delete($cat_id);
				
		redirect("amway/category/list");
	}

}
