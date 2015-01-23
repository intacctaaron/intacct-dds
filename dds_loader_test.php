<?php
/**
 * File dds_loader_test.php contains the class dds_loader_test.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2013 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

include_once 'DdsLoader/DBs/DdsDbRedshift.php';
include_once 'DdsLoader/storage/DdsStorageS3.php';

try {
    // get the DB connection
    $intacctPg = new DdsDbRedshift(
        $_SERVER['RedShiftURL'],
        $_SERVER['RedShiftDB'],
        $_SERVER['RedShiftUser'],
        $_SERVER['RedShiftPWD']
    );

    // get the storage object
    $stS3 = new DdsStorageS3(
        'intacct.ddsdev',
        'DDS_ATLAS',
        $_SERVER['AWSAccessKeyId'],
        $_SERVER['AWSAccessKey']
    );

    $intacctPg->loadAll('REPORTINGPERIOD', $stS3);


} catch (Exception $ex) {
    print_r($ex->getMessage());
}
