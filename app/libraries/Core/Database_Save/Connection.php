<?php
/**
 * Created by PhpStorm.
 * User: dylanbui
 * Date: 11/24/15
 * Time: 12:18 PM
 */

/**
 *
 * @Singleton to create database connection
 *
 *
 */

namespace App\Lib\Core\Database;

use App\Lib\Core\Config;

class Connection
{

    /**
     * Holds an array insance of self
     * @var $instance
     */
    private static $instances = array();

    /**
     *
     * the constructor is set to private so
     * so nobody can create a new instance using new
     *
     */
    private function __construct()
    {
    }

    /**
     *
     * Return DB instance or create intitial connection
     *
     * @return object (PDO)
     *
     * @access public
     *
     */
    public static function getInstance($config_name = 'database_master')
    {
        if (!isset(self::$instances[$config_name]))
        {
            $config = Config::getInstance();
            $db_driver = $config->config_values[$config_name]['db_driver'];
            $hostname = $config->config_values[$config_name]['db_hostname'];
            $db_name = $config->config_values[$config_name]['db_name'];
            $db_password = $config->config_values[$config_name]['db_password'];
            $db_username = $config->config_values[$config_name]['db_username'];
            $db_port = $config->config_values[$config_name]['db_port'];

            try {
                $class = __NAMESPACE__. '\Driver\\'.$db_driver;
                self::$instances[$config_name] = new $class($hostname, $db_port, $db_username, $db_password, $db_name);
            } catch (\Exception $ex)
            {
                echo 'ERROR: ' . $ex->getMessage();
                exit();
            }
        }
        return self::$instances[$config_name];
    }


    /**
     *
     * Like the constructor, we make __clone private
     * so nobody can clone the instance
     *
     */
    private function __clone()
    {
    }

} // end of class