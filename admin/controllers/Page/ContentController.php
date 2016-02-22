<?php

namespace Admin\Controller\Page;

use App\Model\Page\Category,
	App\Model\Page\Configure,
	App\Model\Page\Content,
	App\Model\Page\Gallery,
	App\Lib\UploadLib,
	App\Lib\ImageLib,
	App\Lib\Paginator;

class ContentController extends \Admin\Controller\AdminController
{
	var $_cfg_upload_file;
	var $_cfg_thumb_image;	
	var $_confLn;
	
	var $_items_per_page;

	var $_page_id;
	var $_page_code;
	var $_content_id;
	
	var $_objPageConf;
	var $_rowPageConf;
	var $_objPageContent;
	
	public function __construct()
	{
		parent::__construct();
		$this->_confLn = $this->oConfigureSystem['configure_languages'];
		$this->oView->configure_languages = $this->_confLn;

		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		$this->_cfg_upload_file['max_size']	= '2000';
		$this->_cfg_upload_file['max_width']  = '2048';
		$this->_cfg_upload_file['max_height']  = '1536';
		
		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['maintain_ratio'] = TRUE;
		$this->_cfg_thumb_image['width'] = 175;
		$this->_cfg_thumb_image['height'] = 150;

		$this->_items_per_page = 10;
		
		$this->_page_id = NULL;
		$this->_content_id = NULL;
		
		$this->_objPageConf = new Configure();
		$this->_objPageContent = new Content();
	}

	public function indexAction() 
	{
		return $this->forward("common/error/error-404");
	}

    public function listAction($page_code, $cat_id = 0)
    {
        $this->_loadConfigPage($page_code);
        $cat_id = intval($cat_id);

        if(df($this->_rowPageConf['data']['use_category'], 0) == 1)
        {
            // --- Use Category ---//
            $objCat = new Category();
            $this->oView->arrMenuTree = $objCat->loadMenuTree($this->_page_id, $this->_confLn['default_lang']);

            $currentCategoryName = "--- Filter Category ---";
            if($cat_id != 0)
            {
                $row = $objCat->getRowDataCategory($cat_id);
                $currentCategoryName = $row['ln_cat_field']["{$this->_confLn['default_lang']}"]['name'];
            }
            $this->oView->currentCategoryName = $currentCategoryName;
        }

        // -- Use to load current page --
        $page_number = $this->oInput->get('page_number', 0);
        $display_length = $this->oInput->get('display_length', 10);

        $this->oView->page_number = $page_number;
        $this->oView->display_length = $display_length;

        $this->oView->add_link = site_url("page/content/add/".$page_code);
        $this->oView->delete_link = site_url("page/content/delete-content/".$page_code);
        $this->oView->update_link = site_url("page/content/update/".$page_code);
        $this->oView->active_link = site_url("page/content/switch-status/".$page_code);
        $this->oView->load_data_link = site_url("page/content/load-data-content/".$page_code.'/'.$cat_id);

        $this->renderView('page/content/list');
    }

	public function addAction($page_code)
	{
		$this->updateAction($page_code);		
	}	
	
