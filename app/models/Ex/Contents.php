<?php

namespace App\Model\Ex;

use App\Lib\Core\Model;

class Contents extends Model 
{
	protected $_table_name = TB_EX_CONTENT;
	protected $_primary_key = 'id';
	
	public function __construct()
	{
		parent::__construct();
	}

	
}

?>