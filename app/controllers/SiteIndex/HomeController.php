<?php

namespace App\Controller\SiteIndex;

use App\Lib\Core\BaseController;
use App\Lib\Core\Request;

class HomeController extends BaseController
{

	var $_cfg_upload_file;
	var $_cfg_thumb_image;
	var $children;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		$this->_cfg_upload_file['max_size']	= '500';
		$this->_cfg_upload_file['max_width']  = '2048';
		$this->_cfg_upload_file['max_height']  = '1536';

		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['maintain_ratio'] = TRUE;
		$this->_cfg_thumb_image['width'] = 175;
		$this->_cfg_thumb_image['height'] = 0;
		
		$this->children = array();
	}

	public function indexAction() 
	{
		$this->oSession->userdata['test_1'] = 'Thong tin duoc luu vao test';
		$this->oSession->userdata['test'] = 'Thong tin duoc luu vao test';
	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
	    $this->renderView('site-index/home/index');
	}
	
	public function tinymceAction()
	{
		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';

		// redirect('http://google.com');

		$this->renderView('site-index/home/tinymce');
	}

	public function ckeditorAction()
	{
		// Cho phep truy cap KCFINDER
		// Tranh truong hop truy cap thong wa duong link cua iframe
		$_SESSION['KCFINDER'] = array();
		$_SESSION['KCFINDER']['disabled'] = false; // Activate the uploader,
		
		if ($this->oInput->isPost()) 
		{
			$this->_cfg_upload_file['file_name']  = 'img_'.time();
			
			$uploadLib = new UploadLib($this->_cfg_upload_file);
			
			$returnValue = $uploadLib->do_multi_upload("content_file");
				
			if (empty($returnValue))
			{
				echo $uploadLib->display_errors();
				exit();
			}
			else
			{
				foreach ($returnValue as $fileData)
				{
					$this->_cfg_thumb_image['source_image']	= $fileData['full_path'];
					
					$imageLib = new ImageLib($this->_cfg_thumb_image);
					if ( ! $imageLib->resize())
					{
						echo $imageLib->display_errors();
						exit();
					}
				}
// 				echo "Upload thanh cong";
			}

// 			echo "<pre>";
// 			print_r($returnValue);
// 			echo "</pre>";
// 			exit();
		}
		
// 		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
// 		$this->renderView('site/home/ckeditor');

		$this->_children[] = new Request('site-index/home/child-first');
		$this->_children[] = new Request('site-index/home/child-second',array('Title duoc truyen vao site/home/child-second'));		
		
		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
		$this->renderView('site-index/home/ckeditor');		
	}
	
	public function testResizeImageAction()
	{
		$this->_cfg_thumb_image['source_image']	= __UPLOAD_DATA_PATH."img_1387339644.jpg";//$fileData['full_path'];
		
		
		$imageLib = new ImageLib($this->_cfg_thumb_image);
		if ( ! $imageLib->resize())
		{
			echo $imageLib->display_errors();
			exit();
		}
		
		echo "<pre>";
		print_r($this->_cfg_thumb_image);
		echo "</pre>";
		exit();
		
		
		$this->renderView('site/home/test_resize_image');
	}
	
	public function partRenderAction($title)
	{
	    $this->oView->title = 'Day la phan noi dung duoc render vao';
	    $this->oView->render_title = $title;
	    return $this->oView->fetch('site-index/home/part_render');
	}
	
	public function renderAction()
	{
		$this->oSession->userdata['c'] = 2000;
		$this->oView->title_aaa = 'Day la trang dung chuc nang renderAction --- '.$this->oSession->userdata['test'];
//		$this->oView->part_render = Module::run(new Request('site/home/part-render',array('Title duoc truyen vao '.$this->oSession->userdata['c'])));
        $this->oView->part_render = Request::staticRun(new Request('site-index/home/part-render',array('Title duoc truyen vao '.$this->oSession->userdata['c'])));
//		$this->oView->part_render = Module::run('site/home/part-render/buivantienduc');		
		$this->renderView('site-index/home/render');
	}

	public function checkLoginAction()
	{
// 		return new Request('site/home/deny',array('Title duoc truyen vao - DENY'));		
	}	
	
	public function firstAction()
	{
		$this->oView->param_first = "Thong tin load tu firstAction";
	}
	
	public function secondAction()
	{
		$this->oView->param_second = "Thong tin load tu secondAction";	
	}
	
	public function denyAction($title_deny)
	{
		$this->oView->title_deny = $title_deny;
		$this->display('site/home/deny');
	}	
	
	public function childFirstAction()
	{
		return "Thong tin load tu childFirstAction";
	}
	
	public function childSecondAction($title)
	{
		return "Thong tin load tu childSecondAction - title : " . $title;
	}

    public function siteRenderAction()
    {
        $this->oView->file_title = 'siteRenderAction';
        $this->oView->title = 'Title truyen vao cac trang con';
        $this->renderView('site/home/site_render');
    }

    public function forwardDemoAction()
    {
        $this->oView->forward_title = "Duoc forward tu thang 'site-index/home/forward-demo'";
        return $this->forward('site-index/index/captcha');
    }


	private function display($path)
	{
		foreach ($this->children as $child) {
			$param_name = str_replace("-", "_", $child->getAction());
			$this->oView->{$param_name} = Module::run($child);
		}		
		
		$this->oView->main_content = $this->oView->fetch($path);
		$result = $this->oView->renderLayout($this->_layout_path);
		$this->oResponse->setOutput($result, $this->oConfig->config_values['application']['config_compression']);		
	}


}
