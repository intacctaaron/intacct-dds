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
    abstract protected function query($query, $params);

    abstract protected function execStmt($stmt, $params);

    abstract protected function tableExists($tableName);

}