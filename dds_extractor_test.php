<?php
/**
 * File dds_extractor_test.php contains the class dds_extractor_test.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2013 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

require_once 'intacctws-php/api_session.php';
require_once 'intacctws-php/api_post.php';

require_once 'DdsLoader/DdsController.php';

try {

    // Intacct API session
    $memcache = new Memcache();
    $memcache->connect('localhost', 11211);
    $key = 'dds_extractor_test_session';
    $session = $memcache->get($key);
    if ($session === false) {
        $session = new api_session();
        $session->connectCredentials(
            $_SERVER['IntacctCompanyId'],
            $_SERVER['IntacctUserId'],
            $_SERVER['IntacctPwd'],
            $_SERVER['SenderId'],
            $_SERVER['SenderPwd']
        );
        $memcache->set($key, $session, null, 300);
    }

    // let's just extract the glaccount object
    DdsController::runDdsJob('GLACCOUNT', api_post::DDS_JOBTYPE_ALL, "Aaron's Dropbox", $session, null, true);

}
catch (Exception $ex) {
    printf($ex->getMessage() . '\n');
    printf(api_post::getLastRequest() . '\n');
    printf(api_post::getLastResponse() . '\n');
}