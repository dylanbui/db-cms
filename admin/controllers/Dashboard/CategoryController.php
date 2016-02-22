<?php

class Dashboard_CategoryController extends BaseController
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
		$this->oView->box_title = "List Category";
		
		$configure_languages = $this->oConfigureModule['configure_languages'];
		
		$this->oView->configure_languages = $configure_languages;
		
		$objCat = new Category();
		
		$this->oView->rsCats = $objCat->getRowset();
		$this->oView->arrMenuTree = $objCat->loadMenuTree();
		
		$this->renderView('dashboard/category/list');
	}
	
	public function addAction()
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Add Category";
		$configure_languages = $this->oConfigureModule['configure_languages'];
		$this->oView->configure_languages = $configure_languages;
		$this->oView->link_url = site_url('dashboard/category/add');
		
		if ($this->oInput->isPost())
		{
			$objCat = new Category();
			
			$parent_code = $this->oInput->post('parent_code');
			$parent_id = 0;
			
			if (trim(strtolower($parent_code)) != "root") 
			{
				$row = $objCat->getRow("code = ?",array($parent_code));
				$parent_id = $row["id"]; 
			}
			
			$data['parent_id'] = $parent_id;
			$data['code'] = $this->oInput->post('code');
			$data['active'] = $this->oInput->post('active');
			$data['sort_order'] = $this->oInput->post('sort_order');
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["name_{$code}"] = $this->oInput->post("name_{$code}");
				$data["description_{$code}"] = $this->oInput->post("description_{$code}");
				$data["icon_{$code}"] = $this->oInput->post("icon_{$code}");
				$data["image_{$code}"] = $this->oInput->post("image_{$code}");
			}
			
			$data['create_at'] = now_to_mysql();
			$last_id = $objCat->insert($data);
			
			redirect("dashboard/category/list");
		}

		$this->renderView('dashboard/category/_form');
	}
	
	public function editAction($cat_id)
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Edit Category";
		$configure_languages = $this->oConfigureModule['configure_languages'];
		$this->oView->configure_languages = $configure_languages;				
		$this->oView->link_url = site_url('dashboard/category/edit/'.$cat_id);
		$this->oView->cat_id = $cat_id;
		
		$objCat = new Category();
		
		if ($this->oInput->isPost())
		{
			$parent_code = $this->oInput->post('parent_code');
			$parent_id = 0;
			
			if (trim(strtolower($parent_code)) != "root") 
			{
				$row = $objCat->getRow("code = ?",array($parent_code));
				$parent_id = $row["id"]; 
			}
			
			$data['parent_id'] = $parent_id;
			$data['code'] = $this->oInput->post('code');
			$data['active'] = $this->oInput->post('active');
			$data['sort_order'] = $this->oInput->post('sort_order');
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["name_{$code}"] = $this->oInput->post("name_{$code}");
				$data["description_{$code}"] = $this->oInput->post("description_{$code}");
				$data["icon_{$code}"] = $this->oInput->post("icon_{$code}");
				$data["image_{$code}"] = $this->oInput->post("image_{$code}");
			}

			$objCat->update($cat_id,$data);
		
		}

		$rowCat = $objCat->get($cat_id);
		
		$rowParentCat = $objCat->get($rowCat['parent_id']);
		$rowCat['parent_code'] = $rowParentCat['code'];
		if ($rowParentCat['code'] == 0) 
			$rowCat['parent_code'] = "ROOT";
		
		$this->oView->rowCat = $rowCat;		
		
		$this->renderView('dashboard/category/_form');
	}
	
	public function deleteAction($cat_id)
	{
		// TODO : Check validate and delete image resource
		
		$objCat = new Category();
		$objCat = $objCat->delete($cat_id);
				
		redirect("dashboard/category/list");
	}

}