	public function updateAction($page_code ,$content_id = NULL)
	{
		$this->_loadConfigPage($page_code);

		// Check permission user
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
		
		$this->oView->cancel_link = site_url("page/content/list/".$page_code);
	
		$this->oView->arrMainDbFields = $this->_objPageConf->loadContentMainFields();
		$this->oView->arrLnDbFields = $this->_objPageConf->loadContentLnFields();

		$dataContent = array();
		if(!empty($content_id))
		{
			$objGallery = new Gallery();
			$totalGalItem = $objGallery->getTotalRow("content_id = ? AND active = 1", array($content_id));
			$this->oView->gallery_link = site_url("page/gallery/show/".$this->_page_id."/".$content_id);
			$this->oView->totalGalItem = $totalGalItem;
			
			$this->oView->form_link = site_url("page/content/update/".$page_code."/".$content_id);
			$this->oView->page_title = "Update record";
			$this->oView->page_action = "update";
			// Check $dataContent exsit
			$dataContent = $this->_objPageContent->getRowDataContent($content_id);
            if (empty($dataContent))
                redirect(site_url("page/content/list/".$page_code));
			
		} else 
		{
			$this->oView->page_title = "Add new record";
			$this->oView->page_action = "add";			
			$this->oView->form_link = site_url("page/content/add/".$page_code);
			
			// Set default value
			$dataContent['main_field']['cat_id'] = 0;
			$dataContent['main_field']['sort_order'] = '';
			$dataContent['main_field']['active'] = 1;
		}

		if(df($this->_rowPageConf['data']['use_category'], 0) == 1)
		{
			$selected_id = $dataContent['main_field']['cat_id'];
			$objCategory = new Category();
			$this->oView->htmlCat = $objCategory->loadMenuTreeOptionHtml($this->_page_id, "main_field[cat_id]", $selected_id, -1, $this->_confLn);
		}
		
		$this->oView->dataContent = $dataContent;
		
		$data = $this->_rowPageConf['data']; // unserialize($rowPageConf['data']);
		$arrMainField = $data['main_field'];
		$arrLnField = $data['ln_field'];
		$arrMainImage = $data['main_image'];
		$arrLnImage = $data['ln_image'];

		$arrGalleryField = $data['gallery_image'];
		
		if ($this->oInput->isPost()) {

            // echo "<pre>";
            // print_r($_FILES);
            // echo "</pre>";
            // exit();

            // Process upload data
            $arrMainImageField = $this->_upload_main_image($arrMainImage);
            // -- Delete old image if upload data succes --
            // -- Neu ton tai hinh anh up len thi xoa hinh anh cu --
            foreach ($arrMainImageField as $key => $value) {
                @unlink(__UPLOAD_DATA_PATH . $dataContent['main_field'][$key]);
                @unlink(__UPLOAD_DATA_PATH . 'thumb_' . $dataContent['main_field'][$key]);
            }

            $arrLnImageField = $this->_upload_ln_image($arrLnImage);
            // -- Neu ton tai hinh anh trong content language up len thi xoa hinh anh cu --
            foreach ($arrLnImageField as $key => $valueArr) {
                foreach ($valueArr as $field => $value) {
                    @unlink(__UPLOAD_DATA_PATH . $dataContent['ln_field'][$key][$field]);
                    @unlink(__UPLOAD_DATA_PATH . 'thumb_' . $dataContent['ln_field'][$key][$field]);
                }
            }

			$arrMainField = $this->oInput->post('main_field');
			$arrLnField = $this->oInput->post('ln_field');

			// --- Set current user ---//
			$currentUser = $this->oAuth->currentUser();
			$arrMainField['user_id'] = $currentUser['id'];
			
			// --- Set temp enable_seo_url variable ---//
			$arrMainField['enable_seo_url'] = df($this->_rowPageConf['enable_seo_url'], 0);
			$arrMainField['active'] = df($arrMainField['active'], 0);
			
			$arr_temp = array();
			foreach ($this->_confLn['languages'] as $code => $row)
			{
				if (!empty($arrLnImageField))
					$arr_temp[$code] = array_merge($arrLnField[$code], $arrLnImageField[$code]);
				else 
					$arr_temp[$code] = $arrLnField[$code];
			}			
			
			$arrLnField = $arr_temp;
			
			if (!empty($arrMainImageField))
				$arrMainField = array_merge($arrMainField, $arrMainImageField);

			$returnUrl = 'page/content/add/'.$page_code;
			if(empty($content_id))
			{
				// Insert data
				$content_id = $this->_objPageContent->insertContent($this->_page_id, $arrMainField, $arrLnField);
				
				// Notify insert successfully !
				$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
						'msg_code' => "success",
						'msg_content' => "Insert successfully !"));				
			} else
			{
				// Update data
				$this->_objPageContent->updateContent($content_id, $arrMainField, $arrLnField);
				
				// Notify update successfully !
				$this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
						'msg_code' => "success",
						'msg_content' => "Update successfully !"));								
				$returnUrl = 'page/content/update/'.$page_code.'/'.$content_id;
			}
			
			if (df($arrGalleryField['use_gallery'],0) == 1)
			{
				if (!empty($this->oInput->_files["image_gallery"]["name"][0]))
				{
					$file_gallery_data = $this->_upload_files_gallery("image_gallery", $arrGalleryField);
					
					$objGallery = new Gallery();
// 					$objGallery->processImageGallery($content_id, $file_gallery_data, $arrGalleryField);					
					foreach ($file_gallery_data as $file_data)
					{
						$objGallery->insertImageGallery($content_id, $file_data, $arrGalleryField);
					}

				}
			}
			
			redirect($returnUrl);
		}
		
		$this->oView->arrMainField = $arrMainField;
		$this->oView->arrLnField = $arrLnField;
		$this->oView->arrMainImage = $arrMainImage;
		$this->oView->arrLnImage = $arrLnImage;
		
		$this->oView->arrGalleryField = $arrGalleryField;

		$this->renderView('page/content/_form');
	}

    public function deleteContentAction($page_code, $content_id)
    {
        $returnVal['result'] = false;
        $returnVal['message'] = '';
        $returnVal['data'] = '';
        // Load permission
        $this->detectModifyPermission('page/content/'.$page_code);
        if ($this->_isModify) {
            $rowContent = $this->_objPageContent->getRowDataContent($content_id);
            if (!empty($rowContent))
            {
                $result = $this->_objPageContent->deleteContent($content_id);
                if ($result)
                {
                    // Delete image , get info from $rowContent
                    @unlink(__UPLOAD_DATA_PATH.$rowContent['main_field']['icon']);
                    @unlink(__UPLOAD_DATA_PATH.$rowContent['main_field']['image']);
                    @unlink(__UPLOAD_DATA_PATH.'thumb_'.$rowContent['main_field']['image']);
                    foreach ($rowContent['ln_field'] as $ln => $value) {
                        @unlink(__UPLOAD_DATA_PATH.$value['ln_icon']);
                        @unlink(__UPLOAD_DATA_PATH.$value['ln_image']);
                        @unlink(__UPLOAD_DATA_PATH.'thumb_'.$value['ln_image']);
                    }
                }
                $returnVal['result'] = true;
            }
        }
        // -- Demo sleep --
        sleep(1);

        echo json_encode($returnVal);
        exit();
    }


	public function deleteAction($page_code, $content_id)
	{
		// Load permission
		$this->detectModifyPermission('page/content/'.$page_code);		
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');

        $rowContent = $this->_objPageContent->getRowDataContent($content_id);
		if (!empty($rowContent))
		{
			$result = $this->_objPageContent->deleteContent($content_id);
			if ($result)
			{
				// Delete image , get info from $rowContent
                @unlink(__UPLOAD_DATA_PATH.$rowContent['main_field']['icon']);
                @unlink(__UPLOAD_DATA_PATH.$rowContent['main_field']['image']);
                @unlink(__UPLOAD_DATA_PATH.'thumb_'.$rowContent['main_field']['image']);
                foreach ($rowContent['ln_field'] as $ln => $value) {
                    @unlink(__UPLOAD_DATA_PATH.$value['ln_icon']);
                    @unlink(__UPLOAD_DATA_PATH.$value['ln_image']);
                    @unlink(__UPLOAD_DATA_PATH.'thumb_'.$value['ln_image']);
                }
			}
		}
		redirect('page/content/list/'.$page_code);
	}

    public function switchStatusAction($page_code, $content_id)
    {
        $returnVal['result'] = '';
        $returnVal['message'] = '';
        $returnVal['data'] = '';
        // Load permission
        $this->detectModifyPermission('page/content/'.$page_code);
        if (!$this->_isModify)
            $returnVal['result'] = false;
        else
        {
            $this->_objPageContent->setActiveField($content_id);
            $returnVal['result'] = true;
        }

        // -- Demo sleep --
        sleep(1);

        echo json_encode($returnVal);
        exit();
    }

	public function activeAction($page_code, $content_id, $offset = 0)
	{
		// Load permission
		$this->detectModifyPermission('page/content/'.$page_code);
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->_objPageContent->setActiveField($content_id);		
		redirect('page/content/list/'.$page_code.'?offset='.$offset);	
	}

	private function _loadConfigPage($page_code)
	{
		$objPageConf = new Configure();
		$rowPageConf = $objPageConf->getRow("code = ?",array($page_code));
	
		if (empty($rowPageConf))
		{
			echo "<pre>";
			print_r("Khong ton tai Page");
			echo "</pre>";
			exit();
		}
	
		// Load permission
		$this->detectModifyPermission('page/content/'.$page_code);
		
		$this->_page_id = $rowPageConf['id'];
		$rowPageConf['data'] = unserialize($rowPageConf['data']);
		$this->oView->rowPageConf = $this->_rowPageConf = $rowPageConf;
	}	
	
	private function _upload_main_image($arrMainImage)
	{
		$data = array();
		if (df($arrMainImage['choose'],0) == 1) 
		{
			// Do upload image
			if (!empty($this->oInput->_files["image"]['name']))
			{
				$file_image = $this->_do_upload_file("image", $arrMainImage['image']['width'], $arrMainImage['image']['height']);
				// Resize image
				if (df($arrMainImage['image_thumb']['choose'],0) == 1) 
				{
					$this->_create_thumb_image($file_image, $arrMainImage['image_thumb']['width'], $arrMainImage['image_thumb']['height']);					
				}
				$data['image'] = $file_image['file_name'];
			}
			// Do upload icon			
			if (!empty($this->oInput->_files["icon"]['name']))
			{
				$file_icon = $this->_do_upload_file("icon", $arrMainImage['icon']['width'], $arrMainImage['icon']['height']);
				$data['icon'] = $file_icon['file_name'];
			}						
		}
		return $data;
		// return array('icon'=>'','image'=>'');		
	}
	
	private function _upload_ln_image($arrLnImage)
	{
		$data = array();
		if (df($arrLnImage['choose'],0) == 1)
		{
			foreach ($this->_confLn['languages'] as $code => $row)
			{
// 				$sub_data = array('ln_icon'=>'','ln_image'=>'');
				$sub_data = array();
				if (!empty($this->oInput->_files["ln_image"]["name"][$code]["image"]))
				{
					$name_temp = 'ln_image_temp_'.$code;
					$_FILES[$name_temp]['name'] = $_FILES['ln_image']['name'][$code]['image'];
					$_FILES[$name_temp]['type'] = $_FILES['ln_image']['type'][$code]['image'];
					$_FILES[$name_temp]['tmp_name'] = $_FILES['ln_image']['tmp_name'][$code]['image'];
					$_FILES[$name_temp]['error'] = $_FILES['ln_image']['error'][$code]['image'];
					$_FILES[$name_temp]['size'] = $_FILES['ln_image']['size'][$code]['image'];
					
					$file_image = $this->_do_upload_file($name_temp, $arrLnImage['image']['width'], $arrLnImage['image']['height']);
					// Resize image
					if (df($arrLnImage['image_thumb']['choose'],0) == 1) 
					{
						$this->_create_thumb_image($file_image, $arrLnImage['image_thumb']['width'], $arrLnImage['image_thumb']['height']);
					}
					$sub_data['ln_image'] = $file_image['file_name'];
				}
				// Do upload icon			
				if (!empty($this->oInput->_files["ln_image"]["name"][$code]["icon"]))
				{
					$name_temp = 'ln_icon_temp_'.$code;
					$_FILES[$name_temp]['name'] = $_FILES['ln_image']['name'][$code]['icon'];
					$_FILES[$name_temp]['type'] = $_FILES['ln_image']['type'][$code]['icon'];
					$_FILES[$name_temp]['tmp_name'] = $_FILES['ln_image']['tmp_name'][$code]['icon'];
					$_FILES[$name_temp]['error'] = $_FILES['ln_image']['error'][$code]['icon'];
					$_FILES[$name_temp]['size'] = $_FILES['ln_image']['size'][$code]['icon'];
					
					$file_icon = $this->_do_upload_file($name_temp, $arrLnImage['icon']['width'], $arrLnImage['icon']['height']);
					$sub_data['ln_icon'] = $file_icon['file_name'];
				}			
				$data[$code] = $sub_data;
			}
		}
		return $data;		
		// return va $data = array("en" => array('ln_icon' => "", 'ln_image' => ""), "vn" => array('ln_icon' => "", 'ln_image' => ""));
	}

	private function _upload_files_gallery($field, $arrGalleryField)
	{
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_GALLERY_PATH;
		$this->_cfg_upload_file['file_name']  = 'img_'.strtolower(create_uniqid(5)).'_'.time();
		
		$uploadLib = new UploadLib($this->_cfg_upload_file);
		$returnValue = $uploadLib->do_multi_upload($field);
			
		if (empty($returnValue))
		{
			echo $uploadLib->display_errors();
			exit();
		}
		
		return $returnValue;
	}	
	
	private function _do_upload_file($field, $s_width, $s_height)
	{
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['file_name']  = 'img_'.strtolower(create_uniqid(5)).'_'.time();
		$uploadLib = new UploadLib($this->_cfg_upload_file);
	
		if ( ! $uploadLib->do_upload($field))
		{
			echo $uploadLib->display_errors();
			exit();
		}
		else
		{
			$this->_cfg_thumb_image['create_thumb']	= FALSE;			
			$this->_cfg_thumb_image['source_image']	= $uploadLib->data('full_path');
			$this->_cfg_thumb_image['width'] = $s_width;
			$this->_cfg_thumb_image['height'] = $s_height;			
	
			$imageLib = new ImageLib($this->_cfg_thumb_image);
			if($s_width !== 0 && $s_height !== 0)
			{
				$image_config["source_image"] = $uploadLib->data('full_path');
				$image_config['create_thumb'] = FALSE;
				$image_config['new_image'] = $uploadLib->data('full_path');
				$image_config['maintain_ratio'] = FALSE;
				$image_config['quality'] = "100%";
				$image_config['width'] = $s_width;
				$image_config['height'] = $s_height;				
			
				$imageLib->clear();
				$imageLib->initialize($image_config);
				if(!$imageLib->resize_and_crop())
				{
					print_r($imageLib->display_errors());
					exit();
				}
			} else
			{
				if(!$imageLib->resize())
				{
					print_r($imageLib->display_errors());
					exit();
				}
			}
			// Thum image : $imageLib->thumb_marker.$imageLib->source_image
		}
	
		return  $uploadLib->data();
	}

	private function _create_thumb_image($file_image, $s_width, $s_height)
	{
		$this->_cfg_thumb_image['create_thumb']	= TRUE;
		$this->_cfg_thumb_image['source_image']	= $file_image['full_path'];
		$this->_cfg_thumb_image['width'] = $s_width;
		$this->_cfg_thumb_image['height'] = $s_height;
		
		$imageLib = new ImageLib($this->_cfg_thumb_image);
		
		if($s_width !== 0 && $s_height !== 0)
		{
			$image_config["source_image"] = $file_image['full_path'];
			$image_config['create_thumb'] = FALSE;
			$image_config['new_image'] = $file_image["file_path"] . $imageLib->thumb_marker . $file_image['file_name'];;
			$image_config['maintain_ratio'] = FALSE;
			$image_config['quality'] = "100%";
			$image_config['width'] = $s_width;
			$image_config['height'] = $s_height;			
			
			$imageLib->clear();
			$imageLib->initialize($image_config);
			if(!$imageLib->resize_and_crop())
			{
				print_r($imageLib->display_errors());
				exit();
			}
		} else
		{
			if(!$imageLib->resize())
			{
				print_r($imageLib->display_errors());
				exit();
			}
				
		}		
	}

    public function loadDataContentAction($page_code, $cat_id = 0)
    {
        // -- Danh sach cot phai tuong ung voi ds hien thi --
        $aColumns = array('id', 'sort_order', 'name', 'last_update', 'active', 'id');

        // -- Paging --
        $offset = ""; $items_per_page = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $offset = $_GET['iDisplayStart'];
            $items_per_page = $_GET['iDisplayLength'];
        }

        // -- Ordering --
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if (empty(trim($sOrder)))
            {
                $sOrder = NULL;
            }
        }

        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = NULL;
        if ($_GET['sSearch'] != "")
        {
            $sWhere = "(";
            for ($i=0 ; $i<count($aColumns) ; $i++)
            {
                if ($_GET['bSearchable_'.$i] == "true")
                    $sWhere .= $aColumns[$i]." LIKE '%".real_escape_string( $_GET['sSearch'] )."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }

        /* Individual column filtering */
        for ($i=0 ; $i<count($aColumns) ; $i++)
        {
            if ($_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '')
            {
                if ($sWhere == "")
                    $sWhere = "WHERE ";
                else
                    $sWhere .= " AND ";
                $sWhere .= $aColumns[$i]." LIKE '%".real_escape_string($_GET['sSearch_'.$i])."%' ";
            }
        }

        $sOrder = empty($sOrder) ? 'last_update DESC' : $sOrder;

        $rowPageConf = $this->_objPageConf->getRow("code = ?", array($page_code));
        $page_id = $rowPageConf['id'];

        $rowSets = $this->_objPageContent->getListDisplay($page_id, $this->_confLn['default_lang'], $cat_id, $sWhere, $offset, $items_per_page, $sOrder);
        $totalRecord = $this->_objPageContent->getContentTotalRow($page_id, $this->_confLn['default_lang'], $cat_id, $sWhere);

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => $totalRecord, //$iFilteredTotal,
            "aaData" => array()
        );

        foreach ($rowSets as $aRow) {
            $row = array();
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                if ( $aColumns[$i] == "version" )
                {
                    /* Special output formatting for 'version' column */
                    $row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
                }
                else if ( $aColumns[$i] != ' ' )
                {
                    /* General output */
                    $row[] = $aRow[ $aColumns[$i] ];
                }
            }
            $output['aaData'][] = $row;
        }

        // -- Demo sleep 3 second --
