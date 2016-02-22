<?php

namespace App\Model\Page;

define ('__DF_GALLERY_THUMB_W', 200);
define ('__DF_GALLERY_THUMB_H', 170);

class Gallery extends Model 
{
	protected $_table_name = TB_PAGE_GALLERY;
	protected $_primary_key = 'id';	
	
	private $_cfg_thumb_image;
	
	function __construct()
	{
		parent::__construct();
		
		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['maintain_ratio'] = TRUE;
		$this->_cfg_thumb_image['width'] = __DF_GALLERY_THUMB_W;
		$this->_cfg_thumb_image['height'] = __DF_GALLERY_THUMB_H;
				
	}
	
	public function insertGallery($content_id ,$upload_images_data)
	{
		$rs = $this->getRowset("content_id = ?", array($content_id), "sort_order DESC");
		$i = 0;
		if(!empty($rs))
			$i = $rs[0]['id'];
		
		$data['content_id'] = $content_id;
		
		foreach ($upload_images_data as $image)
		{
			$i++;
			$data['uniqid'] = create_uniqid(10);			
			$data['image'] = $image['file_name'];
			$data['sort_order'] = $i;
			$data['create_at'] = now_to_mysql();
			parent::insert($data);
		}
	}
	
	public function processImageGallery($content_id ,$upload_images_data ,$arr_gallery_field)
	{
		$imageLib = new \App\Lib\ImageLib($this->_cfg_thumb_image);
		foreach ($upload_images_data as $fileData)
		{
			$this->_cfg_thumb_image['create_thumb'] = TRUE;
			$this->_cfg_thumb_image['source_image']	= $fileData['full_path'];
	
			// Tao hinh anh mac dinh de hien thi trong gallery
			
			$image_config["source_image"] = $fileData['full_path'];
			$image_config['new_image'] = $fileData["file_path"] . 'gal_' . $fileData['file_name'];
			$image_config['maintain_ratio'] = FALSE;
			$image_config['quality'] = "100%";
			$image_config['width'] = __DF_GALLERY_THUMB_W;
			$image_config['height'] = __DF_GALLERY_THUMB_H;
			
			$imageLib->clear();
			$imageLib->initialize($image_config);
			if(!$imageLib->resize_and_crop())
			{
				print_r($imageLib->display_errors());
// 				return FALSE;
// 				exit();
			}			
	
			if (df($arr_gallery_field['icon']['choose'],0) == 1)
			{
				$this->_cfg_thumb_image['thumb_marker'] = "icon_";
				$this->_cfg_thumb_image['width'] = intval($arr_gallery_field['icon']['width']);
				$this->_cfg_thumb_image['height'] = intval($arr_gallery_field['icon']['height']);
				
				if(!$this->_doProcessImage($this->_cfg_thumb_image, $fileData))
				{
					print_r($imageLib->display_errors());
// 					return FALSE;
				}
			}
	
			if (df($arr_gallery_field['image_thumb']['choose'],0) == 1)
			{
				$this->_cfg_thumb_image['thumb_marker'] = "thumb_";
				$this->_cfg_thumb_image['width'] = intval($arr_gallery_field['image_thumb']['width']);
				$this->_cfg_thumb_image['height'] = intval($arr_gallery_field['image_thumb']['height']);
	
				if(!$this->_doProcessImage($this->_cfg_thumb_image, $fileData))
				{
					print_r($imageLib->display_errors());
// 					return FALSE;
				}
			}
	
			if (df($arr_gallery_field['image']['choose'],0) == 1)
			{
				$this->_cfg_thumb_image['thumb_marker'] = '';	
				$this->_cfg_thumb_image['create_thumb'] = FALSE;
				$this->_cfg_thumb_image['width'] = intval($arr_gallery_field['image']['width']);
				$this->_cfg_thumb_image['height'] = intval($arr_gallery_field['image']['height']);
	
				if(!$this->_doProcessImage($this->_cfg_thumb_image, $fileData))
				{
					print_r($imageLib->display_errors());
// 					return FALSE;
				}
			}
				
				
		}
	
		// Insert data
		$this->insertGallery($content_id, $upload_images_data);
	}
	
