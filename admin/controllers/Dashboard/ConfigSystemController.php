<?php

class Dashboard_ConfigSystemController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		return $this->forward('dashboard/config-system/list');
	}	
	
	public function listAction() 
	{
		$oConfigSys = new Base_ConfigureSystem();
		$data = $oConfigSys->getAllGroups();
		
		$this->oView->arrConfigData = $data;
		$this->oView->save_link = site_url('dashboard/config-system/save');
		
		$this->renderView('dashboard/config-system/list');
	}
	
	public function saveAction($group_id)
	{
		$post = $this->oInput->_post; 
		$oConfigSys = new Base_ConfigureSystem();
		foreach ($post as $key => $value)
		{
			$num = $oConfigSys->updateConfigSystem($group_id, $key, array("value" => $value ));
		}
		
		redirect('dashboard/config-system/list');		
	}
	

}
