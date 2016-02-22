<?php

namespace App\Controller\Paging;

use App\Lib\Core\BaseController;
use App\Lib\Paginator;

Class PageController Extends BaseController 
{

	function __construct()
	{
		parent::__construct();
	}
	
	public function indexAction() 
	{
	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
	    $this->renderView('paging/page/index');
	}
	
	public function paginatorAction($offset = 0) 
	{
	    $this->oView->title = 'Welcome to Paginator MVC';
	    
//	    $pages = new Paginator();
//	    $pages->current_url = site_url('paging/index');
//	    $pages->current_page = isset($_GET['page']) ? $_GET['page'] : 1;
//	    
//		$pages->items_total = 1202;
//		$pages->mid_range = 7;
//		$pages->paginate();
//		
//		$this->_temp->pages = $pages;
		
		$items_per_page = 25;
		$offset = ($offset % $items_per_page != 0 ? 0 : $offset);
		
	    $pages = new Paginator();
	    $pages->current_url = site_url('paging/page/paginator/%d');
	    $pages->offset = $offset;
	    $pages->items_per_page = $items_per_page;
	    
		$pages->items_total = 1252;
		$pages->mid_range = 5;
		$pages->paginate();
		
		$this->oView->pages = $pages;
		
		$this->renderView('paging/page/paginator');
	}	

}

?>
