<?php

namespace App\Model;

class Contact extends \App\Model\Base\Model
{
	protected $_table_name = TB_CONTACT;
	protected $_primary_key = 'user_id';
	
	public function __construct()
	{
		parent::__construct();
	}

	
}
	
?>