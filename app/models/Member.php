<?php

namespace App\Model;

use App\Model\Base\Model;

class Member extends Model
{
	protected $_table_name = TB_MEMBER;
	protected $_primary_key = 'id';
	
	
	public function __construct()
	{
		parent::__construct();
	}

    public function auth($email, $password)
    {
        return $this->getRow('email = ? AND password = ?', array($email, encryption($password)));
    }

	
}
	
?>