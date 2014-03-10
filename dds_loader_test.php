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

include_once 'DdsLoader/DdsDbRedshift.php';
include_once 'DdsLoader/DdsStorageS3.php';

try {
    // get the DB connection
    $dbConn = new DdsDbRedshift(
        "dds-dev.c808ui4qmvc1.us-west-2.redshift.amazonaws.com",
        "asimcmpyawana",
        "ddsdev",
        "ExodusDds14"
    );

    // get the storage object
    $stS3 = new DdsStorageS3(
        'intacct.ddsdev', '',
        'AKIAI6MGW6K3RUHM7RJA',
        'f2x9TXA8eub7btfnK/GdZIJ0mlpIdRYC3uYFK8gL'
    );

    $dbConn->loadAll('VENDOR', $stS3);


} catch (Exception $ex) {
    print_r($ex->getMessage());
}
