<?php

class Dashboard_UserController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		// TODO : Check login - to do late 		
// 		if(!$this->isLogged())
// 			redirect('dashboard/member/login');
			
	    return $this->forward('dashboard/user/list');
	}	
	
	public function listAction() 
	{
		$this->oView->title = 'PHP MVC Framework';
		
		$objUser = new Base_User();
		
		$rsUsers = $objUser->getAllUser();
		
		$this->oView->rsUsers = $rsUsers;		
		
		$this->renderView('dashboard/user/list');
	}
	
	public function addAction()
	{
		$this->oView->box_title = "Add New User";
		$this->oView->link_url = site_url('dashboard/user/add');		
		$this->oView->cancel_url = site_url('dashboard/user/list');
		
		$objGroup = new Base_Group();
		$rsGroups = $objGroup->getRowset();
		$this->oView->rsGroups = $rsGroups;

		if ($this->oInput->isPost()) 
		{
			// TODO : Check validate
			$group_id = $this->oInput->post("group_id",array());
			$group_id = implode(",", $group_id);
			
			$data = array(
				"display_name" => $this->oInput->post("display_name",""),		
				"email" => $this->oInput->post("email",""),
				"password" => md5($this->oInput->post("pw","")),
				"group_id" => $group_id,
				"activated" => $this->oInput->post("activated",0)					
			);
			
			// TODO : Notify save successfully
			$oUser = new Base_User();
			$oUser->insert($data);			
		}
		
		$this->renderView('dashboard/user/add');
	}
	
	public function editAction($user_id)
	{
		// TODO : Check validate $user_id
				
		$this->oView->box_title = "Update User";
		$this->oView->link_url = site_url('dashboard/user/edit/'.$user_id);
		$this->oView->cancel_url = site_url('dashboard/user/list');
	
		$objGroup = new Base_Group();
		$rsGroups = $objGroup->getRowset();
		$this->oView->rsGroups = $rsGroups;
		
		$oUser = new Base_User();
		
		if ($this->oInput->isPost())
		{
			// TODO : Check validate
			$group_id = $this->oInput->post("group_id",array());
			$group_id = implode(",", $group_id);
			
			$data = array(
				"display_name" => $this->oInput->post("display_name",""),
				"email" => $this->oInput->post("email",""),
				"group_id" => $group_id,
				"activated" => $this->oInput->post("activated",0)
			);			
			
			$reset_password = $this->oInput->post('reset_password',NULL);
			if ($reset_password != NULL)
			{
				// TODO : Check validate password
				$data['password'] = md5($this->oInput->post("pw",""));  
			}
				
			// TODO : Notify save successfully
			$oUser->update($user_id,$data);
		}		
		
		$rowUser = $oUser->get($user_id);
		
		$this->oView->rowUser = $rowUser;
		$this->oView->arrGroupIds = explode(",",$rowUser['group_id']);
		
		$this->renderView('dashboard/user/edit');
	}
	
	public function deleteAction($user_id)
	{
		// TODO : Check validate $user_id
		$oUser = new Base_User();
		$oUser->delete($user_id);
		redirect("dashboard/user/list");
	}
	
	public function activateAction($user_id)
	{
		// TODO : Check validate $user_id
		$oUser = new Base_User();
		$rowUser = $oUser->get($user_id);
		
		$data = array(
			"activated" => ($rowUser['activated'] == 0 ? 1 : 0)
		);
		
		// TODO : Notify save successfully
		$oUser->update($user_id,$data);		
		redirect("dashboard/user/list");	
	}	

}
