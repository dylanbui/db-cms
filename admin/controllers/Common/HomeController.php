<?php

namespace Admin\Controller\Common;

use App\Lib\Core\BaseController;

class HomeController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
	    return $this->forward('common/common/login');
	}	
	
	public function loginAction() 
	{
		$this->oView->link_url = site_url('common/home/login');
		$this->oView->authentication_error = FALSE;
		
		// user : ducbui - pw : 123456 => ma hoa sha1
		if ($this->oInput->isPost()) 
		{
			$username = $this->oInput->post('username');
			$password = $this->oInput->post('password');
			
			if($this->oAuth->login($username ,$password))
			{
// 				$_SESSION['KCFINDER'] = array();
// 				$_SESSION['KCFINDER']['disabled'] = false; // Activate the uploader,
				// Cho phep truy cap KCFINDER
				// Tranh truong hop truy cap thong wa duong link cua iframe
				$this->oSession->userdata['KCFINDER']['disabled'] = false;				
				redirect($this->oConfig->config_values['application']['admin_default_uri']);				
			}
			else 
			{
				$this->oView->authentication_error = TRUE;
			}
		}else 
		{
			if ($this->oAuth->isLoggedIn()) 
			{
// 				redirect('home/dashboard/show');
				redirect($this->oConfig->config_values['application']['admin_default_uri']);				
			}
		}

		$this->_layout_path = "admin/layout_login";
		$this->renderView('common/common/blank');
	}
	
	public function logoutAction()
	{
		$this->oAuth->logout();
		redirect('common/home/login');		
	}
	
	
}
