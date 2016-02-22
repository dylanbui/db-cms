<?php

namespace App\Controller\SiteIndex;
use App\Lib\Core\BaseController;
use App\Helper\Captcha;
use App\Lib\PHPMailer;
use App\Lib\Email;

class IndexController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
//		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
	}

	public function indexAction() 
	{
//		$_SESSION['test'] = 12;
		$this->oSession->userdata['test'] = 12;
//	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
	    $this->renderView('site/home/index');
	}
	
	public function captchaAction()
	{
        $cap = new Captcha();
		
		$vals = array(
    		'img_path'	 => __DATA_PATH,
    		'img_url'	 => __DATA_URL,
			'font_path'	 =>	__DATA_PATH.'font/monofont.ttf',				
			'length'	 => 6,
    		'img_width'	 => 150,
    		'img_height' => 40,
    		'expiration' => 3600
    	);

		$this->oView->cap = $cap->create_captcha($vals);
		$this->renderView('site-index/index/captcha');
	}

    public function sentEmailAction()
    {
        // Cho phep truy cap KCFINDER
        // Tranh truong hop truy cap thong wa duong link cua iframe
        $_SESSION['KCFINDER'] = array();
        $_SESSION['KCFINDER']['disabled'] = false; // Activate the uploader,

        $this->oView->returnSentMail = null;
        if ($this->oInput->isPost()) {
            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
//            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465; // or 587

            // Dia chi email chung thuc phai duoc cho phep truy cap tu cac ung dung khac
            // https://support.google.com/accounts/answer/6010255
            $mail->Username = "dylanmobiledev@gmail.com";
//            $mail->Password = '!qa2ws3ed';

            // Khi tao 1 pw voi chung thuc 2-Step Verification, khong can phai chung thuc cho phep truy cap tu cac ung dung khac
            // https://myaccount.google.com/security
            $mail->Password = 'akpoomydoyopmaug';

            // Su dung google no se tu dong lay dia chi smtp
            // Muon thay doi Sent From thi phai Settings -> Accounts -> Send mail as -> Add another email address you own
            $mail->SetFrom("buivantienduc@gmail.com");

            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $this->oInput->post('subject');
            $mail->Body = $this->oInput->post('email_content');
            $mail->AddAddress($this->oInput->post('email'));

            if(!$mail->Send()) {
                $this->oView->returnSentMail = '<td style="font-weight: bold;color: red; ">' . $mail->ErrorInfo;
            } else {
                $this->oView->returnSentMail = '<td style="font-weight: bold;color: green; ">Message has been sent';
            }
        }

        $this->oView->title = 'PHPMailer Service';
        $this->oView->sub_title = 'Use PHPMailer Class';
        $this->renderView('site-index/index/sent_email');
    }

    public function sentEmailSmtpAction()
    {
        // Cho phep truy cap KCFINDER
        // Tranh truong hop truy cap thong wa duong link cua iframe
        $_SESSION['KCFINDER'] = array();
        $_SESSION['KCFINDER']['disabled'] = false; // Activate the uploader,

        $this->oView->returnSentMail = null;
        if ($this->oInput->isPost()) {

            $mail = new Email();
            $mail->to = $this->oInput->post('email');
            $mail->subject = $this->oInput->post('subject');
            $mail->body = $this->oInput->post('email_content');

            $returnVal = $mail->sendWithSmtpConfig($this->oConfig->config_values['mail']);

            if($returnVal == true) {
                $this->oView->returnSentMail = '<td style="font-weight: bold;color: green; ">Message has been sent';
            } else {
                $this->oView->returnSentMail = '<td style="font-weight: bold;color: red; ">' . $returnVal;
            }
        }

        $this->oView->title = 'SMTP Service';
        $this->oView->sub_title = 'Use Email Class';
        $this->renderView('site-index/index/sent_email');
    }
	
	public function linksAction($other_link)
	{
		$this->oView->func_get_args = func_get_args();
        $this->oView->other_link = $other_link;
		$this->renderView('site-index/index/links');
	}

    public function linksItemAction()
    {
        echo "<pre>";
        print_r($_GET['_url']);
        echo "</pre>";

        echo "<pre>";
        print_r($_GET['_url_params']);
        echo "</pre>";

        echo "<pre>";
        print_r('Khong xu ly gi ca - chuyen link thoi');
        echo "</pre>";
        exit();
    }

    public function showConfigWebAction()
    {
        $this->renderView('site-index/index/show-config-web');
    }

	
	public function runCommanderAction()
	{
		$arr = exec("dir");
		
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		exit();
		
		
	}

    public function session1Action()
    {
        $this->oSession->userdata['session_1'] = "gia tri session 1";

        echo "<pre>";
        print_r($this->oSession->userdata);
        echo "</pre>";
        exit();
    }

    public function session2Action()
    {

        echo "In ra gia tri";

        echo "<pre>";
        print_r($this->oSession->userdata['session_1']);
        echo "</pre>";
        exit();
    }


	public function testAction() 
	{
		$arr[] = "thong---tin|| cong; ty";
		$arr[] = "!-thong---tin|| cong; ty-=+";
		$arr[] = "thong-+-tin|| cong; ty";
		$arr[] = "thong---tin|| -------- cong; ty &*";
		$arr[] = "@@thong---tin|| +=- cong; ty+=-";
		$arr[] = "thong---bùi văn tiến đức-------------cong; ty";
		
		foreach ($arr as $r)
		{
			echo "<pre>";
			print_r(str2url($r));
			echo "</pre>";
		}
		
		exit();
		
//		echo "<pre>";
//		print_r(print rand(1, 1000000));
//		echo "</pre>";
//		exit();
		
//echo "<pre>";
//print_r((rand() * rand()) / (getrandmax() * getrandmax()));
//echo "</pre>";
//exit();
//
//echo "<pre>";
//print_r(abs((rand()%150)-50) );
//echo "</pre>";
//exit();
//
//		$x = rand(0,1) ? rand(1,100) : rand(1,50);

		$i = 90;
		
		$r = rand(1, 100);
		$p = rand(1, $i);
		
		echo ($p - $r > 0) ? "OK" : "ERROR";
		echo "<br>";		   		
		print("Winner! -- Hit refresh on your browser to play again");
      	exit;
	}

    public function loadRouterAction($slug, $num)
    {
        $this->oView->title = 'Load router';
        $this->oView->slug = $slug;
        $this->oView->num = $num;
        $this->oView->current_url = $_SERVER['REQUEST_URI'];
        $this->oView->current_router = $_GET['_url'];

        // echo "<pre>";
        // print_r($_SERVER);
        // echo "</pre>";

        // echo "<pre>";
        // print_r($_GET);
        // echo "</pre>";
        // exit();

        $this->renderView('site-index/index/change');
    }
	
	public function changeAction()
	{
	    $this->renderView('site/index/change');
 	}

    public function siteRenderHeaderAction($my_title)
    {
        $this->oView->my_title = $my_title;
        return $this->oView->fetch('site/index/site_render_header');
    }

    public function siteRenderFooterAction()
    {
        return $this->oView->fetch('site/index/site_render_footer');
    }


	
	
}
