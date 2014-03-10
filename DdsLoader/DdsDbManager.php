<?php
/**
 * File DdsDbManager.php contains the class DdsDbManager.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2013 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

class DdsDbManager
{

    CONST DDLSQL_PREFIX = "--DDL SQL to create all DDS tables";

    /**
     * Delete all the tables and recreate the schema
     *
     * @param DdsDb       $ddsDb Instance of a DdsDb object
     *
     * @param api_session $sess  Active, connected Intacct API session
     *
     * @throws Exception
     * @return null
     */
    public static function rebuildSchema(DdsDb $ddsDb, api_session $sess)
    {
        // Get the list of tables
        $objects = api_post::getDdsObjects($sess);

        if (count($objects) == 0) {
            throw new Exception("getDdsObjects returned null.  Are you sure DDS is enabled?");
        }

        foreach ($objects as $object) {
            if ($ddsDb->tableExists($object)) {
                $ddsDb->dropTable($object);
            }

            $ddl = api_post::getDdsDdl($sess, $object);
            $ddsDb->execStmt($ddl);
        }

    }

    /**
     * Return the DDL SQL required to create the schema from scratch
     *
     * @param api_session $sess
     * @return string
     * @throws Exception
     *
     */
    public static function getSchemaDdl(api_session $sess)
    {

        // get the list of objects
        $objects = api_post::getDdsObjects($sess);

        if (count($objects) == 0) {
            throw new Exception("getDdsObjects returned null.  Are you sure DDS is enabled?");
        }

        $ddlSql = self::DDLSQL_PREFIX . "\n";
        foreach ($objects as $object) {
            $ddl = api_post::getDdsDdl($sess, $object);
            $ddlSql .= "--Object $object";
            $ddlSql .= $ddl . "\n";
        }

        return $ddlSql;
    }

}