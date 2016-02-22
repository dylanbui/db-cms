<?php

class Amway_DashboardController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{		
	    $this->showAction();
	}	
	
	public function showAction($defautl_tab = "question-tab") 
	{
		$this->oView->box_title = "Dashboard";
		$this->oView->box_question_action = "Danh Sách câu hỏi mới";
		$this->oView->defautl_tab = $defautl_tab;
				
		$objQuestion = new Amway_Question();
		$this->oView->rsQuestion = $objQuestion->getRowset("active = 0", NULL,"last_update DESC");
				
		$objComment = new Amway_Comment();
		$this->oView->rsComment = $objComment->getAllComment(FALSE);
		
		$this->renderView('amway/dashboard/show');
	}
	
	public function commentAction()
	{
		$this->oView->box_title = "Dashboard";
		$this->oView->box_question_action = "Danh Sách câu hỏi mới";
	
		$objQuestion = new Amway_Question();
		$this->oView->rsQuestion = $objQuestion->getRowset("active = 0", NULL,"last_update DESC");
	
		$objComment = new Amway_Comment();
		$this->oView->rsComment = $objComment->getRowset("active = 0", NULL,"last_update DESC");
	
		$this->renderView('amway/dashboard/show');
	}	
	

}
