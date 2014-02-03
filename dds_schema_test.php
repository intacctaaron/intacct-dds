<?php
/**
 * Test DDS Schema creation
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2014 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

// simple test to see if we can create a database
require_once 'intacctws-php/api_session.php';
require_once 'intacctws-php/api_post.php';

require_once 'DdsLoader/DdsDbPostgres.php';

try {

    $memcache = new Memcache();
    $memcache->connect("localhost", 11211);
    $key = 'dds_loader_test_session';
    $session = $memcache->get($key);
    if ($session === false) {
        $session = new api_session();
        $session->connectCredentials('asim_cmpy_awana', 'Aman', 'Aa123456!', 'intacct_dev', 'babbage');
        $memcache->set($key, $session, null, 300);
    }

    $objKey = "dds_loader_test_objects";
    $ddsObjects = $memcache->get($objKey);
    if ($ddsObjects === false) {
        $ddsObjects = api_post::getDdsObjects($session);
        $memcache->set($objKey, $ddsObjects, MEMCACHE_COMPRESSED, 3000);
    }

    $intacctPg = new DdsDbPostgres(
        "dds-dev.c808ui4qmvc1.us-west-2.redshift.amazonaws.com",
        "asimcmpyawana",
        "ddsdev",
        "ExodusDds14",
        '5439'
    );

    foreach ($ddsObjects as $ddsObject) {
        // does the table exist?
        // skip USERINFO
        if ($ddsObject == 'USERINFO') {
            continue;
        }

        if ($intacctPg->tableExists($ddsObject)) {
            $ddl = api_post::getDdsDdl($session, $ddsObject);
            $res = $intacctPg->execStmt($ddl);
            printf("table $ddsObject created.\n");
        } else {
            printf("table $ddsObject skipped.\n");
        }
    }
    echo "done!";
}
catch (Exception $ex) {
    print_r($ex->getMessage());
    print_r(api_post::getLastRequest());
    print_r(api_post::getLastResponse());
}

