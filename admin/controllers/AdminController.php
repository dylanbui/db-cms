<?php

namespace Admin\Controller;

use App\Lib\Core\BaseController;

class AdminController extends BaseController
{
	protected $_isModify;

	public function __construct()
	{
		parent::__construct();		
	}
	
	protected function detectModifyPermission($route)
	{
		if ($this->oAuth->hasPermission('modify',$route))
			$this->_isModify = TRUE;
		
		return $this->_isModify;
	}

}
