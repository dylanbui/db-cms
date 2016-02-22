<?php

namespace Admin\Controller\Common;

use App\Lib\Core\BaseController;

class ErrorController extends BaseController
{
	// -- Tam thoi khong dung lop nay de dieu huong loi --

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
	    $this->forward('common/common/login');
	}	
	
	
	public function error404Action()
	{
// 		show_404(); // system show error
		$this->_layout_path = "admin/layout_error_404";
		$this->renderView('common/common/blank');		
	}
	
	public function error500Action()
	{
// 		show_error($message); // system show error
		$this->_layout_path = "admin/layout_error_500";
		$this->renderView('common/common/blank');
	}

	public function errorDenyAction()
	{
// 		show_error($message); // system show error		
		$this->_layout_path = "admin/layout_error_deny";
		$this->renderView('common/common/blank');
	}	
	
	
	
}
