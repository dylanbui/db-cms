<?php

class Amway_QuestionController extends BaseController
{
	
	public function __construct()
	{
		parent::__construct();
		$this->_items_per_page = 50;
	}

	public function indexAction() 
	{
		$this->listAction();		
	}	
	
	public function listAction($cat_id = 0, $offset = 0) 
	{
		$this->oView->box_title = "Danh Sách Câu Hỏi";
		$this->oView->box_action = "Danh Sách";
		
		$objCat = new Amway_Category();		
		$this->oView->rsCats = $objCat->getRowset("active = 1", NULL,"sort_order ASC");;
		
		$offset = intval($offset);
		$offset = ($offset % $this->_items_per_page != 0 ? 0 : $offset);
		
		$cat_id = intval($cat_id);
		$this->oView->cat_id = $cat_id;
		
		$objContent = new Amway_Question();
		if ($cat_id > 0)
			$rsContent = $objContent->getRowset("cat_id = ?", array($cat_id),"create_at DESC",$offset,$this->_items_per_page);
		else
			$rsContent = $objContent->getRowset(NULL, NULL,"create_at DESC",$offset,$this->_items_per_page);
		$this->oView->rsContent = $rsContent;
		
		$pages = new Paginator();
		$pages->current_url = site_url("amway/question/list/{$cat_id}/%d");
		$pages->offset = $offset;
		$pages->items_per_page = $this->_items_per_page;
		$pages->items_total = $objContent->getTotalRow();;
		$pages->mid_range = 8;
		$pages->paginate();
		
		$this->oView->pages = $pages;
		
		$this->renderView('amway/question/list');
	}
	
	public function addAction()
	{
		$this->oView->box_title = "Danh Sách Câu Hỏi";
		$this->oView->box_action = "Thêm mới";
		
		$this->oView->link_url = site_url('amway/question/add');
		$this->oView->rowContent = array();
		
		$objCat = new Amway_Category();
		$this->oView->rsCats = $objCat->getRowset();

		$objMember = new Member();
		$this->oView->rsMembers = $objMember->getRowset();		
		
		if ($this->oInput->isPost())
		{
			$objContent = new Amway_Question();
			
			$data['cat_id'] = $this->oInput->post('cat_id' ,0);
			$data['member_id'] = $this->oInput->post('member_id' ,0);
			
			$data['active'] = $this->oInput->post('active' ,0);
			$data['sort_order'] = $this->oInput->post('sort_order' ,0);
			
			$data["question"] = $this->oInput->post("question");
			$data["answer"] = $this->oInput->post("answer");
			
			$data['create_at'] = now_to_mysql();
			
			$last_id = $objContent->insert($data);
			
			redirect("amway/question/list");
		}
		

		$this->renderView('amway/question/_form');
	}
	
	public function editAction($content_id)
	{
		$this->oView->box_title = "Danh Sách Câu Hỏi";
		$this->oView->box_action = "Chỉnh Sửa";
		
		$this->oView->link_url = site_url('amway/question/edit/'.$content_id);
		$this->oView->content_id = $content_id;
		
		$objCat = new Amway_Category();
		$this->oView->rsCats = $objCat->getRowset();

		$objMember = new Member();
		$this->oView->rsMembers = $objMember->getRowset();		
		
		$objContent = new Amway_Question();
		$rowContent = $objContent->get($content_id);
		$this->oView->rowContent = $rowContent;
				
		if ($this->oInput->isPost())
		{
			$data['cat_id'] = $this->oInput->post('cat_id' ,0);
			$data['active'] = $this->oInput->post('active' ,0);
			$data['sort_order'] = $this->oInput->post('sort_order' ,0);
			
			$data["question"] = $this->oInput->post("question");
			$data["answer"] = $this->oInput->post("answer");
						
			$objContent->update($content_id,$data);
			
			if (!empty($data["answer"]) && $data['active'] == 1)
			{
				$rowContent['answer'] = $data["answer"];
				$this->post_notify_to_fb($rowContent);
			}
			
			redirect("amway/question/list");
		}
		
		$this->renderView('amway/question/_form');
	}
	
	public function deleteAction($content_id)
	{
		$objContent = new Amway_Question();
		$objContent->delete($content_id);
		
		$this->delete_comment_of_content($content_id);
		
		$objComment = new Amway_Comment();
		$objComment->deleteWithCondition("question_id = ?", array($content_id));		
				
		redirect("amway/question/list");
	}
	
	private function delete_comment_of_content($content_id)
	{
		
	}

	private function post_notify_to_fb($rowQuestion)
	{
		$objMember = new Member();
		$rowMember = $objMember->get($rowQuestion['member_id']);
		
		if (!empty($rowMember['fbid']))
		{
			$content = $this->oConfigureSystem['facebook_notification_title'];

			$facebook = new Facebook(array(
					'appId'  => $this->oConfigureSystem['facebook_appid'],
					'secret' => $this->oConfigureSystem['facebook_appsecret'],
					'grant_type' => 'client_credentials'
			));
			
			$params = array(
					'access_token' => $this->oConfigureSystem['facebook_appid']."|".$this->oConfigureSystem['facebook_appsecret'],
					'template' => $content,
					'ref' => "" // Param gui wa url
			);
			
			$post = $facebook->api('/' . $rowMember['fbid'] . '/notifications/', 'post' ,$params);			
		}
				
	}
	
	public function fbPostAction()
	{
		$fbid = "808258752519605";
	
		$content = $this->oConfigureSystem['facebook_notification_title'];
	
		$facebook = new Facebook(array(
				'appId'  => $this->oConfigureSystem['facebook_appid'],
				'secret' => $this->oConfigureSystem['facebook_appsecret'],
				'grant_type' => 'client_credentials'
		));
			
		$params = array(
				'access_token' => $this->oConfigureSystem['facebook_appid']."|".$this->oConfigureSystem['facebook_appsecret'],
				'template' => $content,
				'ref' => "" // Param gui wa url
		);
			
		$post = $facebook->api('/' . $fbid . '/notifications/', 'post' ,$params);
	
		echo "<pre>";
		print_r($post);
		echo "</pre>";
		exit();
	
	}
	

}
