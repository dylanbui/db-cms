<?php

namespace Admin\Controller\Page;

use App\Model\Page\Configure;

class ConfigureController extends \Admin\Controller\AdminController
{

	public function __construct()
	{
		parent::__construct();
		$this->detectModifyPermission('page/configure');
		$this->oView->_isModify = $this->_isModify;		
	}

	public function indexAction() 
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$objPageConf = new Configure();
		
		$this->oView->copy_url = site_url('page/configure/copy/');
		$this->oView->delete_url = site_url('page/configure/delete/');
		$this->oView->update_url = site_url('page/configure/update/');
		
		$this->oView->rsPages = $objPageConf->getRowset();
		
		$this->renderView('page/configure/list');
	}
	
	public function addAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		if ($this->oInput->isPost()) 
		{
			$data['name'] = $this->oInput->post('txtName' ,0);
			$data['code'] = $this->oInput->post('txtCode' ,0);
			$data['page'] = $this->oInput->post('txtPage' ,0);
            $data['icon'] = $this->oInput->post('txtPageIcon' ,'icon-list-alt');
			
			// default display template content
			$data['display_template_file'] = 'page/content/display-table-content';
			
			$data['create_at'] = now_to_mysql();

			$objPageConf = new Configure();
			$objPageConf->insert($data);
		}
		redirect("page/configure/list");
	}

	public function updateAction($page_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->oView->link_url = site_url('page/configure/update/'.$page_id);
		$objPageConf = new Configure();
	
		if ($this->oInput->isPost())
		{
			$data['name'] = $this->oInput->post('txtName');
            $data['icon'] = $this->oInput->post('txtPageIcon');
			$data['content'] = $this->oInput->post('txtContent');
			$data['display_template_file'] = $this->oInput->post('txtDisplayDefaultTemplate');
			$data['enable_seo_url'] = $this->oInput->post('chkSeoUrl');
			
			unset($this->oInput->_post['txtName']);
			unset($this->oInput->_post['txtPageIcon']);
            unset($this->oInput->_post['txtContent']);
			unset($this->oInput->_post['txtDisplayDefaultTemplate']);
			unset($this->oInput->_post['chkSeoUrl']);
			
			$use_category = $this->oInput->post('use_category', 0);
			$this->oInput->_post['use_category'] = $use_category;
			
			$data['data'] = serialize($this->oInput->_post);
			$objPageConf->update($page_id,$data);
			
// 			echo "<pre>";
// 			print_r($_POST);
// 			echo "</pre>";
// 			echo "<br>-------------<br>";
// 			echo "<pre>";
// 			print_r($this->oInput->_post);
// 			echo "</pre>";
// 			exit();
			
			redirect("page/configure/list");
		}
	
		$rowPageConf = $objPageConf->get($page_id);
		
// 		echo "<pre>";
// 		print_r(unserialize($rowPageConf['data']));
// 		echo "</pre>";
// 		exit();
		
		$this->oView->arrMainDbFields = $objPageConf->loadContentMainFields();
		$this->oView->arrLnDbFields = $objPageConf->loadContentLnFields();
		
		$this->oView->arrMainCatDbFields = $objPageConf->loadCategoryMainFields();
		$this->oView->arrLnCatDbFields = $objPageConf->loadCategoryLnFields();
		
		$this->oView->rowPageConf = $rowPageConf;
		
		$arrMainField = $arrLnField = $arrMainImage = $arrLnImage = $arrGalImage = array();
		$intUseGallery = $intUseCategory = 0;
		$arrGalImage = array();
		$arrMainCatField = $arrMainCatImage = $arrLnCatField = $arrLnCatImage = array();
		
		if (!empty($rowPageConf['data'])) 
		{
			$data = unserialize($rowPageConf['data']);
			
			$arrMainField = $data['main_field'];
			$arrLnField = $data['ln_field'];
			$arrMainImage = $data['main_image'];
			$arrLnImage = $data['ln_image'];
			
			// Category Data
			$intUseCategory = $data['use_category'];
			$arrMainCatField = $data['main_cat_field'];
			$arrLnCatField = $data['ln_cat_field'];
			$arrMainCatImage = $data['main_cat_image'];			
			$arrLnCatImage = $data['ln_cat_image'];
			
			// Gallery Data
			$arrGalImage = $data['gallery_image'];			
		}
	
		$this->oView->arrMainField = $arrMainField;
		$this->oView->arrLnField = $arrLnField;
		$this->oView->arrMainImage = $arrMainImage;
		$this->oView->arrLnImage = $arrLnImage;
		
		$this->oView->intUseCategory = $intUseCategory;
		$this->oView->arrMainCatField = $arrMainCatField;
		$this->oView->arrLnCatField = $arrLnCatField;
		$this->oView->arrMainCatImage = $arrMainCatImage;
		$this->oView->arrLnCatImage = $arrLnCatImage;

		$this->oView->arrGalImage = $arrGalImage;
		
		$this->renderView('page/configure/_form');
	}	
	
	public function copyAction($page_id = 0)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$objPageConf = new Configure();
		$rowPageConf = $objPageConf->get($page_id);
		
		if ($this->oInput->isPost() && !empty($rowPageConf))
		{		
			$data['name'] = $this->oInput->post('txtName' ,0);
			$data['code'] = $this->oInput->post('txtCode' ,0);
			$data['page'] = $this->oInput->post('txtPage' ,0);
			$data['data'] = $rowPageConf['data'];		
			$data['create_at'] = now_to_mysql();
	
			$objPageConf->insert($data);
		}
		
		redirect("page/configure/list");
	}
	
	public function deleteAction($page_id = 0)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$objPageConf = new Configure();
		$rowPageConf = $objPageConf->get($page_id);
		
		if (!empty($rowPageConf))
		{
			// TODO : Delete relation tables
			$objPageConf->delete($page_id);
		}

		redirect("page/configure/list");
	}
	
	public function truncateAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$objPageConf = new Configure();
		$objPageConf->truncatePageModule();
		redirect("page/configure/list");		
	}

	public function clearAllRecordAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$objPageConf = new Configure();
		$objPageConf->clearAllPageModuleData();
		redirect("page/configure/list");		
	}	
	
}
