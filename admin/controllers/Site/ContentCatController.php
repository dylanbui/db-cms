<?php

class Site_ContentCatController extends BaseController
{
	private $CODE_ROOT = "CONTENT";
	
	var $_cfg_upload_file;
	var $_cfg_thumb_image;	
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		$this->_cfg_upload_file['max_size']	= '200';
		$this->_cfg_upload_file['max_width']  = '2048';
		$this->_cfg_upload_file['max_height']  = '1536';
		
		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['maintain_ratio'] = TRUE;
		$this->_cfg_thumb_image['width'] = 175;
		$this->_cfg_thumb_image['height'] = 150;		
		
	}

	public function indexAction() 
	{
		$this->listAction();
	}	
	
	public function listAction() 
	{
		$this->oView->box_title = "List Content Category";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;

		$objCat = new MainCats();
		$this->oView->arrMenuTree = $objCat->loadMenuTree($this->CODE_ROOT);
		
		$this->renderView('site/content-cat/list');
	}
	
	public function addAction()
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Add Content Category";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;
		$this->oView->link_url = site_url('site/content-cat/add');
		$this->oView->cat_id = -1;
		
		$objCat = new MainCats();		
		
		if ($this->oInput->isPost())
		{ 
			$data['parent_id'] = $this->oInput->post('parent_id', 0);
			$data['code'] = $this->oInput->post('code', '');
						
			if ((int)$data['parent_id'] == 0)
				$data['code'] = $this->CODE_ROOT;

			$data['active'] = $this->oInput->post('active', 0);
			$data['sort_order'] = $this->oInput->post('sort_order', 100);
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["name_{$code}"] = $this->oInput->post("name_{$code}");
				$data["description_{$code}"] = $this->oInput->post("description_{$code}");

				if (!empty($this->oInput->_files["image_{$code}"]['name'])) 
				{
					$file = $this->upload_files_content("image_{$code}");
					$data["image_{$code}"] = $file['file_name'];
				}
			}
			
			$data['create_at'] = now_to_mysql();
			$last_id = $objCat->insert($data);
			
			redirect("site/content-cat/list");
		}

		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml($this->CODE_ROOT,'parent_id', -1, -1, $configure_languages);
		
		$this->renderView('site/content-cat/_form');
	}
	
	public function editAction($cat_id)
	{
		$this->oView->status = "edit";
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Edit Category";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;				
		$this->oView->link_url = site_url('site/content-cat/edit/'.$cat_id);
		$this->oView->cat_id = $cat_id;
		
		$objCat = new MainCats();
		
		if ($this->oInput->isPost())
		{
			$data['parent_id'] = $this->oInput->post('parent_id', 0);
			$data['code'] = $this->oInput->post('code', '');

			if ((int)$data['parent_id'] == 0)
				$data['code'] = $this->CODE_ROOT;			
			
			$data['active'] = $this->oInput->post('active', 0);
			$data['sort_order'] = $this->oInput->post('sort_order', 100);
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["name_{$code}"] = $this->oInput->post("name_{$code}");
				$data["description_{$code}"] = $this->oInput->post("description_{$code}");

				if (!empty($this->oInput->_files["image_{$code}"]['name']))
				{
					$file = $this->upload_files_content("image_{$code}");
					$data["image_{$code}"] = $file['file_name'];
				}
			}

			$objCat->update($cat_id,$data);
		}

		$rowCat = $objCat->get($cat_id);
		
		$this->oView->rowCat = $rowCat;
		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml($this->CODE_ROOT,'parent_id', $rowCat['parent_id'], $rowCat['id'], $configure_languages);						
		
		$this->renderView('site/content-cat/_form');
	}
	
	public function deleteAction($cat_id)
	{
		// TODO : Check validate and delete image resource
		
		$objCat = new MainCats();
		$objCat = $objCat->delete($cat_id);
				
		redirect("site/content-cat/list");
	}
	
	public function deleteImageAction($cat_id,$code)
	{
		$objCat = new MainCats();

		$data["image_{$code}"] = "";
		$objCat->update($cat_id,$data);
		
		redirect("site/content-cat/edit/{$cat_id}");		
	}	

	private function upload_files_content($field)
	{
		$this->_cfg_upload_file['file_name']  = 'img_'.time();
		$uploadLib = new UploadLib($this->_cfg_upload_file);
	
		if ( ! $uploadLib->do_upload($field))
		{
			echo $uploadLib->display_errors();
			exit();
		}
		else
		{
			//$this->_cfg_thumb_image['source_image']	= $uploadLib->full_file_name;
			$this->_cfg_thumb_image['source_image']	= $uploadLib->data('full_path');
	
			$imageLib = new ImageLib($this->_cfg_thumb_image);
			if ( ! $imageLib->resize())
			{
				echo $imageLib->display_errors();
				exit();
			}
			// Thum image : $imageLib->thumb_marker.$imageLib->source_image
		}
	
		return  $uploadLib->data();
	}
	
	
}
