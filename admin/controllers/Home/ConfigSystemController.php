<?php

namespace Admin\Controller\Home;

use App\Lib\MysqlDump,
	App\Model\Base\ConfigureSystem;

class ConfigSystemController extends \Admin\Controller\AdminController
{

	public function __construct()
	{
		parent::__construct();
		$this->detectModifyPermission('home/config-system');
		$this->oView->_isModify = $this->_isModify;		
	}

	public function indexAction() 
	{
		return $this->forward('home/config-system/list');
	}	
	
	public function listAction($tab_pos = 0)
	{
		$oConfigSys = new ConfigureSystem();
		$data = $oConfigSys->getAllGroups();

        $this->oView->tab_pos = $tab_pos;
		$this->oView->arrConfigData = $data;
		$this->oView->save_link = site_url('home/config-system/save');
		
		$this->renderView('home/config-system/list');
	}
	
	public function saveAction($group_id)
    {
        if (!$this->_isModify)
            return $this->forward('common/error/error-deny');

        $tab_pos = 0;
        if ($this->oInput->isPost()) {
            $tab_pos = $this->oInput->post('tab_pos', 0);
            $post = $this->oInput->_post;
            $oConfigSys = new ConfigureSystem();
            foreach ($post as $key => $value) {
                $num = $oConfigSys->updateConfigSystem($group_id, $key, array("value" => $value));
            }
            // Notify update successfully !
            $this->oSession->set_flashdata('notify_msg',array('msg_title' => "Notify",
                'msg_code' => "success",
                'msg_content' => "Update successfully !"));
        }

		redirect('home/config-system/list/'.$group_id);
	}
	
	public function backupDbAction()
	{
		if (!$this->_isModify)
			return $this->forward('common/error/error-deny');
				
		$this->oView->box_title = "Backup Database";
		$this->oView->box_action = "List Backup Files";
		
		if ($this->oInput->isPost()) 
		{
			$hostname = $this->oConfig->config_values['database_master']['db_hostname'];
			$database = $this->oConfig->config_values['database_master']['db_name'];
			$username = $this->oConfig->config_values['database_master']['db_username'];
			$password = $this->oConfig->config_values['database_master']['db_password'];
			
			$time = str_replace(array('-',':',' '), "_", now_to_mysql());
			
			$dumpSettings['compress'] = $this->oInput->post('backup_type');
			
			try {
				$dump = new MysqlDump($database, $username, $password, $hostname, "mysql", $dumpSettings, array());
				$dump->start(__SITE_PATH.'/sql/backup_'.$time.'_mysql.sql');
			} catch (Exception $e) {
				echo 'mysqldump-php error: ' . $e->getMessage();
				exit();
			}
			
			redirect('home/config-system/backup-db');
		}
		
		$this->oView->upload_dir = __SITE_PATH.'/sql';
		$this->renderView ('home/config-system/backup-db');
	
	}

	public function importDbAction($filename)
	{
		$database = $this->oConfig->config_values['database_master']['db_name'];
		$username = $this->oConfig->config_values['database_master']['db_username'];
		$password = $this->oConfig->config_values['database_master']['db_password'];
		
		$restore_file = __SITE_PATH.'/sql/'.$filename;
		
		if (preg_match("/\.gz$/i",$filename))
		{
			$source_file = __SITE_PATH.'/sql/'.$filename;
			$restore_file = str_replace('.gz', '', $source_file);;
			
			$fp = fopen($restore_file, "w");
			fwrite($fp, implode("", gzfile($source_file)));
			fclose($fp);
		}
		
		#Now restore from the .sql file
		$command = "mysql --user={$username} --password={$password} --database={$database} < ".$restore_file;
		exec($command);

		redirect('home/config-system/backup-db');
	}

	public function deleteDbAction($filename)
	{
		unlink(__SITE_PATH.'/sql/'.$filename);
		redirect('home/config-system/backup-db');
	}

	public function dumpDbAction()
	{
		$database = $this->oConfig->config_values['database_master']['db_name'];
		$username = $this->oConfig->config_values['database_master']['db_username'];
		$password = $this->oConfig->config_values['database_master']['db_password'];
		//Gzip
		$time = str_replace(array('-',':',' '), "_", now_to_mysql());
	
		try {
			$dump = new MysqlDump($database, $username, $password);
			$dump->start(__SITE_PATH.'/sql/backup_'.$time.'_mysql.sql');
		} catch (Exception $e) {
			echo 'mysqldump-php error: ' . $e->getMessage();
		}
	
		echo "<pre>";
		print_r("Xonggggg");
		echo "</pre>";
		exit();
	}
	
	public function runCommanderAction()
	{
		$arr = array();
		$str = exec("dir", $arr);
	
		echo "<pre>";
		print_r(__SITE_PATH);
		echo "</pre>";
	
		echo "<pre>";
		print_r($str);
		echo "</pre>";
	
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		exit();
	
	
	}
	
	public function bigDumpAction()
	{
		$this->oView->backup_dir = __SITE_PATH.'/sql';
		
		$database = $this->oConfig->config_values['database_master']['db_name'];
		$username = $this->oConfig->config_values['database_master']['db_username'];
		$password = $this->oConfig->config_values['database_master']['db_password'];
		//Gzip
		$time = str_replace(array('-',':',' '), "_", now_to_mysql());
		
		echo "<pre>";
		print_r($_REQUEST);
		echo "</pre>";
		exit();
	
		$this->oView->database = $database;
		$this->oView->username = $username;
		$this->oView->password = $password;
	
		echo $this->oView->fetch('home/config-system/big-dump');
		exit();
	
		// 		$this->renderView ('index/search/big-dump');
	}
	

}
