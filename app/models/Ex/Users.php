<?php

namespace App\Model\Ex;

use App\Lib\Core\Model;

class Users extends Model 
{
	protected $_table_name = TB_EX_USER;
	protected $_primary_key = 'user_id';
	
	public function __construct()
	{
		parent::__construct();
	}

	
}

?>