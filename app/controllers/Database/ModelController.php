<?php

namespace App\Controller\Database;

use App\Lib\Core\BaseController,
	App\Lib\Core\DbConnection,
	App\Lib\Paginator;

Class ModelController Extends BaseController 
{

	function __construct()
	{
		parent::__construct();
	}
	
	public function indexAction($offset = 0) 
	{
	    $this->oView->title = 'Normal Database MVC';
	    
	    $db = DbConnection::getInstance();

		// Get one row
	    $row = $db->selectOneRow("SELECT count(user_id) As Total FROM ".TB_EX_USER);

	    $items_per_page = 15;
	    $offset = ($offset % $items_per_page != 0 ? 0 : $offset);
	    $rs = $db->query("SELECT * FROM ".TB_EX_USER." ORDER BY user_id DESC limit {$offset},{$items_per_page} ");
	    
	    $pages = new Paginator();
	    $pages->current_url = site_url('database/model/index/%d');
	    $pages->offset = $offset;
	    $pages->items_per_page = $items_per_page;
	    
		$pages->items_total = $row['Total'];
		$pages->mid_range = 7;
		$pages->paginate();
		
		$this->oView->pages = $pages;
	    $this->oView->rs = $rs;
	    
	    $this->oView->add_link = site_url('database/model/add/');
	    $this->oView->edit_link = site_url('database/model/edit/');
	    $this->oView->delete_link = site_url('database/model/delete/');
	    
	    $this->renderView('database/model/index');
	}
	
	public function deleteAction($id)
	{
	    $db = Db::getInstance();
	    $row = $db->selectOneRow("SELECT * FROM ".TB_EX_USER." WHERE user_id = " . $id);
	    if(!empty($row))
	    {
			$db->query("DELETE FROM ".TB_EX_USER." WHERE user_id = " . $id);
	    }
	    redirect('database/model/index');
	}
	
	public function eval_input_data($key_value)
	{
		// 	preg_match_all('/<TABLE id="Table2" cellSpacing="0" cellPadding="0" width="100%" border="0">(.*?)cham_ngang.gif"><\/TD>(.*?)<\/TABLE>/s',$content,$matches,PREG_SET_ORDER);
		// 	$content = $tuyensinh ='';
		// 	foreach ($matches as $match) {
		// 		//   	print 'adfasfasdfsdfsdfas'. $match[4];
		// 		$content .= '<TABLE id="Table2" cellSpacing="0" cellPadding="0" width="100%" border="0">'.$match[1].'cham_ngang.gif"></TD>'.$match[2].'</table>';
	
		$data = NULL;
		eval('\$data = array('.$key_value.');');
		return $data;
	}
	
	
	public function addAction()
	{
		$this->oView->title = 'Normal Add Form';
		$this->oView->link = site_url('database/model/add');
		
		if(!empty($_POST))
		{
//			$str = $_POST['first_name'];
//			$str = '"'.$str.'"';
//			$a = array('a' => 'Apple' ,'b' => 'banana' , 'c' => 'Coconut');
//
//			//serialize the array
//			$s = var_export($a , true);
//
//			$s = $_POST['first_name'];
//
//			echo $s;
//			//strin is >> array ( 'a' => 'Apple', 'b' => 'banana', 'c' => 'Coconut', )
//
//			echo '<br /><br />';
//
//			//unserialize
//			eval('$my_var=' . $s . ';');
//
//			print_r($my_var);
//
//			exit();
//
//
//// 			$str = "'foo'=>'bar'";
//
//			eval("\$arr = array({$str});");
//
//			echo "<pre>";
//			print_r($arr);
//			echo "</pre>";
//			exit();
//			exit();
			
			
			
// 			$eval = "\$str = '{$str}'";
// 			$eval .= "\$data = array(\$str)";
			
// 			eval("\$data = {$str};");
			
			
			
// 			echo "<pre>";
// 			print_r($str);
// 			echo "</pre>";
			
// 			eval("\$data = $eval;");
			
// 			echo "<pre>";
// 			print_r($data);
// 			echo "</pre>";
// 			exit();
			
//			echo "<pre>";
//			print_r($this->eval_input_data($_POST['first_name']));
//			echo "</pre>";
//			exit();


//			$sql = "INSERT INTO ".TB_EX_USER."(first_name,last_name,email,address) VALUES('{$_POST['first_name']}','{$_POST['last_name']}','{$_POST['email']}','{$_POST['address']}')";
	    	$db = Db::getInstance();
//	    	$db->query($sql);

            $sql = "INSERT INTO ".TB_EX_USER."(first_name,last_name,email,address) VALUES(:first_name,:last_name,:email,:address)";
            $params = array(':first_name' => $_POST['first_name']
                            ,':last_name' => $_POST['last_name']
                            ,':email' => $_POST['email']
                            ,':address' => $_POST['address']);

            $val = $db->insert($sql, $params);

			redirect('database/model/index');
		}
		
		$this->renderView('database/model/_form');
	}
	
	public function editAction($id)
	{
		$db = Db::getInstance();
		$this->oView->title = 'Normal Edit Form';
		$this->oView->link = site_url('database/model/edit/' . $id);
	    $row = $db->selectOneRow("SELECT * FROM ".TB_EX_USER." WHERE user_id = " . $id);
		
	    if(empty($row))
	    	redirect('database/model/index');
	    	
		if(!empty($_POST))
		{
			$sql = "UPDATE ".TB_EX_USER." SET first_name = '{$_POST['first_name']}',last_name = '{$_POST['last_name']}',email = '{$_POST['email']}',address = '{$_POST['address']}' WHERE user_id = {$id}";
	    	$db->query($sql);
	    	redirect('database/model/index');
		}
		
		$data = array(
			'first_name' => $row['first_name'],
			'last_name' => $row['last_name'],
			'email' => $row['email'],
			'address' => $row['address'],
		);
		
		$this->oView->data = $data;
		$this->renderView('database/model/_form');
	}	
	
	
}

?>