//        sleep(3);

        echo json_encode($output);
        exit();
    }

    // TODO : Xoa nhung hinh anh khong co trong DB --
    public function deleteContentImageAction($page_code)
    {
        $rowPageConf = $this->_objPageConf->getRow("code = ?", array($page_code));
        $page_id = $rowPageConf['id'];

        $sql = "SELECT pr.* ,ci.* FROM ".TB_PAGE_CONTENT. " as pr INNER JOIN ".TB_PAGE_CONTENT_LN." as ci ";
        $sql .= " ON pr.id = ci.id ";
        $sql .= " WHERE pr.page_id = ?";
        $rsContent = Db::getInstance()->query($sql, array($page_id));

        $rsContent = $this->_objPageContent->getAllRowsWithPage($page_id);
        echo "<pre>";
        print_r($rsContent);
        echo "</pre>";
        exit();

//        foreach ($rsContent as $content)
//        {
//            if(!empty($content['icon'])) {
//                @unlink(__UPLOAD_DATA_PATH.$content['icon']);
//                echo 'Delete : '.__UPLOAD_DATA_PATH.$content['icon'];
//            }
//            if(!empty($content['image'])) {
//                @unlink(__UPLOAD_DATA_PATH.$content['image']);
//                @unlink(__UPLOAD_DATA_PATH.'thumb_'.$content['image']);
//                echo 'Delete : '.__UPLOAD_DATA_PATH.$content['image'].' & (thumb_)';
//            }
//
//        }
    }

}
