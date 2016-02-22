<?php

class Site_ContentController extends BaseController
{
	private $CODE_ROOT = "CONTENT";
	
	var $_cfg_upload_file;
	var $_cfg_thumb_image;
	var $_items_per_page;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_items_per_page = 10;
		
		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		$this->_cfg_upload_file['max_size']	= '2000';
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
	
	public function listAction($offset = 0, $cat_id = NULL) 
	{
		$this->oView->box_title = "List Content";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;
		
		$offset = intval($offset);
		$offset = ($offset % $this->_items_per_page != 0 ? 0 : $offset);
		
		$objContent = new Contents();
		$this->oView->rsContent = $objContent->getRowset(NULL,NULL,NULL,$offset,$this->_items_per_page);
		
		$pages = new Paginator();
		$pages->current_url = site_url('site/content/list/%d');
		$pages->offset = $offset;
		$pages->items_per_page = $this->_items_per_page;
		$pages->items_total = $objContent->getTotalRow();;
		$pages->mid_range = 8;
		$pages->paginate();
		
		$this->oView->pages = $pages;
		
		$this->renderView('site/content/list');
	}
	
	public function addAction()
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Add Content";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;
		$this->oView->link_url = site_url('site/content/add');
		$this->oView->rowContent = array();
		
		if ($this->oInput->isPost())
		{
			$objContent = new Content();
			
			if (!empty($this->oInput->_files["image_file"]['name']))
			{			
				$file_content_data = $this->upload_files_content("image_file");
				$data['image_file'] = $file_content_data['file_name'];
			}
						
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
			
			if (count($this->oInput->_files["image_gallery"]) > 0)
			{
				$file_gallery_data = $this->upload_files_gallery("image_gallery");
				
				$objGallery = new Gallery();
				$objGallery->insert($this->CODE_ROOT, $last_id, $file_gallery_data);
			}
			
			redirect("site/content/list");
		}
		
		$objCat = new MainCats();
		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml($this->CODE_ROOT,'cat_id', -1, -1, $configure_languages);

		$this->renderView('site/content/_form');
	}
	
	public function editAction($content_id)
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Edit Content";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;				
		$this->oView->link_url = site_url('site/content/edit/'.$content_id);
		$this->oView->content_id = $content_id;
		
		$objContent = new Content();
		$objGallery = new Gallery();
		
		if ($this->oInput->isPost())
		{
			if (!empty($this->oInput->_files["image_file"]['name']))
			{
				$file_content_data = $this->upload_files_content("image_file");
				$data['image_file'] = $file_content_data['file_name'];
			}			
							
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
			
			if (!empty($this->oInput->_files["image_file"]['name'][0]))
			{
				$file_gallery_data = $this->upload_files_gallery("image_gallery");
				$objGallery->insert($this->CODE_ROOT, $content_id, $file_gallery_data);
			}			
		}

		$rowContent = $objContent->get($content_id);
		$this->oView->rowContent = $rowContent;
		$this->oView->rsGalleryImages = $objGallery->getRowset("group_code = ? AND relation_id = ?",array($this->CODE_ROOT, $content_id));
		
		$objCat = new MainCats();
		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml($this->CODE_ROOT,'cat_id', $rowContent['cat_id'], -1, $configure_languages);		
		
		$this->renderView('site/content/_form');
	}
	
	public function deleteAction($cat_id)
	{
		// TODO : Check validate and delete image resource
		
		$objContent = new Content();
		$objContent->delete($cat_id);
				
		redirect("site/content/list");
	}
	
	public function deleteImageAction($content_id)
	{
		$objContent = new Content();
		
		$data["image_file"] = "";
		$objContent->update($content_id,$data);
	
		redirect("site/content/edit/{$content_id}");
	}

	public function deleteGalleryImageAction($content_id, $gallery_id)
	{
		$objGallery = new Gallery();
		$objGallery->delete($gallery_id);
	
		redirect("site/content/edit/{$content_id}");
	}	
	
	private function upload_files_gallery($field)
	{
		$this->_cfg_upload_file['file_name']  = 'img_'.time();
		$uploadLib = new UploadLib($this->_cfg_upload_file);
		
		$returnValue = $uploadLib->do_multi_upload($field);
			
		if (empty($returnValue))
		{
			echo $uploadLib->display_errors();
			exit();
		}
		else
		{
			foreach ($returnValue as $fileData)
			{
				$this->_cfg_thumb_image['source_image']	= $fileData['full_path'];
		
				$imageLib = new ImageLib($this->_cfg_thumb_image);
				if ( ! $imageLib->resize())
				{
					echo $imageLib->display_errors();
					exit();
				}
			}
		}

		return $returnValue;
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
			// 				$this->_cfg_thumb_image['source_image']	= $uploadLib->full_file_name;
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
