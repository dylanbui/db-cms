<?php

namespace Admin\Controller\Home;

class DashboardController extends \Admin\Controller\AdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{		
	    return $this->forward('dashboard/panel/show');
	}	
	
	public function showAction() 
	{
		$menuInfo = $this->fullNav();
		
		$this->oView->arrConfigSystemNav = $menuInfo['config-system'];
		
		unset($menuInfo['config-system']);
		
		// echo "<pre>";
		// print_r($menuInfo);
		// echo "</pre>";
		// exit();
		
		$this->oView->menuInfo = $menuInfo;

		$this->renderView('home/dashboard/show');
	}
	
	public function formViewAction()
	{
		$this->renderView('home/dashboard/form');
	}

	public function tableViewAction()
	{
		$this->renderView('home/dashboard/table');
	}

	public function blankPageAction()
	{
		$this->renderView('home/dashboard/blank');
	}

	public function permissionFormAction()
	{
		$acls = new Base_ModuleAcls(__APP_PATH.'/config/acls.php');
		
		
		$this->renderView('home/dashboard/permission');
	}	
	
	public function renderLeftNavAction()
	{
// 		$arrPageMenus = array();
// 		$objPageConf = new Page_Configure();
// 		$rsPages = $objPageConf->getRowset("data <> ''");

// 		$objPageCat = new Page_Category();
		
// 		foreach ($rsPages as $rowPage)
// 		{
// 			$key = 'page/content/'.$rowPage['code'];
// 			if ($this->oAuth->hasPermission('access',$key))
// 			{
// 				$arrTemp = array();
// 				$arrTemp['name'] = $rowPage['name'];
// 				$arrTemp['icon'] = "icon-list-alt";
				
// 				$data = unserialize($rowPage['data']);
				
// 				$arrSub = array();
// // 				$rsCat = $objPageCat->getRowset('page_id = ?', array($rowPage['id']));
// // 				if (count($rsCat))
// 				if (df($data['use_category'], 0) == 1) 
// 				{
// 					$arrSub[] = array("icon"=>"icon-folder-open", "name" => "Category","link" => "page/category/list/".$rowPage['code']);
// 				}
// 				$arrSub[] = array("icon"=>"icon-list", "name" => "List","link" => "page/content/list/".$rowPage['code']);
// 				$arrSub[] = array("icon"=>"icon-edit","name" => "Add new","link" => "page/content/add/".$rowPage['code']);
// 				$arrTemp['sub_menus'] = $arrSub;
				
// 				$arrPageMenus[$key] = $arrTemp;
// 			}
// 		}
		
// 		$menuInfo = array();
// 		$menus = require_once(__APP_PATH.'/config/left_menus.php');
		
// 		foreach ($menus as $key => $menu)
// 		{
// 			$arrTemp = array();
// 			$arrTemp['name'] = $menu['name'];
// 			$arrTemp['icon'] = $menu['icon'];
			
// 			foreach ($menu['sub_menus'] as $sub_menu)
// 			{
// 				if ($this->oAuth->hasPermission('access',$sub_menu['key']))
// 				{
// 					$arrTemp['sub_menus'][] = $sub_menu;
// 				}
// 			}
			
// 			if (!empty($arrTemp['sub_menus']))
// 			{
// 				$menuInfo[$key] = $arrTemp;
// 			}
// 		}

// 		$menuInfo = array_merge($arrPageMenus,$menuInfo);
// 		$this->oView->menuInfo = $menuInfo;
		
		$this->oView->menuInfo = $this->fullNav();
		return $this->oView->fetch('home/dashboard/nav');
	}

	private function fullNav()
	{
		$arrPageMenus = array();
		$objPageConf = new \App\Model\Page\Configure();
		$rsPages = $objPageConf->getRowset("data <> ''");
	
		$objPageCat = new \App\Model\Page\Category();

//        echo "<pre>";
//        print_r($rsPages);
//        echo "</pre>";
//        exit();
	
		foreach ($rsPages as $rowPage)
		{
			$key = 'page/content/list/'.$rowPage['code'];
			if ($this->oAuth->hasPermission('access',$key))
			{
				$arrTemp = array();
				$arrTemp['name'] = $rowPage['name'];
				$arrTemp['icon'] = $rowPage['icon'];//"icon-list-alt";

				$data = unserialize($rowPage['data']);
	
				$arrSub = array();
				// 				$rsCat = $objPageCat->getRowset('page_id = ?', array($rowPage['id']));
				// 				if (count($rsCat))
				if (df($data['use_category'], 0) == 1)
				{
					$arrSub[] = array("icon"=>"icon-folder-open", "name" => "Category","link" => "page/category/list/".$rowPage['code']);
				}
				$arrSub[] = array("icon"=>"icon-list", "name" => "List","link" => "page/content/list/".$rowPage['code']);
				$arrSub[] = array("icon"=>"icon-edit","name" => "Add new","link" => "page/content/add/".$rowPage['code']);
				$arrTemp['sub_menus'] = $arrSub;
	
				$arrPageMenus[$key] = $arrTemp;
			}
		}


		$menuInfo = array();
		$menus = require(__APP_PATH.'/config/left_menus.php');
	
		foreach ($menus as $key => $menu)
		{
			$arrTemp = array();
			$arrTemp['name'] = $menu['name'];
			$arrTemp['icon'] = $menu['icon'];

			foreach ($menu['sub_menus'] as $sub_menu)
			{
				if ($this->oAuth->hasPermission('access',$sub_menu['key']))
				{
					$arrTemp['sub_menus'][] = $sub_menu;
				}
			}
				
			if (!empty($arrTemp['sub_menus']))
			{
				$menuInfo[$key] = $arrTemp;
			}
		}

		return array_merge($arrPageMenus,$menuInfo);
	}
	

}
