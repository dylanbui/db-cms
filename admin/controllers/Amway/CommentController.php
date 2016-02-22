<?php

class Amway_CommentController extends BaseController
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		$this->listAction();		
	}	
	
	public function listAction($question_id = NULL) 
	{
		if (empty($question_id)) 
			redirect("amway/question/list");			
		
		$this->oView->box_title = "Comments";
		$this->oView->box_action = "Comments";		
		
		$objComment = new Amway_Comment();
// 		$this->oView->rsComment = $objComment->getRowset("question_id = ?", array($question_id), 'create_at DESC');
		$this->oView->rsComment = $objComment->getAllCommentOfQuestion($question_id, NULL);
		
		
		$objMember = new Member();
		$this->oView->objMember = $objMember;
		
		$objQuestion = new Amway_Question();
		$rowQuestion = $objQuestion->get($question_id);
		$this->oView->rowQuestion = $rowQuestion;
				
		$this->renderView('amway/comment/list');
	}
	
	public function addAction()
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Add Content";
		$configure_languages = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $configure_languages;
		$this->oView->link_url = site_url('site/content/add');
		$this->oView->rowContent = array();
		
		if ($this->oInput->isPost())
		{
			$objContent = new Content();
			
			if (!empty($this->oInput->_files["image_file"]['name']))
			{			
				$file_content_data = $this->upload_files_content("image_file");
				$data['image_file'] = $file_content_data['file_name'];
			}
						
			$data['cat_id'] = $this->oInput->post('cat_id' ,0);
			$data['active'] = $this->oInput->post('active' ,0);
			$data['sort_order'] = $this->oInput->post('sort_order' ,0);
			
			foreach ($configure_languages['languages'] as $code => $row)
			{
				$data["title_{$code}"] = $this->oInput->post("title_{$code}");
				$data["short_body_{$code}"] = $this->oInput->post("short_body_{$code}");
				$data["long_body_{$code}"] = $this->oInput->post("long_body_{$code}");
			}
			
			$data['create_at'] = now_to_mysql();
			$last_id = $objContent->insert($data);
			
			if (count($this->oInput->_files["image_gallery"]) > 0)
			{
				$file_gallery_data = $this->upload_files_gallery("image_gallery");
				
				$objGallery = new Gallery();
				$objGallery->insert($this->CODE_ROOT, $last_id, $file_gallery_data);
			}
			
			redirect("site/content/list");
		}
		
		$objCat = new MainCats();
		$this->oView->menuTreeOptionHtml = $objCat->loadMenuTreeOptionHtml($this->CODE_ROOT,'cat_id', -1, -1, $configure_languages);

		$this->renderView('site/content/_form');
	}
	
	public function editAction($question_id, $comment_id)
	{
		// TODO : Check validate and process upload image
		$this->oView->box_title = "Edit Comment";
		$this->oView->box_action = "Edit Comment";
		$this->oView->link_url = site_url('amway/comment/edit/'.$question_id.'/'.$comment_id);
		$this->oView->return_url = site_url('amway/comment/list/'.$question_id);
		$this->oView->question_id = $question_id;
		$this->oView->comment_id = $comment_id;
		
		$objComment = new Amway_Comment();
		
		if ($this->oInput->isPost())
		{
			$data['content'] = $this->oInput->post('content' ,0);
			
			$data['active'] = $this->oInput->post('active' ,0);
			$data['sort_order'] = $this->oInput->post('sort_order' ,0);
			
			$objComment->update($comment_id,$data);
			
		}

		$rowComment = $objComment->get($comment_id);
		$this->oView->rowComment = $rowComment;
		
		$this->renderView('amway/comment/_form');
	}
	
	public function deleteAction($question_id, $comment_id)
	{
		// TODO : Check validate and delete image resource
		$objComment = new Amway_Comment();
		$objComment->delete($comment_id);
				
		$return_url = $this->oInput->varGet('return_url' ,"amway/comment/list/".$question_id);
		redirect($return_url);		
		
// 		redirect("amway/comment/list/".$question_id);
	}
	
	public function activeAction($question_id, $comment_id, $status)
	{
		$objComment = new Amway_Comment();
		$rowComment = $objComment->get($comment_id);
		
		if (!empty($rowComment)) 
		{
			$data['active'] = $status;
			$objComment->update($comment_id, $data);
		}
		
		$return_url = $this->oInput->varGet('return_url' ,"amway/comment/list/".$question_id);
		redirect($return_url);
				
// 		redirect("amway/comment/list/".$question_id);
	}	
	

}
