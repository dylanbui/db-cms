<?php

namespace App\Controller\PdoDatabase;

use App\Lib\Core\BaseController;
use App\Lib\Paginator;
use App\Lib\Validation;
use App\Model\Ex\Users as Ex_Users;

Class PdoModelController Extends BaseController 
{

	function __construct()
	{
		parent::__construct();
	}
	
	public function indexAction()
	{
		return $this->forward('pdo-database/pdo-model/pdo-model');
	}
	
	public function pdoModelAction($offset = 0) 
	{
	    $this->oView->title = 'PDO Model Database MVC';
	    
	    $users = new Ex_Users();
	    
	    $items_per_page = 15;
	    $offset = ($offset % $items_per_page != 0 ? 0 : $offset);
	    
		$rs = $users->getRowSet(NULL,array(),'user_id DESC',$offset,$items_per_page);
		$total = $users->getTotalRow();

       // $users->showDebug(true);
       // exit;

	    $pages = new Paginator();
	    $pages->current_url = site_url('pdo-database/pdo-model/pdo-model/%d');
	    $pages->offset = $offset;
	    $pages->items_per_page = $items_per_page;
	    
		$pages->items_total = $total; //$users->getTotalRow();
		$pages->mid_range = 7;
		$pages->paginate();
		
		$this->oView->pages = $pages;
	    $this->oView->rs = $rs;		
	    
	    $this->oView->add_link = site_url('pdo-database/pdo-model/pdo-add-model/');
	    $this->oView->view_link = site_url('pdo-database/pdo-model/pdo-view-model/');
	    $this->oView->edit_link = site_url('pdo-database/pdo-model/pdo-edit-model/');
	    $this->oView->delete_link = site_url('pdo-database/pdo-model/pdo-delete-model/');
	    
	    $this->renderView('pdo-database/pdo-model/index');
	}	

	public function pdoDeleteModelAction($id)
	{
		$users = new Ex_Users();
		$row = $users->get($id);
	    if(!empty($row))
	    {
	    	$users->delete($id);
	    }
	    redirect('pdo-database/pdo-model/pdo-model');
	}
	
	public function pdoAddModelAction()
	{
		$this->oView->title = 'PDO Model Add Form';
		$this->oView->link = site_url('pdo-database/pdo-model/pdo-add-model');
		
		$val = new Validation();
		$val->source = $_POST;
		
		if(!empty($_POST))
		{
			$val = new Validation();
			$val->source = $_POST;
			$val->addValidator(array('name' => 'first_name','type' =>'string','required'=>true));
			$val->addValidator(array('name' => 'last_name','type' =>'string','required'=>true));
			$val->addValidator(array('name' => 'email','type' =>'email','required'=>true));
			$val->addValidator(array('name' => 'address','type' =>'string','required'=>true));
			$val->run();
			
			if(!$val->hasError())
			{
				$users = new Ex_Users();
				$data = array(
					'first_name' => $_POST['first_name'],
					'last_name' => $_POST['last_name'],
					'email' => $_POST['email'],
					'address' => $_POST['address'],
					'summary' => $_POST['summary'],
					'description' => $_POST['description']						
				);
				$users->insert($data);
				redirect('pdo-database/pdo-model/pdo-model');
			}
			
			$this->oView->errorMessage = $val->errorMessage();
			$this->oView->data = $_POST;
		}
		
		$this->renderView('pdo-database/pdo-model/_form');
	}
	
	public function pdoEditModelAction($id)
	{
		$this->oView->title = 'Model Edit Form';		
		$this->oView->link = site_url('pdo-database/pdo-model/pdo-edit-model/' . $id);		
		
		$users = new Ex_Users();
		$row = $users->get($id);
// 		$row = $users->getRow("user_id = ?", array($id));
		
	    if(empty($row))
	    	redirect('pdo-database/pdo-model/pdo-model');

		$this->oView->data = $row;
		
		if(!empty($_POST))
		{
			$val = new Validation();
			$val->source = $_POST;
			$val->addValidator(array('name' => 'first_name','type' =>'string','required'=>true));
			$val->addValidator(array('name' => 'last_name','type' =>'string','required'=>true));
			$val->addValidator(array('name' => 'email','type' =>'email','required'=>true));
			$val->addValidator(array('name' => 'address','type' =>'string','required'=>true));
			$val->run();
			
			if(sizeof($val->errors) == 0)
			{
				$data = array(
					'first_name' => $_POST['first_name'],
					'last_name' => $_POST['last_name'],
					'email' => $_POST['email'],
					'address' => $_POST['address'],
					'summary' => $_POST['summary'],
					'description' => $_POST['description']						
				);				
				$users->update($id,$data);
		    	redirect('pdo-database/pdo-model/pdo-model');
			}
			$this->oView->errorMessage = $val->errorMessage();
			$this->oView->data = $_POST;
		}
		
		$this->renderView('pdo-database/pdo-model/_form');
	}

	public function pdoViewModelAction($id)
	{
		$this->oView->title = 'Model View Form';
	
		$users = new Ex_Users();
		$row = $users->get($id);

		if(empty($row))
			redirect('pdo-database/pdo-model/pdo-model');
	
		$this->oView->data = $row;
	
		$this->renderView('pdo-database/pdo-model/_form_view');
	}

    public function pdoShowAnyAction()
    {
        $this->oView->title = 'Model View Form';

        $users = new Ex_Users();

        $row = $users->getRow('first_name = ? OR email = ?', array('honh kong', 'tienduc2002vn@yahoo.com'));

        echo "<pre>";
        print_r($row);
        echo "</pre>";

        $rows = $users->getRowset('first_name = ? OR email = ?', array('honh kong', 'tienduc2002vn@yahoo.com'));

        echo "<pre>";
        print_r($rows);
        echo "</pre>";

        $users->setActiveField(999827871);

        $users->showDebug(true);
        exit;

        if(empty($row)) {
            echo "<pre>";
            print_r('Khong co hang');
            echo "</pre>";
            exit();
            redirect('pdo-database/pdo-model/pdo-model');
        }

        $this->oView->data = $row;

        $this->renderView('pdo-database/pdo-model/_form_view');
    }


}

?>
