<?php

class Dashboard_ContentController extends BaseController
{

	var $_cfg_upload_file;
	var $_cfg_thumb_image;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		$this->_cfg_upload_file['max_size']	= '200';
		$this->_cfg_upload_file['max_width']  = '1024';
		$this->_cfg_upload_file['max_height']  = '768';
		
		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['maintain_ratio'] = TRUE;
		$this->_cfg_thumb_image['width'] = 175;
		$this->_cfg_thumb_image['height'] = 150;		
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
		$this->oView->box_title = "Add Content";
		$configure_languages = $this->oConfigureModule['configure_languages'];
		$this->oView->configure_languages = $configure_languages;
		$this->oView->link_url = site_url('dashboard/content/add');
		$this->oView->rowContent = array();
		
		if ($this->oInput->isPost())
		{
			$this->_cfg_upload_file['file_name']  = 'img_'.time();
			$uploadLib = new UploadLib($this->_cfg_upload_file);
			
			if ( ! $uploadLib->do_upload("image_file"))
			{
				echo $uploadLib->display_errors();
				exit();
			}
			else
			{
				$this->_cfg_thumb_image['source_image']	= $uploadLib->full_file_name;
				
				$imageLib = new ImageLib($this->_cfg_thumb_image);
				if ( ! $imageLib->resize())
				{
					echo $imageLib->display_errors();
					exit();
				}
				// Thum image : $imageLib->thumb_marker.$imageLib->source_image 
			}			
			
			$objContent = new Content();
			
			$data['cat_id'] = $this->oInput->post('cat_id' ,0);
			$data['active'] = $this->oInput->post('active' ,0);
			$data['sort_order'] = $this->oInput->post('sort_order' ,0);
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["title_{$code}"] = $this->oInput->post("title_{$code}");
				$data["short_body_{$code}"] = $this->oInput->post("short_body_{$code}");
				$data["long_body_{$code}"] = $this->oInput->post("long_body_{$code}");
			}
			
			$data['create_at'] = now_to_mysql();
			$last_id = $objContent->insert($data);
			
			redirect("dashboard/content/list");
		}

		$this->renderView('dashboard/content/_form');
	}
	
	public function editAction($content_id)
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Edit Content";
		$configure_languages = $this->oConfigureModule['configure_languages'];
		$this->oView->configure_languages = $configure_languages;				
		$this->oView->link_url = site_url('dashboard/content/edit/'.$content_id);
		$this->oView->content_id = $content_id;
		
		$objContent = new Content();
		
		if ($this->oInput->isPost())
		{
			$data['cat_id'] = $this->oInput->post('cat_id' ,0);
			$data['active'] = $this->oInput->post('active' ,0);
			$data['sort_order'] = $this->oInput->post('sort_order' ,0);
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["title_{$code}"] = $this->oInput->post("title_{$code}");
				$data["short_body_{$code}"] = $this->oInput->post("short_body_{$code}");
				$data["long_body_{$code}"] = $this->oInput->post("long_body_{$code}");
			}
			
			$objContent->update($content_id,$data);
		
		}

		$rowContent = $objContent->get($content_id);
		
		$this->oView->rowContent = $rowContent;		
		
		$this->renderView('dashboard/content/_form');
	}
	
	public function deleteAction($cat_id)
	{
		// TODO : Check validate and delete image resource
		
		$objContent = new Content();
		$objContent->delete($cat_id);
				
		redirect("dashboard/content/list");
	}

}