	private function _doProcessImage($image_config, $image_data)
	{
// 		$image_config["source_image"] = $image_data['full_path'];
// 		$image_config['new_image'] = $image_data["file_path"] . 'gal_' . $fileData['file_name'];
// 		$image_config['maintain_ratio'] = FALSE;
// 		$image_config['quality'] = "100%";
// 		$image_config['width'] = __DF_GALLERY_THUMB_W;
// 		$image_config['height'] = __DF_GALLERY_THUMB_H;

// 		echo "<pre>";
// 		print_r($image_config);
// 		echo "</pre>";
	
		
		$imageLib = new \App\Lib\ImageLib($image_config);
		if($image_config['width'] !== 0 && $image_config['height'] !== 0)
		{
			$image_config["source_image"] = $image_data['full_path'];
			$image_config['create_thumb'] = FALSE;
			$image_config['new_image'] = $image_data["file_path"] . $image_config["thumb_marker"] . $image_data['file_name'];
			$image_config['maintain_ratio'] = FALSE;
			$image_config['quality'] = "100%";

			$imageLib->clear();
			$imageLib->initialize($image_config);
			if(!$imageLib->resize_and_crop())
			{
				print_r($imageLib->display_errors());
				return FALSE;
				exit();
			}			
		} else
		{
			if(!$imageLib->resize())
			{
				print_r($imageLib->display_errors());
				return FALSE;
				exit();
			}			
			
		}
		return TRUE;
	}
	
	public function deleteImageGallery($gallery_id)
	{
		$rowGallery = $this->get($gallery_id);
		
		// Before : Delete Row
		$this->delete($gallery_id);
		
		// Before : Delete Image
		if(file_exists(__UPLOAD_GALLERY_PATH.'icon_'.$rowGallery['image']))
			@unlink(__UPLOAD_GALLERY_PATH.'icon_'.$rowGallery['image']);
		if(file_exists(__UPLOAD_GALLERY_PATH.'thumb_'.$rowGallery['image']))
			@unlink(__UPLOAD_GALLERY_PATH.'thumb_'.$rowGallery['image']);
		if(file_exists(__UPLOAD_GALLERY_PATH.'gal_'.$rowGallery['image']))
			@unlink(__UPLOAD_GALLERY_PATH.'gal_'.$rowGallery['image']);
		if(file_exists(__UPLOAD_GALLERY_PATH.$rowGallery['image']))
			@unlink(__UPLOAD_GALLERY_PATH.$rowGallery['image']);		
	}
	
