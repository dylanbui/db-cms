<?php

class Dashboard_GroupController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		
	}	
	
	public function listAction() 
	{
		$this->oView->box_title = "Edit Group";
		
		$objGroup = new Base_Group();
		$rsGroups = $objGroup->getRowset();
		$this->oView->rsGroups = $rsGroups;

		$this->renderView('dashboard/group/list');
	}
	
	public function addAction()
	{
		// TODO : Check validate
		
		$this->oView->box_title = "Add Group";
		
		$acls = new Base_ModuleAcls(__APP_PATH.'/config/acls.php');
		$this->oView->arrAcls = $acls->getModuleAcls();
		$this->oView->link_url = site_url('dashboard/group/add');
		$this->oView->cancel_url = site_url('dashboard/group/list');
		
		$objPageConf = new Page_Configure();
		$this->oView->rsPageConfig = $objPageConf->getRowset();
		
		if ($this->oInput->isPost())
		{
			$group_name = $this->oInput->post('group_name','');
			$role = str_replace(" ", "_", trim($group_name));
			
			$permission = $this->oInput->post('permission',NULL);
			$is_admin = $this->oInput->post('is_admin','0');
			
			// TODO : Check validate
			if ($permission == NULL)
			{
				
			}
			
			$permission = serialize($permission);
			
			$data = array(
				"role" => $role,					
				"group_name" => $group_name,
				"level" => $this->oInput->post('level','0'),
				"is_admin" => $is_admin,
				"acl_resources" => $permission					
			);
			
			$objGroup = new Base_Group();
			$last_id = $objGroup->insert($data);
		}

		$this->renderView('dashboard/group/_form');
	}
	
	public function editAction($group_id)
	{
		// TODO : Check validate
		
		$this->oView->box_title = "Edit Group";
				
		$acls = new Base_ModuleAcls(__APP_PATH.'/config/acls.php');
		$this->oView->arrAcls = $acls->getModuleAcls();
		$this->oView->link_url = site_url('dashboard/group/edit/'.$group_id);
		$this->oView->group_id = $group_id;
		$this->oView->cancel_url = site_url('dashboard/group/list');		
		
		$objPageConf = new Page_Configure();
		$this->oView->rsPageConfig = $objPageConf->getRowset();		
		
		$objGroup = new Base_Group();
		$rowGroup = $objGroup->get($group_id);

		$this->oView->rowGroup = $rowGroup;
		$this->oView->arrAclResources = unserialize($rowGroup['acl_resources']);
		
		if ($this->oInput->isPost())
		{
			$group_name = $this->oInput->post('group_name','');
			// Khong cho thay doi role
				
			$permission = $this->oInput->post('permission',NULL);
			$is_admin = $this->oInput->post('is_admin','0');
				
			// TODO : Check validate
			if ($permission == NULL)
			{
		
			}
				
			$permission = serialize($permission);
				
			$data = array(
					"group_name" => $group_name,
					"level" => $this->oInput->post('level','0'),
					"is_admin" => $is_admin,
					"acl_resources" => $permission
			);

			$objGroup->update($group_id,$data);
		}		
		
		$this->renderView('dashboard/group/_form');
	}
	
	
	public function deleteAction($group_id)
	{
		// TODO : Check validate
		
		$objGroup = new Base_Group();
		$rowGroup = $objGroup->delete($group_id);
				
		redirect("dashboard/group/list");
	}

}
