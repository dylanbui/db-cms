<?php
/**
 * Created by PhpStorm.
 * User: dylanbui
 * Date: 11/24/15
 * Time: 12:18 PM
 */

namespace App\Lib\Core\Database\Driver;

class MySqli implements ConnectionInterface
{
    private $mysqli, $stmt;

    public function __construct($hostname, $port, $username, $password, $database)
    {
        try {
            $this->mysqli = new mysqli($hostname, $username, $password, $database, $port);

            //Output any connection error
            if ($this->mysqli->connect_error) {
                die('MySqli Error : ('. $this->mysqli->connect_errno .') '. $this->mysqli->connect_error);
            }
        } catch (\Exception $e) {
            echo 'MySqli Exception: ' . $e->getMessage();
            exit();
        };
    }

    public function errno()
    {
        return $this->mysqli->errno;
    }

    public function error()
    {
        return $this->mysqli->error_list;
    }

    public function escape($value)
    {
        return $this->mysqli->real_escape_string($value);
    }

    public function query($sql, $bindParams = array())
    {
        try {
            $params = array(''); // Create the empty 0 index

            $this->stmt = $this->mysqli->prepare($sql);

            if (is_array($bindParams) === true) {
                foreach ($bindParams as $prop => $val) {
                    $params[0] .= $this->_determineType($val);
                    array_push($params, $bindParams[$prop]);
                }

                call_user_func_array(array($this->stmt, 'bind_param'), $this->refValues($params));
            }

            //execute query
            $this->stmt->execute();

            $this->count = $this->stmt->affected_rows;
            $this->_stmtError = $this->stmt->error;
            $res = $this->_dynamicBindResults($this->stmt);

            return $res;

        } catch (\Exception $e) {
            echo 'MySqli Exception: ' . $e->getMessage();
            exit();
        };
    }

    public function selectOneRow($sql, $data = array())
    {

    }

    public function insert($sql, $data = array())
    {

    }

    public function update($sql, $data = array())
    {

    }

    public function delete($sql, $data = array())
    {

    }

    public function replace($sql, $data = array())
    {

    }

    public function countAffected()
    {
        return $this->mysqli->affected_rows();
    }

    public function getLastId()
    {
        return $this->mysqli->insert_id;
    }

    public function close()
    {
        $this->stmt = null;
        return $this->mysqli->close();
    }


    /**
     * This helper method takes care of prepared statements' "bind_result method
     * , when the number of variables to pass is unknown.
     *
     * @param mysqli_stmt $stmt Equal to the prepared statement object.
     *
     * @return array The results of the SQL fetch.
     */
    protected function _dynamicBindResults(mysqli_stmt $stmt)
    {
        $parameters = array();
        $results = array();
        /**
         * @see http://php.net/manual/en/mysqli-result.fetch-fields.php
         */
        $mysqlLongType = 252;
        $shouldStoreResult = false;
        $meta = $stmt->result_metadata();
        // if $meta is false yet sqlstate is true, there's no sql error but the query is
        // most likely an update/insert/delete which doesn't produce any results
        if (!$meta && $stmt->sqlstate)
            return array();
        $row = array();
        while ($field = $meta->fetch_field()) {
            if ($field->type == $mysqlLongType) {
                $shouldStoreResult = true;
            }
            if ($this->_nestJoin && $field->table != $this->_tableName) {
                $field->table = substr($field->table, strlen(self::$prefix));
                $row[$field->table][$field->name] = null;
                $parameters[] = & $row[$field->table][$field->name];
            } else {
                $row[$field->name] = null;
                $parameters[] = & $row[$field->name];
            }
        }
        // avoid out of memory bug in php 5.2 and 5.3. Mysqli allocates lot of memory for long*
        // and blob* types. So to avoid out of memory issues store_result is used
        // https://github.com/joshcam/PHP-MySQLi-Database-Class/pull/119
        if ($shouldStoreResult) {
            $stmt->store_result();
        }
        call_user_func_array(array($stmt, 'bind_result'), $parameters);
        $this->totalCount = 0;
        $this->count = 0;
        while ($stmt->fetch()) {
            $result = array();
            foreach ($row as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $result[$key][$k] = $v;
                    }
                } else {
                    $result[$key] = $val;
                }
            }
            $this->count++;
            if ($this->_mapKey) {
                $results[$row[$this->_mapKey]] = count($row) > 2 ? $result : end($result);
            } else {
                array_push($results, $result);
            }
        }
        if ($shouldStoreResult) {
            $stmt->free_result();
        }
        $stmt->close();
        // stored procedures sometimes can return more then 1 resultset
        if ($this->mysqli()->more_results()) {
            $this->mysqli()->next_result();
        }
        return $results;
    }

    /**
     * This method is needed for prepared statements. They require
     * the data type of the field to be bound with "i" s", etc.
     * This function takes the input, determines what type it is,
     * and then updates the param_type.
     *
     * @param mixed $item Input to determine the type.
     *
     * @return string The joined parameter types.
     */
    protected function _determineType($item)
    {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'boolean':
            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }
        return '';
    }

    /**
     * Dynamic array that holds a combination of where condition/table data value types and parameter references
     * @var array
     */
    protected $_bindParams = array(''); // Create the empty 0 index
    /**
     * Variable which holds an amount of returned rows during get/getOne/select queries
     * @var string
     */
    public $count = 0;
    /**
     * Variable which holds an amount of returned rows during get/getOne/select queries with withTotalCount()
     * @var string
     */
    public $totalCount = 0;
    /**
     * Variable which holds last statement error
     * @var string
     */
    protected $_stmtError;







}