<?php

class Site_IndexController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
//		$_SESSION['test'] = 12;
		$this->oSession->userdata['test'] = 12;
	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
	    $this->renderView('site/home/index');
	}
	
	public function changeAction()
	{
		$db = DbConnection::getInstance();
	    
	    $rs = $db->query("SELECT * FROM tb_content");
	    
	    foreach ($rs as $row)
	    {
	    	$title = trim($this->textToVN(strip_tags($row['title'])));
	    	$short_body = trim($this->textToVN(strip_tags($row['short_body'])));
	    	$long_body = trim($this->textToVN(strip_tags($row['long_body'])));
	    	
			$sql = "UPDATE tb_content SET title = \"{$title}\",short_body = \"{$short_body}\",long_body = \"{$long_body}\" WHERE id = {$row['id']}";
    		$db->query($sql);
	    }
	    
	    $rs = $db->query("SELECT * FROM tb_content");
	    
	    $this->oView->rs = $rs;
	    $this->renderView('site/index/change');
 
//		$sql = "UPDATE users SET first_name = '{$_POST['first_name']}',last_name = '{$_POST['last_name']}',email = '{$_POST['email']}',address = '{$_POST['address']}' WHERE user_id = {$id}";
//    	$db->query($sql);

	}
	
	private function textToVN($str)
	{
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
		$str = preg_replace("/(đ)/", "d", $str);
	
		$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
		$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
		$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
		$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
		$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
		$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
		$str = preg_replace("/(Đ)/", "D", $str);
	
		return $str;
	}	
	
}
