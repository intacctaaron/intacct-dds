<?php
/**
 * File DdsDbPostgres.php contains the class DdsLoader.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2014 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

require_once 'DdsDb.php';

/**
 * Class DdsDbPostgres
 */
class DdsDbPostgres extends DdsDb
{

    /**
     * @var resource
     */
    protected $dbConn;

    /**
     * Create instance of DdsLoader class.  Pass valid connection parameters
     *
     * @param string $host Postgres host URL
     * @param string $db   Database name on the Postgres server
     * @param string $user Database user
     * @param string $pwd  User password
     * @param string $port Optional.  The connection port.  Use 5439 for Amazon RedShift
     *
     * @throws Exception
     */
    public function __construct($host, $db, $user, $pwd, $port='5432')
    {
        $pgConn = pg_connect("host=$host dbname=$db port=$port user=$user password=$pwd");
        if ($pgConn === false) {
            throw new Exception(pg_last_error($pgConn));
        }
        $this->dbConn = $pgConn;
    }

    /**
     * Execute a query and return the results as an associative array
     *
     * @param string $query  Query in SQL.
     * @param array  $params Optional array of query parameters
     *
     * @throws Exception
     * @return array
     */
    public function query($query, $params=array())
    {
        $queryRes = pg_query_params($this->dbConn, $query, $params);
        if ($queryRes === false) {
            throw new Exception(pg_last_error($this->dbConn));
        }

        $rows = pg_fetch_all($queryRes);
        pg_free_result($queryRes);
        return ($rows === false) ? null : $rows;
    }

    /**
     * Execute a statement and return the number of rows affected
     *
     * @param string $stmt   SQL statement to execute
     * @param array  $params Optional array of statement parameters
     *
     * @return int
     * @throws Exception
     */
    public function execStmt($stmt, $params=array())
    {
        $stmtRes = pg_query_params($this->dbConn, $stmt, $params);
        if ($stmtRes === false) {
            throw new Exception(pg_last_error($this->dbConn));
        }
        return pg_affected_rows($stmtRes);

    }

    /**
     * Determines whether or not a table exists
     *
     * @param string $tableName name of the table to check
     *
     * @return bool
     *
     */
    public function tableExists($tableName)
    {
        static $tables;

        if ($tables === null || count($tables) == 0) {
            $query = 'select table_name from information_schema.tables where table_schema = $1';
            $tableList = $this->query($query, array('public'));
            if (count($tableList) == 0) {
                $tables = array();
            } else {
                foreach ($tableList as $table) {
                    $tables[] = $table['table_name'];
                }
            }
        }

        return in_array(strtolower($tableName), $tables);

    }

    /**
     * Drop a table
     *
     * @param string $tableName name of table to drop
     *
     * @return null
     */
    public function dropTable($tableName)
    {
        $query = "drop table $tableName";
        $this->execStmt($query);
    }

}