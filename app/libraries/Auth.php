<?php
/**
* Auth class. Used for all login/logout stuff.
*/

namespace App\Lib;

use App\Model\Base\User;
use App\Model\Base\Group;    

class Auth
{
    var $userNameField, $passField, $miscFields, $lastLoggedInField;
    
    var $loggedIn;
    var $homePageUrl, $loginPageUrl, $membersAreaUrl;
    
    var $_session;
    var $_user;
    var $_session_current_name = "session_current_user";
    
    var $_permission = array(); 

    function __construct($registry)
    {
    	$this->_user = new User();

    	$this->_session = $registry->oSession;
    	
        //The fields below should be columns in the table above, which are used to
        //authenticate the user's credentials.
        $this->userNameField = 'username';
        $this->passField = 'password';

        //The following are general columns in the database which are
        //stored in the Session, for easily displaying some information
        //about the user:
        $this->miscFields='id,group_id,is_admin,display_name,first_name,last_name,email,username,password,data,acl_resources,active,last_login,create_at,last_update'; 

        /* If there is a no lastLoggedIn field in the table which is updated
               to the current DATETIME whenever the user logs in, set the next
              variable to blank to disable this feature. */

        $this->lastLoggedInField = 'last_login';

        $this->homePageUrl = site_url();
        $this->loginPageUrl = site_url('common/home/login');
        $this->membersAreaUrl = site_url();

        //All data passed on from a form to this class must be 
        // already escaped to prevent SQL injection.
        //However, all data stored in sessions is escaped by the class.

        if ($this->isLoggedIn())
                $this->refreshInfo();
    }
    
    function isSessLoggedIn()
    {
        if ($this->loggedIn == TRUE)
        	return TRUE;        
        
        $user = $this->escapeStr($this->getUserSession('user'));
        $pass = $this->escapeStr($this->getUserSession('pass'));
        
        if ($this->checkLogin($this->escapeStr($user),$this->escapeStr($pass)))        	
        {
			$this->loggedIn = TRUE;
			return TRUE;
        } 
        else
        {
            $this->loggedIn = FALSE;
            return false;
        }
    }

    function isCookieLoggedIn()
    {
        if (! array_key_exists('user',$_COOKIE) || ! array_key_exists('pass',$_COOKIE))
                return false;
        
        $user = $this->escapeStr($_COOKIE['user']);
        $pass = $this->escapeStr($_COOKIE['pass']);
        
        if ($this->checkLogin($user,$pass))
                $loggedIn = TRUE;
        else
                $loggedIn = FALSE;
        
        if ($loggedIn && ! $this->isSessLoggedIn())
        {
        	$row = $this->_user->getRow('username = ?',array($user));
        	$pass = $row[$this->passField];
        	$this->login($user,$pass);
        }
        
        return $loggedIn;
    }

    function isLoggedIn()
    {
        return ($this->isSessLoggedIn() || $this->isCookieLoggedIn());
    }

    function login($user, $pass ,$remember = FALSE)
    {
    	// Ma hoa
    	$pass = encryption($pass);

        if ($this->isSessLoggedIn())
        	return false;

        if (! $this->checkLogin($user,$pass))
            return false;

        $this->setUserSession('user',$user);        
        $this->setUserSession('pass',$pass);
        
        $row = $this->_user->getRow('username = ? AND password = ?',array($user,$pass));        
        $fields = explode(',',$this->miscFields);
        
        foreach ($fields as $k=>$v)
        {
                $fieldName=$v;
                $fieldVal = $row[$v];
                $this->setUserSession($fieldName,$fieldVal);
        }

        if ($this->lastLoggedInField !='')
        {
        	$condition = "{$this->userNameField} = ? && {$this->passField} = ?";
        	$value = array($this->lastLoggedInField => now_to_mysql());        	
        	$this->_user->updateWithCondition($condition, array($user, $pass), $value);
        }
        
        // Check is group admin
//         $this->setUserSession('is_admin',$this->_user->isAdmin($row['group_id']));
        
        $objGroup = new Group();
        $arrAcl = $objGroup->getAclFromGroup($row['group_id']);
        $this->setUserSession('acl_resources', $arrAcl);
        $this->_permission = $arrAcl;

        if ($remember)
			$this->setCookies();
        
        return true;
    }

