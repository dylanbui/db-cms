<?php

class ExModule_IndexController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		redirect('ex-module/content/list');
	}	
	

}
