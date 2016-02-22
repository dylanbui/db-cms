<?php

namespace Admin\Controller\Home;

use App\Model\Base\User,
	App\Model\Base\Group;	

class UserController extends \Admin\Controller\AdminController	
{

	public function __construct()
	{
		parent::__construct();
		$this->detectModifyPermission('home/user');
		$this->oView->_isModify = $this->_isModify;
	}

	public function indexAction() 
	{
	    return $this->forward('home/user/list');
	}	
	
	public function listAction() 
	{
		$objUser = new User();
		$rsUsers = $objUser->getRowset();
		$this->oView->rsUsers = $rsUsers;
		$this->renderView('home/user/list');
	}
	
	public function addAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->oView->box_title = "Add New User";
		$this->oView->link_url = site_url('home/user/add');		
		$this->oView->cancel_url = site_url('home/user/list');
		
		$objGroup = new Group();
		$rsGroups = $objGroup->getRowset();
		$this->oView->rsGroups = $rsGroups;

		if ($this->oInput->isPost()) 
		{
			// TODO : Check validate
			$group_id = $this->oInput->post("group_id",array());
			$group_id = implode(",", $group_id);
			
			$data = array(
				"username" => $this->oInput->post("username",""),					
				"display_name" => $this->oInput->post("display_name",""),		
				"email" => $this->oInput->post("email",""),
				"password" => encryption($this->oInput->post("pw","")),
				"group_id" => $group_id,
				"active" => $this->oInput->post("active",0)					
			);
			
			$oUser = new User();
			$oUser->insert($data);

			// Notify insert successfully !
			$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
					'msg_code' => "success",
					'msg_content' => "Insert successfully !"));

			redirect('home/user/list');	
		}
		
		$this->renderView('home/user/add');
	}
	
	public function editAction($user_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->oView->box_title = "Update User";
		$this->oView->link_url = site_url('home/user/edit/'.$user_id);
		$this->oView->cancel_url = site_url('home/user/list');
	
		$objGroup = new Group();
		$rsGroups = $objGroup->getRowset();
		$this->oView->rsGroups = $rsGroups;
		
		$oUser = new User();
		
		if ($this->oInput->isPost())
		{
			// TODO : Check validate
			$group_id = $this->oInput->post("group_id",array());
			$group_id = implode(",", $group_id);
			
			$data = array(
				"username" => $this->oInput->post("username",""),					
				"display_name" => $this->oInput->post("display_name",""),
				"email" => $this->oInput->post("email",""),
				"group_id" => $group_id,
				"active" => $this->oInput->post("active",0)
			);			
			
			$reset_password = $this->oInput->post('reset_password',NULL);
			if ($reset_password != NULL)
			{
				// TODO : Check validate password
				$data['password'] = encryption($this->oInput->post("pw",""));
			}
				
			$oUser->update($user_id,$data);
			
			$currentUser = $this->oAuth->currentUser();
			if ($user_id == $currentUser['id'])
			{
				if ($reset_password != NULL || $data['username'] != $currentUser['name'])
				{
					// Reseted pw or change username => logout
					// redirect("home/user/edit/".$user_id);
					redirect("common/home/logout");
				}
			}

			// Notify update successfully !
			$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
					'msg_code' => "success",
					'msg_content' => "Update successfully !"));

			redirect('home/user/list');
		}		
		
		$rowUser = $oUser->get($user_id);
		
		$this->oView->rowUser = $rowUser;
		$this->oView->arrGroupIds = explode(",",$rowUser['group_id']);
		
		$this->renderView('home/user/edit');
	}
	
	public function deleteAction($user_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');

        // -- Cannot delete current user --
        $currentUser = $this->oAuth->currentUser();
        if($user_id == $currentUser['id'])
            redirect("home/user/list");

		$oUser = new User();
		$rowAffected = $oUser->delete($user_id);
        if(!empty($rowAffected))
            // TODO : Notify delete success
            echo "success";
        else
            // TODO : Notify delete error
            echo "error";

		redirect("home/user/list");
	}
	
	public function activeAction($user_id)
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');

		$oUser = new User();
		$oUser->setActiveField($user_id);
		redirect("home/user/list");	
	}	

}