    function logout($redir = false)
    {
        if (! $this->isLoggedIn())
			return false;

        $this->_session->clear();

        if ($this->isCookieLoggedIn())
        {
			setcookie('user','', time()-36000, '/');
			setcookie('pass','', time()-36000, '/');
        }
        
        if (! $redir)
			return;

//         header('location: '.$this->homePageUrl);
		redirect($this->homePageUrl);
        die;
    }

    function restrict($minLevel)
    {
        if (! is_numeric($minLevel) && $minLevel!='ADMIN')
                return false;


        //URL of the page the user was trying to access, so upon logging in
        // he is redirected back to this url.
//         $url=$this->obj->uri->uri_string();
//         if (! $this->isLoggedIn())
//         {
//                 $this->obj->session->set_userdata('redirect_url',$url);
//                 header('location: '.$this->loginPageUrl);
//                 die;
//         }

//         if ($this->obj->session->userdata($this->lvlField) < $minLevel)
//         {
//                 header('location: '.$this->membersAreaUrl);
//                 die;
//         }
        return true;
    }

    function setCookies()
    {
        if (! $this->isSessLoggedIn())
        {
                return false;
        }
        $user = $this->getUserSession('user');
        $pass = $this->getUserSession('pass');

        @setcookie('user',$user, time()+60*60*24*30, '/');
        @setcookie('pass',$pass, time()+60*60*24*30, '/');
        return true;
    }

    //This function refreshes all the info in the Session, so if a user changed
    //his name, for example, his name in the Session is updated
    function refreshInfo()
    {
        if (! $this->isLoggedIn())
                return false;
        
        $id = trim($this->getUserSession('id'));
        $row = $this->_user->get($id);
        
        $info['pass'] = $row[$this->passField];
        $info['user'] = $row[$this->userNameField];
        
        $fields = explode(',',$this->miscFields);
        foreach ($fields as $k=>$v)
        {
			$info[$v] = $row[$v];//$query->getSingle($v);
        }
        
        //The following variables are used to determine wether or not to
        //set the cookies on the users computer. If $origUser matches the
        //cookie value 'user' it means the user had cookies stored on his 
        //browser, so the cookies would be re-written with the new value of the
        //username.
        $origUser = $this->getUserSession('user');
        $origPass = $this->getUserSession('pass');
        
        foreach ($info as $k=>$v)
        {
        	$this->setUserSession($k,$v);
        }
        
        // Check is group admin
//         $this->setUserSession('is_admin',$this->_user->isAdmin($row['group_id']));
        
        $objGroup = new Group();
        //         $arrAcl = $objGroup->getAclFromGroup("2,3");
        $arrAcl = $objGroup->getAclFromGroup($row['group_id']);
        $this->setUserSession('acl_resources', $arrAcl);
        $this->_permission = $arrAcl;        

        if (array_key_exists('user',$_COOKIE) && array_key_exists('pass',$_COOKIE))
        {
            if ($_COOKIE['user'] == $origUser && $_COOKIE['pass'] == $origPass)
            	$this->setCookies();
        }
        return true;
    }

    function isAdmin()
    {
        if (! $this->isLoggedIn())
                return false;
        
        return $this->getUserSession("is_admin");
    }           


    function isVerified()
    {
//         return ($this->obj->session->userdata('verified')=='1');
    }
    
    function currentUser()
    {
   		return $this->_session->userdata[$this->_session_current_name];
    }
    
    public function hasPermission($key, $value) 
    {
    	if ($this->isAdmin())
    		return true;

    	if (isset($this->_permission[$key])) 
    	{
    		return in_array($value, $this->_permission[$key]);
    	} else 
    	{
    		return false;
    	}
    }    

    public function getUserSession($key)
    {
    	if (!empty($this->_session->userdata[$this->_session_current_name][$key]))
    	{
    		return $this->_session->userdata[$this->_session_current_name][$key];
    	}
    	return null;
    }
    
    public function setUserSession($key ,$value)
    {
    	$this->_session->userdata[$this->_session_current_name][$key] = $value;
    }
        
    private function checkLogin($user, $pass)
    {
    	$row = $this->_user->getRow('username = ? AND password = ?',array($user,$pass));
    	return empty($row) ? FALSE : TRUE;
    }
    
	private function escapeStr($str)
	{
		return $str;
// 		return trim(mysqli_real_escape_string($str));
	}
}


?>