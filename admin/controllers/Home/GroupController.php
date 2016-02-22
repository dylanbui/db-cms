<?php

namespace Admin\Controller\Home;

use App\Model\Base\Group,
	App\Model\Base\ModuleAcls;

class GroupController extends \Admin\Controller\AdminController
{

	public function __construct()
	{
		parent::__construct();
		$this->detectModifyPermission('home/group');
		$this->oView->_isModify = $this->_isModify;		
	}

	public function indexAction() 
	{
		$ignore = array(
				'common/common',
				'common/error',
				'common/home',
				'home/dashboard'
		);
		
		$permissions = array();
		
		$files = glob(__APP_PATH . '/controllers/*/*.php');
		
		foreach ($files as $file) 
		{
			$data = explode('/', dirname($file));
		
			$permission = end($data) . '/' . camelcaseToHyphen(basename($file, 'Controller.php'));
		
			if (!in_array($permission, $ignore)) {
				$permissions[] = $permission;
			}
		}

		// -- Show debug permissions --
		echo "<pre>";
		print_r($permissions);
		echo "</pre>";
		exit();
	}

	
	public function listAction() 
	{
		$this->oView->box_title = "Edit Group";
		
		$objGroup = new Group();
		$rsGroups = $objGroup->getRowset();
		$this->oView->rsGroups = $rsGroups;

		$this->renderView('home/group/list');
	}
	
	public function addAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		// TODO : Check validate
		if ($this->oInput->isPost())
		{
			$group_name = $this->oInput->post('group_name','');
			$role = str2url(trim($group_name),"_");
			
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
			
			$objGroup = new Group();
			$last_id = $objGroup->insert($data);
			
			redirect("home/group/list");
		}
		
		$this->oView->box_title = "Add Group";
		
		$acls = new ModuleAcls(__APP_PATH.'/config/acls.php');
		
		$this->oView->arrAcls = $acls->getModuleAcls();
		$this->oView->link_url = site_url('home/group/add');
		$this->oView->cancel_url = site_url('home/group/list');
		
		$objPageConf = new \App\Model\Page\Configure();
		$this->oView->rsPageConfig = $objPageConf->getRowset();
		
		$this->oView->arrAclResources = array('access'=>array(), 'modify'=>array());		

		$this->renderView('home/group/_form');
	}
	
	public function editAction($group_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		// TODO : Check validate
		$objGroup = new Group();
		
		if ($this->oInput->isPost())
		{
			$group_name = $this->oInput->post('group_name','');
            // -- Khong cho thay doi role --
			$permission = $this->oInput->post('permission',NULL);
			$is_admin = $this->oInput->post('is_admin','0');
            $active = $this->oInput->post('active','0');
				
			// TODO : Check validate
			if ($permission == NULL)
			{
		
			}
				
			$permission = serialize($permission);
				
			$data = array(
					"group_name" => $group_name,
					"level" => $this->oInput->post('level','0'),
					"is_admin" => $is_admin,
                    "active" => $active,
					"acl_resources" => $permission
			);

			$objGroup->update($group_id,$data);
			
			redirect("home/group/list");
		}		
		
		$this->oView->box_title = "Edit Group";
		
		$acls = new ModuleAcls(__APP_PATH.'/config/acls.php');
		$this->oView->arrAcls = $acls->getModuleAcls();
		$this->oView->link_url = site_url('home/group/edit/'.$group_id);
		$this->oView->group_id = $group_id;
		$this->oView->cancel_url = site_url('home/group/list');
		
		$objPageConf = new \App\Model\Page\Configure();
		$this->oView->rsPageConfig = $objPageConf->getRowset();
		
		$rowGroup = $objGroup->get($group_id);
		
		$this->oView->rowGroup = $rowGroup;
		$this->oView->arrAclResources = unserialize($rowGroup['acl_resources']);

		$this->renderView('home/group/_form');
	}
	
	public function activeAction($group_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		// TODO : Check validate
		$objGroup = new Group();
		$objGroup->setActiveField($group_id);
		redirect("home/group/list");
	}
	
	public function deleteAction($group_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		// TODO : Check validate
		$objGroup = new Group();
		$rowGroup = $objGroup->delete($group_id);
	
		redirect("home/group/list");
	}	

}
