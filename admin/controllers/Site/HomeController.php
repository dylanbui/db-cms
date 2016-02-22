<?php

class Site_HomeController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		$_SESSION['tet'] = 'Bui van tien duc';
	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER : ' . $_SESSION['tet'];
	    $this->renderView('site/home/index');
	}
	
	public function testParamsAction($value_1, $value_2, $value_3 = "value_3")
	{
		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER : ' . $_SESSION['tet'];
		$this->oView->value_1 = $value_1;
		$this->oView->value_2 = $value_2;
		$this->oView->value_3 = $value_3;
		$this->renderView('site/home/test-params');
	}	
	
	public function partRenderAction($title)
	{
	    $this->oView->title = 'Day la phan noi dung duoc render vao';
	    $this->oView->render_title = $title;
	    return $this->oView->fetch('site/home/part_render');
	}
	
	public function renderAction()
	{
		$this->oSession->userdata['c'] = 2000;
		$this->oView->title = 'Day la trang dung chuc nang renderAction --- '.$this->oSession->userdata['test'];
		$this->oView->part_render = Module::run(new Request('site/home/part-render',array('Title duoc truyen vao '.$this->oSession->userdata['c'])));
		$this->renderView('site/home/render');
	}	

}
