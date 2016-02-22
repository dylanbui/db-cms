<?php

namespace App\Model\Base;

class UrlAlias extends Model 
{
	protected $_table_name = TB_URL_ALIAS;
	protected $_primary_key = 'id';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function replaceUrlAlias($data)
	{
		$row = $this->getRow('query = ?',array($data['query']));
		if (empty($row))
			$this->insert($data);
		else
			$this->updateWithCondition("query = ?", array($data['query']), array("keyword"=>$data['keyword']));
	}

	
}

?>