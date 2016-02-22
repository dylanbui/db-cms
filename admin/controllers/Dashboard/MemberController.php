<?php

class Dashboard_MemberController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
	    $this->forward('dashboard/member/login');
	}	
	
	public function loginAction() 
	{
		// user : ducbui - pw : 123456 => ma hoa sha1
		if ($this->oInput->isPost()) 
		{
			$username = $this->oInput->post('username');
			$password = $this->oInput->post('password');
			
			if($this->oAuth->login($username ,$password))
			{
				$user = $this->oAuth->currentUser();
				
				echo "<pre>";
				print_r($user);
				echo "</pre>";
				exit();
				
				redirect('dashboard/panel/show');
			}
			else 
			{
				echo "<pre>";
				print_r("chung thuc sai");
				echo "</pre>";
				exit();
			}
		}else 
		{
			if ($this->oAuth->isLoggedIn()) 
			{
				redirect('dashboard/panel/show');
			}
		}

		$this->_layout_path = "admin/layout_login";
		$this->renderView('dashboard/member/login');
// 		$this->oResponse->setOutput($this->oView->fetch('dashboard/member/login'), $this->oConfig->config_values['application']['config_compression']);
	}
	
	public function logoutAction()
	{
		$this->oAuth->logout();
// 		redirect('dashboard/member/login');		
	}
	

}
