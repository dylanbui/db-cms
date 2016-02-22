<?php

namespace Admin\Controller\Home;

use App\Model\Contact,
    App\Model\Base\ConfigureSystem,
    App\Lib;

class ContactController extends \Admin\Controller\AdminController
{
	
	public function __construct()
	{
		parent::__construct();
		
	}

	public function indexAction() 
	{
		$this->listAction();		
	}

    public function listAction($display_data = 0)
    {
        if (!empty($display_data)) {
            // -- Danh sach cot phai tuong ung voi ds hien thi --
            $aColumns = array( 'user_id', 'username', 'full_name', 'email', 'mobile', 'active', 'user_id');
            $output = \App\Helper\DataTables::loadDataWithOption(new Contact(), $aColumns, 'user_id DESC');

            // -- Dung cach goi ham callback --
//            $output = \App\Helper\DataTables::loadDataWithOption(function ($sWhere, $sOrder, $offset, $items_per_page) {
//
//                $object = new Contact();
//                $totalRecord = $object->getTotalRow($sWhere);
//                $sOrder = empty($sOrder) ? 'user_id DESC' : $sOrder;
//                $rowSets = $object->getRowSet($sWhere, array(), $sOrder, $offset, $items_per_page);
//                $returnVal['totalRecord'] = $totalRecord;
//                $returnVal['rowSets'] = $rowSets;
//                return $returnVal;
//
//            }, $aColumns, 'user_id DESC');

            echo $output;
            exit();
        }

        $this->oView->box_title = "Contact";
        $this->oView->box_action = "Contact";

        $this->oView->add_link = site_url('home/contact/add');
        $this->oView->view_link = site_url('home/contact/view/');
        $this->oView->edit_link = site_url('home/contact/edit/');
        $this->oView->delete_link = site_url('home/contact/delete/');

        $this->renderView('home/contact/list');
    }

	public function listOldAction($offset = 0)
	{
		$this->oView->box_title = "Contact";
		$this->oView->box_action = "Contact";

        $objContact = new Contact();

        $items_per_page = 20;
        $offset = ($offset % $items_per_page != 0 ? 0 : $offset);

        $rsContact = $objContact->getRowSet(NULL,array(),'user_id DESC',$offset,$items_per_page);
        $total = $objContact->getTotalRow();

        $oPaginator = new \App\Lib\Paginator();
        $oPaginator->current_url = site_url('home/contact/list/%d');
        $oPaginator->offset = $offset;
        $oPaginator->items_per_page = $items_per_page;

        $oPaginator->items_total = $total; //$users->getTotalRow();
        $oPaginator->mid_range = 7;
        $oPaginator->paginate();

        $this->oView->oPaginator = $oPaginator;
        $this->oView->rsContact = $rsContact;

        $this->oView->add_link = site_url('home/contact/add');
        $this->oView->view_link = site_url('home/contact/view/');
        $this->oView->edit_link = site_url('home/contact/edit/');
        $this->oView->delete_link = site_url('home/contact/delete/');

		$this->renderView('home/contact/list');
	}
	
	public function addAction()
	{
		$this->oView->box_title = "Contact";
		$this->oView->box_action = "Add Contact";		
		$this->oView->link_url = site_url('home/contact/add');
		$this->oView->rowContact = array();
		
		if ($this->oInput->isPost())
		{
            $last_id = $this->replace();
			redirect("home/contact/list");
		}

		$this->renderView('home/contact/_form');
	}
	
	public function editAction($content_id)
	{
		$this->oView->box_title = "Contact";
		$this->oView->box_action = "Edit Contact";
		$this->oView->link_url = site_url('home/contact/edit/'.$content_id);
		$this->oView->content_id = $content_id;
		
		$objContact = new Contact();
        $rowContact = $objContact->get($content_id);
        if (empty($rowContact))
            redirect("home/contact/list");
		
		if ($this->oInput->isPost())
		{
            $this->replace($content_id);
			redirect("home/contact/list");
		}

		$this->oView->rowContact = $rowContact;
		$this->renderView('home/contact/_form');
	}

    private function replace($content_id = null)
    {
        $data['active'] = $this->oInput->post('active' ,0);

        $data["username"] = $this->oInput->post("username");
        $data["first_name"] = $this->oInput->post("first_name");
        $data["last_name"] = $this->oInput->post("last_name");
        $data["full_name"] = $data["first_name"].' '.$data["last_name"];
        $data["address"] = $this->oInput->post("address", NULL);
        $data["email"] = $this->oInput->post("email", NULL);
        $data["mobile"] = $this->oInput->post("mobile");

        $data["subject"] = $this->oInput->post("subject", NULL);
        $data["question"] = $this->oInput->post("question", NULL);
        $data["answer"] = $this->oInput->post("answer", NULL);

        $objContact = new Contact();
        if(empty($content_id))
        {
            // -- Insert --
            // -- Add variable when insert --
            $content_id = $objContact->insert($data);
        } else
        {
            // -- Update --
            // -- Add variable when update --
            $objContact->update($content_id,$data);
        }

        if ($data['active'] == 1 && !empty($data["email"]))
        {
            $this->sendEmailNotify($data);
        }
    }
	
	public function deleteAction($contact_id)
	{
		$objContact = new Contact();
		$objContact->delete($contact_id);
				
		redirect("home/contact/list");
	}

	private function sendEmailNotify($data)
	{
		$arrVal['data'] = $data;
	
		$oConfigSys = new ConfigureSystem();
        $rs = $oConfigSys->getGroupConfigureByCode('smtp_info');

		foreach ($rs as $row)
			$configEmail[$row['code']] = $row['value'];
		
		$email_view = new \App\Lib\Core\View();
		$body = $email_view->parser(__LAYOUT_PATH."/email_template/notify_contact.phtml",$arrVal);

        $mail = new \App\Lib\Email();
        $mail->to = $data['email']; //$this->oInput->post('email');
        $mail->subject = $data['subject']; //$this->oInput->post('subject');
        $mail->body = $body;

        $returnVal = $mail->sendWithSmtpConfig($configEmail);

        if($returnVal == true) {
            // -- Thanh cong --
            $this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
                    'msg_code' => "success",
                    'msg_content' => 'Send email successfull !'));

        } else {
            // -- Loi cong --
            $this->oSession->set_flashdata('notify_msg',array('msg_title' => "Error",
                'msg_code' => "error",
                'msg_content' => 'Send email error !'));
        }
	}
	

}
