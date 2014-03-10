<?php
/**
 * File DdsDb.php contains the class DdsDb.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2013 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

abstract class DdsDb
{
    abstract public function query($query, $params=array());

    abstract public function execStmt($stmt, $params=array());

    abstract public function tableExists($tableName);

    abstract public function dropTable($tableName);

}