<?php

namespace App\Controller\Index;
use App\Lib\Core\BaseController;

class IndexController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
//		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
	}

	public function indexAction() 
	{
//		$_SESSION['test'] = 12;
		$this->oSession->userdata['test'] = 12;
	    $this->oView->title = 'Welcome to index/index/index MVC';
	    $this->renderView('site-index/home/index');
	}
	

	
}
