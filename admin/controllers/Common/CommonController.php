<?php

namespace Admin\Controller\Common;

use App\Lib\Core\FrontController;
use App\Lib\Core\BaseController;

class CommonController extends \Admin\Controller\AdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		// ket thuc chuong trinh
		// thong bao link nay khong ton tai
		// return $this->forward("common/error/error-404");
		// exit();
		throw new \Exception("Page Not Found", 404);
	}	
	
	public function checkLoginAction() 
	{
		// These pages get past permission checks
		$ignored_pages = array(
				'common/home'
		);
		
		// Check if the current page is to be ignored
		// Day la Request hien tai , khong phai request cua site (fix forward function)
// 		$route = $this->oRequest->getModule().'/'.$this->oRequest->getController();
		$curr_request = FrontController::getInstance()->getCurrentRequest();		
		$route = $curr_request->getModule().'/'.$curr_request->getController();		

		// Dont need to log in, this is an open page
		if(in_array($route, $ignored_pages))
		{
			return;
		}
		else if (!$this->oAuth->isLoggedIn())
		{
			// ket thuc chuong trinh
			// thong bao link nay khong ton tai
			// return $this->forward("common/error/error-404");
			throw new \Exception("Page Not Found", 404);
		}
		else
		{
			$this->oView->current_user = $this->oAuth->currentUser();

			$notify_msg = $this->oSession->flashdata('notify_msg');
			if (empty($notify_msg))
				$notify_msg = array('msg_title'=>NULL,'msg_content'=>NULL,'msg_code'=>NULL);
			$this->oView->notify_msg = $notify_msg;
		}		
		
	}
	
	public function checkPermissionAction()
	{
		$ignore = array(
				'common/home',
				'common/error',
				'home/dashboard',
				'page/gallery'
		);

		$curr_request = FrontController::getInstance()->getCurrentRequest();
		$route = $curr_request->getModule().'/'.$curr_request->getController();

		// Dont need to log in, this is an open page
		if(in_array($route, $ignore))
		{
			return null;
		}

		// Den dong nay thi user da login roi, ton tai session
		$current_user = $this->oAuth->currentUser();
		
		// Neu la super admin => full access
		if ($current_user['is_admin'] == 1)
			return;
		
		// Special Module 
		if ($route == 'page/content' || $route == 'page/category') 
		{
			$params = $curr_request->getArgs();
			$route = $route.(isset($params[0]) ?'/'.$params[0]:'');
		}
		
		if (!in_array($route, $ignore) && !$this->oAuth->hasPermission("access",$route)) 
		{
			// echo "access deny<pre>";
			// print_r($this->oAuth->currentUser());
			// echo "</pre>";
			// exit();
			// return $this->forward('common/error/error-deny');
			throw new \Exception("Permission denied", 500);
		}

	}
	
	
}
