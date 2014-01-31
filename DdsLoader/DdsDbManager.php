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
    private $ddsDb;

    /**
     * @param DdsDb $ddsDb
     */
    public function __construct(DdsDb $ddsDb)
    {
        $this->ddsDb = $ddsDb;
    }

}