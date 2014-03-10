<?php
/**
 * File DdsDbRedshift.php contains the class DdsDbRedshift.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2013 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

include_once 'DdsDbPostgres.php';

/**
 * Class DdsDbRedshift
 */
class DdsDbRedshift extends DdsDbPostgres
{

    /**
     * Create instance of DdsLoader class.  Pass valid connection parameters
     *
     * @param string $host Postgres host URL
     * @param string $db   Database name on the Postgres server
     * @param string $user Database user
     * @param string $pwd  User password
     * @param string $port Optional.  The connection port.
     *
     * @throws Exception
     */
    public function __construct($host, $db, $user, $pwd, $port='5439')
    {
        $pgConn = pg_connect("host=$host dbname=$db port=$port user=$user password=$pwd");
        if ($pgConn === false) {
            throw new Exception(pg_last_error($pgConn));
        }
        $this->dbConn = $pgConn;
    }
    /**
     * Perform a complete load on an object
     *
     * @param string       $object the Object to load
     *
     * @param DdsStorageS3 $s3     instance of a storage object
     *
     * @return null
     */
    public function loadAll($object, DdsStorageS3 $s3)
    {
        // first delete all rows
        $deleteSql = "delete from $object";
        $this->execStmt($deleteSql);

        $path = ($s3->getPath() !== '') ? '/' . $s3->getPath() . '/' . $object : '/' . $object . '.all';

        $sql = "copy $object from 's3://" . $s3->getBucket() . $path . "' credentials 'aws_access_key_id=" . $s3->getKeyId() . ";aws_secret_access_key=" . $s3->getKey() .
            "' timeformat 'YYYY-MM-DDTHH:MI:SSZ' removequotes ignoreheader 1 delimiter ',';";
        try {
            $this->execStmt($sql);
        } catch (Exception $ex) {
            $sql = "select * from stl_load_errors where starttime = (select max(starttime) from stl_load_errors);";
            $errors = $this->query($sql);
            throw new Exception("Unable to run copy command.  Errors:\n" . var_export($errors, true));
        }
    }

    /**
     * Clean up the RedShift connection
     *
     */
    function __destruct()
    {
        // close the connection
        pg_close($this->dbConn);
    }

}