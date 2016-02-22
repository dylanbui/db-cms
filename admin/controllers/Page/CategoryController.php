<?php

namespace Admin\Controller\Page;

use App\Model\Page\Category,
	App\Model\Page\Configure,
	App\Lib\UploadLib,
	App\Lib\ImageLib;

class CategoryController extends \Admin\Controller\AdminController
{
	var $_confLn;
	
	var $_cfg_upload_file;
	var $_cfg_thumb_image;	
	
	var $_arrCatMainField;
	var $_arrCatLnField;
	var $_arrCatMainImage;
	var $_arrCatLnImage;
	
	var $_page_id, $_cat_id;
	
	var $_rowPageConf;

	public function __construct()
	{
		parent::__construct();
		$this->_confLn = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $this->_confLn;

		$this->_arrCatMainField = $this->_arrCatLnField = $this->_arrCatMainImage = $this->_arrCatLnImage = array();
		
		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		$this->_cfg_upload_file['max_size']	= 0;
		$this->_cfg_upload_file['max_width']  = 0;
		$this->_cfg_upload_file['max_height']  = 0;
		
		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['maintain_ratio'] = TRUE;
		$this->_cfg_thumb_image['width'] = 175;
		$this->_cfg_thumb_image['height'] = 150;
		
		$this->_page_id = NULL;
		$this->_cat_id = NULL;		
	}

	public function indexAction() 
	{		
		return $this->forward("common/error/error-404");		
	}
	
	public function listAction($page_code)
	{
		// --- Load config page ---//
		$this->_loadConfigPage($page_code);
		
		// --- Detect Module Permission ---//
		$this->detectModifyPermission('page/category/'.$page_code);
		$this->oView->_isModify = $this->_isModify;		
				
		$this->oView->box_title = "List Content Category";
		
// 		$this->oView->page_id = $this->_page_id;
		$this->oView->page_title = "List Category";
		
// 		echo "<pre>";
// 		print_r($this->_rowPageConf);
// 		echo "</pre>";
// 		exit();
		
		$objCategory = new Category();
		$this->oView->arrMenuTree = $objCategory->loadMenuTree($this->_page_id, $this->_confLn['default_lang']);
		
// 		echo "<pre>";
// 		print_r($objCategory->loadMenuTree($this->_page_id, $this->_confLn['default_lang']));
// 		echo "</pre>";
// 		exit();
		
// 		echo "<pre>";
// 		print_r($objCategory->loadMenuTree($page_id, $this->_confLn['default_lang']));
// 		echo "</pre>";
// 		exit();
		
		$this->renderView("page/category/list");
	}

