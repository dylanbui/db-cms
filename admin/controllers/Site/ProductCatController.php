<?php

class Site_ProductCatController extends BaseController
{
	private $CODE_ROOT = "PRODUCT";

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
		$this->oView->box_title = "List Product Category";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;
		
		$objCat = new MainCats();
		$this->oView->arrMenuTree = $objCat->loadMenuTree($this->CODE_ROOT);
		
		$this->renderView('site/product-cat/list');
	}
	
	public function addAction()
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Add Content Category";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;
		$this->oView->link_url = site_url('site/product-cat/add');
		$this->oView->cat_id = -1;
		
		$objCat = new MainCats();
		
		if ($this->oInput->isPost())
		{
			$data['parent_id'] = $this->oInput->post('parent_id', 0);
			$data['code'] = $this->oInput->post('code', '-none-');
			
			if ((int)$data['parent_id'] == 0)
				$data['code'] = $this->CODE_ROOT;			
			
			$data['active'] = $this->oInput->post('active', 0);
			$data['sort_order'] = $this->oInput->post('sort_order', 100);
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["name_{$code}"] = $this->oInput->post("name_{$code}");
				$data["short_body_{$code}"] = $this->oInput->post("short_body_{$code}");
// 				$data["icon_{$code}"] = $this->oInput->post("icon_{$code}");
// 				$data["image_{$code}"] = $this->oInput->post("image_{$code}");
			}
			
			$data['create_at'] = now_to_mysql();
			$last_id = $objCat->insert($data);
			
			redirect("site/product-cat/list");
		}

		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml($this->CODE_ROOT,'parent_id', -1, TRUE, $configure_languages);
		
		$this->renderView('site/product-cat/_form');
	}
	
	public function editAction($cat_id)
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Edit Category";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;				
		$this->oView->link_url = site_url('site/product-cat/edit/'.$cat_id);
		$this->oView->cat_id = $cat_id;
		
		$objCat = new MainCats();
		
		if ($this->oInput->isPost())
		{
			$data['parent_id'] = $this->oInput->post('parent_id', 0);
			$data['code'] = $this->oInput->post('code', '-none-');
			
			if ((int)$data['parent_id'] == 0)
				$data['code'] = $this->CODE_ROOT;			
			
			$data['active'] = $this->oInput->post('active', 0);
			$data['sort_order'] = $this->oInput->post('sort_order', 100);
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["name_{$code}"] = $this->oInput->post("name_{$code}");
				$data["short_body_{$code}"] = $this->oInput->post("short_body_{$code}");
// 				$data["icon_{$code}"] = $this->oInput->post("icon_{$code}");
// 				$data["image_{$code}"] = $this->oInput->post("image_{$code}");
			}

			$objCat->update($cat_id,$data);
		}

		$rowCat = $objCat->get($cat_id);
		
		$this->oView->rowCat = $rowCat;
		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml($this->CODE_ROOT,'parent_id', $rowCat['parent_id'] ,FALSE ,$configure_languages);						
		
		$this->renderView('site/product-cat/_form');
	}
	
	public function deleteAction($cat_id)
	{
		// TODO : Check validate and delete image resource
		
		$objCat = new MainCats();
		$objCat = $objCat->delete($cat_id);
				
		redirect("site/product-cat/list");
	}

}
