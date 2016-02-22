<?php
/**
 * Created by PhpStorm.
 * User: dylanbui
 * Date: 11/24/15
 * Time: 12:18 PM
 */

namespace App\Lib\Core\Database\Driver;

class Pdo implements ConnectionInterface
{
    // Database connection object
    private $pdo, $stmt;

    // Create a PDO object and connect to the database
    public function __construct($hostname, $port, $username, $password, $database)
    {
        try {
            $this->pdo = new \PDO("mysql:host=$hostname;port=$port;dbname=$database", $username, $password);
//            // Set some options
//            // Return rows found, not changed, during inserts/updates
//            PDO::MYSQL_ATTR_FOUND_ROWS => true,
//            // Emulate prepares, in case the database doesn't support it
//            PDO::ATTR_EMULATE_PREPARES => true,
//            // Have errors get reported as exceptions, easier to catch
//            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//            // Return associative arrays, good for JSON encoding
//            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->query("SET NAMES 'UTF8'");
        } catch(\PDOException $e) {
            echo 'PDOException: ' . $e->getMessage();
            exit();
        }
    }

    public function __destruct()
    {
        // -- Close Pdo connection --
        $this->close();
    }

    public function selectOneRow($sql, $data = array())
    {
        try {
            // Prepare the SQL statement
            $this->stmt = $this->pdo->prepare($sql);
            // Execute the statement
            if ($this->stmt->execute($data)) {
                // Return the selected data as an assoc array
                return $this->stmt->fetch(\PDO::FETCH_ASSOC);
            }
            else {
                return false;
            }
        }
        catch (\PDOException $e) {
            echo 'PDOException: ' . $e->getMessage();
            exit();
        }
    }

    public function query($sql, $data = array())
    {
        try {
            // Prepare the SQL statement
            $this->stmt = $this->pdo->prepare($sql);
            // Execute the statement
            if ($this->stmt->execute($data)) {
                // -- Only run when SELECT query --
                // Return the selected data as an assoc array
                if ($this->stmt->columnCount() > 0)
                    return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            return false;
        } catch(\PDOException $e) {
            echo 'PDOException: ' . $e->getMessage();
            exit();
        }
    }

    // Perform an INSERT query
    public function insert($sql, $data = array()) {
        try {
            // Prepare the SQL statement
            $this->stmt = $this->pdo->prepare($sql);
            // Execute the statement
            // bind the params
//            foreach($data as $k=>$v)
//                $this->stmt->bindParam($k, $v);
            if ($this->stmt->execute($data)) {
                // Return the number of rows affected
                return $this->stmt->rowCount();
            }
            else {
                return false;
            }
        }
        catch (\PDOException $e) {
            echo 'PDOException: ' . $e->getMessage();
            exit();
        }
    }

    // Perform an UPDATE query
    public function update($sql, $data = array()) {
        return $this->insert($sql, $data);
    }

    // Perform a REPLACE query
    public function replace($sql, $data = array()) {
        return $this->insert($sql, $data);
    }

    // Perform a DELETE query
    public function delete($sql, $data = array()) {
        return $this->insert($sql, $data);
    }

    public function errno()
    {
        return $this->pdo->errorCode();
    }

    public function error()
    {
        return $this->pdo->errorCode();
    }

    public function escape($value)
    {
        return $this->pdo->quote($value);
    }

    public function countAffected()
    {
        return $this->stmt->rowCount();
    }

    public function getLastId()
    {
        return $this->pdo->lastInsertId();
    }

    public function close()
    {
        $this->pdo = null;
        $this->stmt = null;
        return true;
    }
}