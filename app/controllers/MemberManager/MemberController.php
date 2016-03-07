<?php

namespace App\Controller\MemberManager;

use App\Lib\Core\BaseController;
use App\Lib\Core\Request;

class MemberController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

    // -- Start Frist --
    public function getLoginInfoAction()
    {
        $current_user = null;
        if (!empty($this->oSession->userdata["current_user"]))
            $current_user = $this->oSession->userdata["current_user"];

        $this->oView->current_user = $current_user;
    }

	public function indexAction()
	{
		$this->oSession->userdata['test_1'] = 'Thong tin duoc luu vao test';
		$this->oSession->userdata['test'] = 'Thong tin duoc luu vao test';
	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
	    $this->renderView('member-manager/facebook/login');
	}

    public function loginAction()
    {
        if(!empty($this->oSession->userdata["current_user"]))
            redirect('member-manager/member/info');

        $this->oView->errorMsg = $this->oSession->flashdata('err_login');

        $this->renderView('member-manager/member/login');
    }

    public function logoutAction()
    {
        unset($this->oSession->userdata["current_user"]);
        redirect('member-manager/member/login');
    }

    public function loginSiteAccountAction()
    {
        if($this->oInput->isPost())
        {
            $email = $this->oInput->post('email', null);
            $password = $this->oInput->post('password', null);
            $objMember = new \App\Model\Member();
            $rowMember = $objMember->auth($email, $password);
            if(empty($rowMember))
            {
                $this->oSession->set_flashdata('err_login', 'Account khong ton tai');
                redirect('member-manager/member/login');
            }

            $this->oSession->userdata["current_user"] = $rowMember;
            redirect('member-manager/member/info');
        }
        redirect('member-manager/member/login');
    }

    public function signupSiteAccountAction()
    {
        if($this->oInput->isPost())
        {
            $objMember = new \App\Model\Member();
            $email = $this->oInput->post("email");
            $rowMember = $objMember->getRow("email = ?", array($email));
            if(!empty($rowMember))
            {
                $this->oSession->set_flashdata('err_login', 'Account da ton tai roi');
                redirect('member-manager/member/login');
            }

            $pw = $this->oInput->post('password');
            $arr = array(
                'gender' => $this->oInput->post("gender"),
                'email' => $email,
                'first_name' => $this->oInput->post("first_name"),
                'last_name' => $this->oInput->post("last_name"),
                'full_name' => $this->oInput->post("first_name").' '.$this->oInput->post("last_name"),
                'password' => encryption($pw),
                'plain_password' => $pw,
                'create_at' => now_to_mysql()
            );

            $current_id = $objMember->insert($arr);

            // -- Sau khi tao thi login luon --
            $rowMember = $objMember->getRow("id = ?", array($current_id));
            $this->oSession->userdata["current_user"] = $rowMember;
            redirect('member-manager/member/info');
        }
        redirect('member-manager/member/login');
    }

    public function infoAction()
    {
        if(empty($this->oSession->userdata["current_user"]))
            redirect('member-manager/member/login');

        $current_user = $this->oSession->userdata['current_user'];
        $objMember = new \App\Model\Member();
        $rowMember = $objMember->getRow("id = ?", array($current_user['id']));
        if(empty($rowMember))
            redirect();

        $this->oView->rowMember = $rowMember;
        $this->renderView('member-manager/member/info');
    }

    public function showAction()
    {
        echo "<pre>";
        print_r($this->oSession->userdata['current_user']);
        echo "</pre>";
        exit();
    }



}