	public function addAction($page_code)
	{
		// --- Load config page ---//
		$this->_loadConfigPage($page_code);
		
		// --- Detect Module Permission ---//
		$this->detectModifyPermission('page/category/'.$page_code);
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');		
		$this->oView->_isModify = $this->_isModify;		
		
		$this->oView->page_id = $this->_page_id;
		
		$this->oView->page_title = "Add category";
		$this->oView->page_action = "add";
		$this->oView->form_link = site_url('page/category/add/'.$page_code);
		$this->oView->cancel_link = site_url('page/category/list/'.$page_code);
		
		// --- Set default value ---//
		$dataContent['main_cat_field']['sort_order'] = '';
		$dataContent['main_cat_field']['active'] = 1;
		$this->oView->dataContent = $dataContent;
		
		$objCategory = new Category();
		$this->oView->htmlCat = $objCategory->loadMenuTreeOptionHtml($this->_page_id, "main_cat_field[parent_id]", 0, -1, $this->_confLn);		
		
		if ($this->oInput->isPost()) 
		{
			// Process upload data
			$arrCatMainImageField = $this->_upload_main_image($this->_arrCatMainImage);
			$arrCatLnImageField = $this->_upload_ln_image($this->_arrCatLnImage);
				
			$arrCatMainField = $this->oInput->post('main_cat_field');
			$arrCatLnField = $this->oInput->post('ln_cat_field');
				
			$arr_temp = array();
			foreach ($this->_confLn['languages'] as $code => $row)
			{
				if (isset($arrCatLnImageField[$code])) {
					$arr_temp[$code] = array_merge($arrCatLnField[$code], $arrCatLnImageField[$code]);
				} else {
					$arr_temp[$code] = $arrCatLnField[$code];
				}
			}
				
			$arrCatLnField = $arr_temp;
			$arrCatMainField = array_merge($arrCatMainField, $arrCatMainImageField);
			
			// --- Set temp enable_seo_url variable ---//
			$arrCatMainField['enable_seo_url'] = df($this->_rowPageConf['enable_seo_url'], 0);
			
			// Insert data
			$cat_id = $objCategory->insertCategory($this->_page_id, $arrCatMainField, $arrCatLnField);
			
			// Notify insert successfully !
			$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
					'msg_code' => "success",
					'msg_content' => "Insert successfully !"));			
			redirect('page/category/list/'.$page_code);
		}
		
		$this->renderView("page/category/_form");
	}	
	
	public function updateAction($page_code, $cat_id)
	{
		// --- Load config page ---//
		$this->_loadConfigPage($page_code);
		
		// --- Detect Module Permission ---//
		$this->detectModifyPermission('page/category/'.$page_code);
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
		$this->oView->_isModify = $this->_isModify;		
		
// 		$this->oView->page_id = $this->_page_id;
		$this->oView->cat_id = $cat_id;
		
		$this->oView->page_title = "Update category";
		$this->oView->page_action = "update";
		$this->oView->form_link = site_url('page/category/update/'.$page_code.'/'.$cat_id);
		$this->oView->cancel_link = site_url('page/category/list/'.$page_code);
		
		$objCategory = new Category();
		
		// TODO : Check cat exsited
		$this->oView->dataContent = $objCategory->getRowDataCategory($cat_id);
		$this->oView->htmlCat = $objCategory->loadMenuTreeOptionHtml($this->_page_id, "main_cat_field[parent_id]", $cat_id, $cat_id, $this->_confLn);

		if ($this->oInput->isPost())
		{
			// Process upload data
			$arrCatMainImageField = $this->_upload_main_image($this->_arrCatMainImage);
			$arrCatLnImageField = $this->_upload_ln_image($this->_arrCatLnImage);
		
			$arrCatMainField = $this->oInput->post('main_cat_field');
			$arrCatLnField = $this->oInput->post('ln_cat_field');
		
			$arr_temp = array();
			foreach ($this->_confLn['languages'] as $code => $row)
			{
				$arr_temp[$code] = array_merge($arrCatLnField[$code], $arrCatLnImageField[$code]);
			}
		
			$arrCatLnField = $arr_temp;
			$arrCatMainField = array_merge($arrCatMainField, $arrCatMainImageField);
			
			// --- Set temp enable_seo_url variable ---//
			$arrCatMainField['enable_seo_url'] = df($this->_rowPageConf['enable_seo_url'], 0);			
				
			// Update data
			$objCategory->updateCategory($cat_id, $arrCatMainField, $arrCatLnField);
				
			// TODO : Notify Update successfully !
			$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
					'msg_code' => "success",
					'msg_content' => "Update successfully !"));			
			redirect('page/category/list/'.$page_code);
		}		
		
		$this->renderView("page/category/_form");
	}

	public function deleteAction($page_code, $cat_id)
	{
		// --- Detect Module Permission ---//
		$this->detectModifyPermission('page/category/'.$page_code);
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');		
		
		$objCategory = new Category();
		$objCategory->deleteCategory($cat_id);
		redirect('page/category/list/'.$page_code);
	}
	
	public function activeAction($page_code, $cat_id)
	{
		// --- Detect Module Permission ---//
		$this->detectModifyPermission('page/category/'.$page_code);
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$objCategory = new Category();
		$objCategory->activeCategory($cat_id);
		redirect('page/category/list/'.$page_code);
	}
	
	public function deleteImageAction($action, $page_code, $cat_id, $ln_code = NULL)
	{
		$objCategory = new Category();
		$row = $objCategory->get($cat_id);
		if (empty($row)) 
		{
			echo "<pre>";
			print_r('Khong ton tai cat_id =>'.$cat_id);
			echo "</pre>";
			exit();
		}
		
		$objCategory->deleteImage($action, $cat_id, $ln_code);
		redirect("page/category/update/{$page_code}/{$cat_id}");
	}
	
	private function _loadConfigPage($page_code)
	{
		$objPageConf = new Configure();
		$rowPageConf = $objPageConf->getRow("code = ?",array($page_code));
		
		if (empty($rowPageConf)) 
		{
			show_404();
// 			echo "<pre>";
// 			print_r("Khong ton tai Page");
// 			echo "</pre>";
// 			exit();
		}
		
		$data = unserialize($rowPageConf['data']);
// 		$intUseCategory = $data['use_category'];
// 		if ($intUseCategory == 0) 
// 		{
// 			echo "<pre>";
// 			print_r("Khong co su dung category");
// 			echo "</pre>";
// 			exit();
// 		}
		
		$this->_page_id = $rowPageConf['id'];
		$this->oView->rowPageConf = $this->_rowPageConf = $rowPageConf;
		
		$this->_arrCatMainField = $data['main_cat_field'];
		$this->_arrCatLnField = $data['ln_cat_field'];
		$this->_arrCatMainImage = $data['main_cat_image'];
		$this->_arrCatLnImage = $data['ln_cat_image'];
		
		$this->oView->_arrCatMainField = $this->_arrCatMainField;
		$this->oView->_arrCatLnField = $this->_arrCatLnField;
		$this->oView->_arrCatMainImage = $this->_arrCatMainImage;
		$this->oView->_arrCatLnImage = $this->_arrCatLnImage;
	}
	
	private function _upload_main_image($arrMainImage)
	{
		$data = array();
		if (df($arrMainImage['choose'],0) == 1)
		{
			// Do upload image
			if (!empty($this->oInput->_files["image"]['name']))
			{
				$file_image = $this->_do_upload_file("image", $arrMainImage['image']['width'], $arrMainImage['image']['height']);
				// Resize image
				if (df($arrMainImage['image_thumb']['choose'],0) == 1)
				{
					$this->_create_thumb_image($file_image, $arrMainImage['image_thumb']['width'], $arrMainImage['image_thumb']['height']);
				}
				$data['image'] = $file_image['file_name'];
			}
			// Do upload icon
			if (!empty($this->oInput->_files["icon"]['name']))
			{
				$file_icon = $this->_do_upload_file("icon", $arrMainImage['icon']['width'], $arrMainImage['icon']['height']);
				$data['icon'] = $file_icon['file_name'];
			}
		}
		return $data;
		// return array('icon'=>'','image'=>'');
	}
	
	private function _upload_ln_image($arrLnImage)
	{
		$data = array();
		if (df($arrLnImage['choose'],0) == 1)
		{
			foreach ($this->_confLn['languages'] as $code => $row)
			{
				// $sub_data = array('ln_icon'=>'','ln_image'=>'');
				$sub_data = array();
				if (!empty($this->oInput->_files["ln_image"]["name"][$code]["image"]))
				{
					$name_temp = 'ln_image_temp_'.$code;
					$_FILES[$name_temp]['name'] = $_FILES['ln_image']['name'][$code]['image'];
					$_FILES[$name_temp]['type'] = $_FILES['ln_image']['type'][$code]['image'];
					$_FILES[$name_temp]['tmp_name'] = $_FILES['ln_image']['tmp_name'][$code]['image'];
					$_FILES[$name_temp]['error'] = $_FILES['ln_image']['error'][$code]['image'];
					$_FILES[$name_temp]['size'] = $_FILES['ln_image']['size'][$code]['image'];
						
					$file_image = $this->_do_upload_file($name_temp, $arrLnImage['image']['width'], $arrLnImage['image']['height']);
					// Resize image
					if (df($arrLnImage['image_thumb']['choose'],0) == 1)
					{
						$this->_create_thumb_image($file_image, $arrLnImage['image_thumb']['width'], $arrLnImage['image_thumb']['height']);
					}
					$sub_data['ln_image'] = $file_image['file_name'];
				}
				// Do upload icon
				if (!empty($this->oInput->_files["ln_image"]["name"][$code]["icon"]))
				{
					$name_temp = 'ln_icon_temp_'.$code;
					$_FILES[$name_temp]['name'] = $_FILES['ln_image']['name'][$code]['icon'];
					$_FILES[$name_temp]['type'] = $_FILES['ln_image']['type'][$code]['icon'];
					$_FILES[$name_temp]['tmp_name'] = $_FILES['ln_image']['tmp_name'][$code]['icon'];
					$_FILES[$name_temp]['error'] = $_FILES['ln_image']['error'][$code]['icon'];
					$_FILES[$name_temp]['size'] = $_FILES['ln_image']['size'][$code]['icon'];
						
					$file_icon = $this->_do_upload_file($name_temp, $arrLnImage['icon']['width'], $arrLnImage['icon']['height']);
					$sub_data['ln_icon'] = $file_icon['file_name'];
				}
				$data[$code] = $sub_data;
			}
		}
		return $data;
		// return va $data = array("en" => array('ln_icon' => "", 'ln_image' => ""), "vn" => array('ln_icon' => "", 'ln_image' => ""));
	}	
	
	private function _do_upload_file($field, $s_width, $s_height)
	{
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['file_name']  = 'img_cat_'.time();
		$uploadLib = new UploadLib($this->_cfg_upload_file);
	
		if ( ! $uploadLib->do_upload($field))
		{
			echo $uploadLib->display_errors();
			exit();
		}
		else
		{
			$this->_cfg_thumb_image['create_thumb']	= FALSE;
			$this->_cfg_thumb_image['source_image']	= $uploadLib->data('full_path');
			$this->_cfg_thumb_image['width'] = $s_width;
			$this->_cfg_thumb_image['height'] = $s_height;
	
			$imageLib = new ImageLib($this->_cfg_thumb_image);
			if($s_width !== 0 && $s_height !== 0)
			{
				$image_config["source_image"] = $uploadLib->data('full_path');
				$image_config['create_thumb'] = FALSE;
				$image_config['new_image'] = $uploadLib->data('full_path');
				$image_config['maintain_ratio'] = FALSE;
				$image_config['quality'] = "100%";
				$image_config['width'] = $s_width;
				$image_config['height'] = $s_height;				
					
				$imageLib->clear();
				$imageLib->initialize($image_config);
				if(!$imageLib->resize_and_crop())
				{
					print_r($imageLib->display_errors());
					exit();
				}
			} else
			{
				if(!$imageLib->resize())
				{
					print_r($imageLib->display_errors());
					exit();
				}
					
			}
				
		}
	
		return  $uploadLib->data();
	}
	
	private function _create_thumb_image($file_image, $s_width, $s_height)
	{
		$this->_cfg_thumb_image['create_thumb']	= TRUE;
		$this->_cfg_thumb_image['source_image']	= $file_image['full_path'];
		$this->_cfg_thumb_image['width'] = $s_width;
		$this->_cfg_thumb_image['height'] = $s_height;
	
		$imageLib = new ImageLib($this->_cfg_thumb_image);
	
		if($s_width !== 0 && $s_height !== 0)
		{
			$image_config["source_image"] = $file_image['full_path'];
			$image_config['create_thumb'] = FALSE;
			$image_config['new_image'] = $file_image["file_path"] . $imageLib->thumb_marker . $file_image['file_name'];;
			$image_config['maintain_ratio'] = FALSE;
			$image_config['quality'] = "100%";
			$image_config['width'] = $s_width;
			$image_config['height'] = $s_height;			
	
			$imageLib->clear();
			$imageLib->initialize($image_config);
			if(!$imageLib->resize_and_crop())
			{
				print_r($imageLib->display_errors());
				exit();
			}
		} else
		{
			if(!$imageLib->resize())
			{
				print_r($imageLib->display_errors());
				exit();
			}
	
		}
	
	}
	
	
}