	public function insertImageGallery($content_id ,$upload_image_data ,$arr_gallery_field)
	{
		$imageLib = new \App\Lib\ImageLib($this->_cfg_thumb_image);
		
		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['source_image']	= $upload_image_data['full_path'];

		// Tao hinh anh mac dinh de hien thi trong gallery
			
		$image_config["source_image"] = $upload_image_data['full_path'];
		$image_config['new_image'] = $upload_image_data["file_path"] . 'gal_' . $upload_image_data['file_name'];
		$image_config['maintain_ratio'] = FALSE;
		$image_config['quality'] = "100%";
		$image_config['width'] = __DF_GALLERY_THUMB_W;
		$image_config['height'] = __DF_GALLERY_THUMB_H;
			
		$imageLib->clear();
		$imageLib->initialize($image_config);
		if(!$imageLib->resize_and_crop())
		{
// 			print_r($imageLib->display_errors());
// 			return FALSE;
// 			exit();
		}
	
		if (df($arr_gallery_field['icon']['choose'],0) == 1)
		{
			$this->_cfg_thumb_image['thumb_marker'] = "icon_";
			$this->_cfg_thumb_image['width'] = intval($arr_gallery_field['icon']['width']);
			$this->_cfg_thumb_image['height'] = intval($arr_gallery_field['icon']['height']);

			if(!$this->_doProcessImage($this->_cfg_thumb_image, $upload_image_data))
			{
// 				print_r($imageLib->display_errors());
// 				return FALSE;
			}
		}

		if (df($arr_gallery_field['image_thumb']['choose'],0) == 1)
		{
			$this->_cfg_thumb_image['thumb_marker'] = "thumb_";
			$this->_cfg_thumb_image['width'] = intval($arr_gallery_field['image_thumb']['width']);
			$this->_cfg_thumb_image['height'] = intval($arr_gallery_field['image_thumb']['height']);

			if(!$this->_doProcessImage($this->_cfg_thumb_image, $upload_image_data))
			{
// 				print_r($imageLib->display_errors());
// 				return FALSE;
			}
		}
	
		if (df($arr_gallery_field['image']['choose'],0) == 1)
		{
			$this->_cfg_thumb_image['thumb_marker'] = '';
			$this->_cfg_thumb_image['create_thumb'] = FALSE;
			$this->_cfg_thumb_image['width'] = intval($arr_gallery_field['image']['width']);
			$this->_cfg_thumb_image['height'] = intval($arr_gallery_field['image']['height']);

			if(!$this->_doProcessImage($this->_cfg_thumb_image, $upload_image_data))
			{
// 				print_r($imageLib->display_errors());
// 				return FALSE;
			}
		}
	
		// Insert data into gallery table
		$data['content_id'] = $content_id;
		$data['uniqid'] = create_uniqid(10);
		$data['image'] = $upload_image_data['file_name'];
		$data['create_at'] = now_to_mysql();
		
		$id = parent::insert($data);
		parent::update($id, array('sort_order' => $id));
		
		$data['id'] = $id;
		return $data;
	}	
	

// 	public function processImageGallery($content_id ,$upload_images_data ,$arr_gallery_field)
// 	{
// 		$imageLib = new ImageLib($this->_cfg_thumb_image);
// 		foreach ($upload_images_data as $fileData)
// 		{
// 			$this->_cfg_thumb_image['create_thumb'] = TRUE;
// 			$this->_cfg_thumb_image['source_image']	= $fileData['full_path'];
		
// 			// Tao hinh anh mac dinh de hien thi trong gallery
// 			$this->_cfg_thumb_image['thumb_marker'] = "gal_";			
// 			$imageLib->initialize($this->_cfg_thumb_image);
// 			if ( ! $imageLib->resize())
// 			{
// 				echo $imageLib->display_errors();
// 				exit();
// 			}
		
// 			if (df($arr_gallery_field['icon']['choose'],0) == 1)
// 			{
// 				$this->_cfg_thumb_image['thumb_marker'] = "icon_";
// 				$this->_cfg_thumb_image['width'] = $arr_gallery_field['icon']['width'];
// 				$this->_cfg_thumb_image['height'] = $arr_gallery_field['icon']['height'];
				
// 				$imageLib->initialize($this->_cfg_thumb_image);
// 				if ( ! $imageLib->resize())
// 				{
// 					echo $imageLib->display_errors();
// 					exit();
// 				}
// 			}
		
// 			if (df($arr_gallery_field['image_thumb']['choose'],0) == 1)
// 			{
// 				$this->_cfg_thumb_image['thumb_marker'] = "thumb_";
// 				$this->_cfg_thumb_image['width'] = $arr_gallery_field['image_thumb']['width'];
// 				$this->_cfg_thumb_image['height'] = $arr_gallery_field['image_thumb']['height'];
				
// 				$imageLib->initialize($this->_cfg_thumb_image);
// 				if ( ! $imageLib->resize())
// 				{
// 					echo $imageLib->display_errors();
// 					exit();
// 				}
// 			}
		
// 			if (df($arr_gallery_field['image']['choose'],0) == 1)
// 			{
// 				$this->_cfg_thumb_image['create_thumb'] = FALSE;
// 				$this->_cfg_thumb_image['width'] = $arr_gallery_field['image']['width'];
// 				$this->_cfg_thumb_image['height'] = $arr_gallery_field['image']['height'];
				
// 				$imageLib->initialize($this->_cfg_thumb_image);
// 				if ( ! $imageLib->resize())
// 				{
// 					echo $imageLib->display_errors();
// 					exit();
// 				}
// 			}
			
			
// 		}
		
// 		// Insert data
// 		$this->insertGallery($content_id, $upload_images_data);
// 	}	
	
}

?>