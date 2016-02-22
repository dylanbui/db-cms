<?php
/**
 * Created by PhpStorm.
 * User: dylanbui
 * Date: 11/24/15
 * Time: 12:18 PM
 */

/**
 *
 * @Lite weight Database abstraction layer
 * @Singleton to create database connection
 *
 *
 */

namespace App\Lib\Core\Database\Driver;


interface ConnectionInterface
{
    public function __construct($hostname, $port, $username, $password, $database);

    public function selectOneRow($sql, $data = array());
    public function query($sql, $data = array());
    public function insert($sql, $data = array());
    public function update($sql, $data = array());
    public function delete($sql, $data = array());
    public function replace($sql, $data = array());

    public function escape($value);
    public function countAffected();
    public function getLastId();
    public function close();

//    function transactionBegin();
//    function transactionCommit();
//    function transactionRollback();
}