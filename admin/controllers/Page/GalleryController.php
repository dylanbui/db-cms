<?php

namespace Admin\Controller\Page;

use App\Model\Page\Configure,
	App\Model\Page\Gallery,
	App\Lib\UploadLib,
	App\Lib\ImageLib;

class GalleryController extends \Admin\Controller\AdminController
{
	var $_cfg_upload_file;

	public function __construct()
	{
		parent::__construct();
		
		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_GALLERY_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		
	}

	public function indexAction() 
	{
		$this->_layout_path = "admin/layout_iframe";
		$this->renderView('page/gallery/show');
	}
	
	public function changeAction()
	{

	}
	
	public function showAction($page_id, $content_id)
	{
		$this->oView->page_id = $page_id;
		$this->oView->content_id = $content_id;
		
		$objGallery = new Gallery();
		$rsGaImgs = $objGallery->getRowset("content_id = ? AND active = 1", array($content_id), "sort_order ASC");
		
		$strHtml = "";
		foreach ($rsGaImgs as $image)
		{
			$image['page_id'] = $page_id;
			$strHtml .= $this->oView->fetch("page/gallery/image_item",$image);
		}
		
		$this->oView->strHtml = $strHtml;
        $this->oView->numberImages = count($rsGaImgs);
		$this->renderView('page/gallery/show', "admin/layout_iframe");
	}
	
	public function uploadImageAction($page_id, $content_id)
	{
		if (!empty($this->oInput->_files["file_upload"]["name"]))
		{
			$this->_cfg_upload_file['file_name']  = 'img_'.strtolower(create_uniqid(5)).'_'.time();
			$uploadLib = new UploadLib($this->_cfg_upload_file);
	
			if (!$uploadLib->do_upload("file_upload"))
			{
				echo $uploadLib->display_errors();
				exit();
			}
				
			$returnValue = $uploadLib->data();
				
			$objPageConf = new Configure();
			$rowPageConf = $objPageConf->get($page_id);
			$data = unserialize($rowPageConf['data']);
			$arrGalleryField = $data['gallery_image'];
	
			$objGallery = new Gallery();
			$rowGallery = $objGallery->insertImageGallery($content_id, $returnValue, $arrGalleryField);
				
			$rowGallery['page_id'] = $page_id;
			echo $this->oView->fetch("page/gallery/image_item",$rowGallery);
			exit();
		}
	
		$this->renderView('page/gallery/test-upload', "admin/layout_iframe");
	}

	public function deleteImageAction($page_id, $content_id, $gallery_id)
	{
		// TODO : Validate Params
		$objGallery = new Gallery();
// 		$rowGallery = $objGallery->get($gallery_id);		
		$objGallery->deleteImageGallery($gallery_id);
		
		redirect("page/gallery/show/{$page_id}/{$content_id}");
	}

    public function deleteAjaxImageAction($page_id, $content_id, $gallery_id)
    {
        // TODO : Validate Params
        $objGallery = new Gallery();
        $objGallery->deleteImageGallery($gallery_id);
        echo "Done";
        exit();
    }

	public function sortOrderAction()
	{
		if($this->oInput->isPost())
		{
			$i = 0;
			$objGallery = new Gallery();
			foreach ($this->oInput->post('item') as $id)
			{
				$i++;
				$data['sort_order'] = $i;
				$objGallery->update($id, $data);
			}
			echo "Done";
		}
		exit();
	}
	
	public function testUploadAction($page_id, $content_id)
	{
		if (!empty($this->oInput->_files["file_upload"]["name"]))	
		{
			$this->_cfg_upload_file['file_name']  = 'img_'.time();
			$uploadLib = new UploadLib($this->_cfg_upload_file);
				
			if (!$uploadLib->do_upload("file_upload"))
			{
				echo $uploadLib->display_errors();
				exit();
			}
			
			$returnValue = $uploadLib->data();
			
			$objPageConf = new Configure();
			$rowPageConf = $objPageConf->get($page_id);
			
			$data = unserialize($rowPageConf['data']);
			$arrGalleryField = $data['gallery_image'];
				
			$objGallery = new Gallery();
			$rowGallery = $objGallery->insertImageGallery($content_id, $returnValue, $arrGalleryField);
			
			$rowGallery['page_id'] = $page_id;
			echo $this->oView->fetch("page/gallery/image_item",$rowGallery);
			exit();
		}
		
		$this->renderView('page/gallery/test-upload', "admin/layout_iframe");
	}

	private function doUploadAndCrop()
	{
		$config = array();
		$config['upload_path'] = __UPLOAD_DATA_PATH;
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name']  = 'test_'.time();
		$config['overwrite'] = TRUE;
		$config['max_size'] = 0;
		$config['max_width']  = 0;
		$config['max_height']  = 0;
	
		$uploadLib = new UploadLib($config);
	
		if (!$uploadLib->do_upload("file_upload"))
		{
			print_r($uploadLib->display_errors());
			exit();
		}
	
		$upload_data = $uploadLib->data();
		$image_config["source_image"] = $upload_data["full_path"];
		$image_config['new_image'] = $upload_data["file_path"] . 'crop_' . $upload_data['file_name'];
		$image_config['maintain_ratio'] = FALSE;		
		$image_config['quality'] = "100%";
		$image_config['width'] = 250;
		$image_config['height'] = 250;		
		
		$imageLib = new ImageLib($image_config);
		
		if(!$imageLib->resize_and_crop())
		{
			print_r($imageLib->display_errors());
			exit();
		}		
	
		return $upload_data;
	}

	
}
