<?php

namespace Admin\Controller\Home;

use App\Lib\Paginator,
	App\Model\Member;

class MemberController extends \Admin\Controller\AdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		$this->listAction();
	}	
	
	public function listAction($offset = 0) 
	{
		$this->oView->title = 'Member';
		$this->oView->box_title = 'List Member';
		
		$items_per_page = 10;
		
		$objMember = new Member();
		
		$rsMembers = $objMember->getRowset(NULL, NULL, NULL, $offset, $items_per_page);
		$this->oView->rsMembers = $rsMembers;
		
		$pages = new Paginator();
		$pages->current_url = site_url("home/member/list/%d");
		$pages->offset = $offset;
		$pages->items_per_page = $items_per_page;
		$pages->items_total = $objMember->getTotalRow();
		$pages->mid_range = 7;
		$pages->paginate();
		
		$this->oView->pages = $pages;
		
		$this->renderView('home/member/list');
	}
	
	public function addAction()
	{
		$this->oView->title = 'Member';
		$this->oView->box_title = "Add New Member";
		$this->oView->box_action = "Add New Member";
		
		$this->oView->link_url = site_url('home/member/add');		
		$this->oView->cancel_url = site_url('home/member/list');

		if ($this->oInput->isPost()) 
		{
			// TODO : Check validate
			
			$data = array(
				"fbid" => $this->oInput->post("fbid",""),					
				"fullname" => $this->oInput->post("fullname",""),					
				"username" => $this->oInput->post("username",""),					
				"email" => $this->oInput->post("email","")
			);
			
			// TODO : Notify save successfully
			$oMember = new Member();
			$oMember->insert($data);			
		}
		
		$this->renderView('home/member/_form');
	}
	
	public function editAction($member_id)
	{
		$this->oView->title = 'Member';		
		$this->oView->box_title = "Update Member";
		$this->oView->link_url = site_url('home/member/edit/'.$member_id);
		$this->oView->cancel_url = site_url('home/member/list');
	
		$oMember = new Member();
		
		if ($this->oInput->isPost())
		{
			// TODO : Check validate
			$data = array(
				"fbid" => $this->oInput->post("fbid",""),					
				"fullname" => $this->oInput->post("fullname",""),					
				"username" => $this->oInput->post("username",""),					
				"email" => $this->oInput->post("email","")
			);
			
			// TODO : Notify save successfully
			$oMember->update($member_id,$data);
		}		
		
		$rowMember = $oMember->get($member_id);
		$this->oView->rowMember = $rowMember;
		
		$this->renderView('home/member/_form');
	}
	
	public function deleteAction($member_id)
	{
		// TODO : Check validate $member_id
		$oMember = new Member();
		$oMember->delete($member_id);
		redirect("home/member/list");
	}
	
	public function activeAction($member_id)
	{
		redirect("home/member/list");
	}
	
	

}
