<?php
/**
 * File dds_api.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2014 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

header('Content-Type:text');

require_once 'intacctws-php/api_post.php';
require_once 'intacctws-php/api_session.php';

require_once 'DdsLoader/DdsController.php';

try {
    // verify the correct URL format
    $sessionId = array_key_exists('sessionId', $_REQUEST) ? $_REQUEST['sessionId'] : null;
    $endPoint = array_key_exists('endPoint', $_REQUEST) ? $_REQUEST['endPoint'] : null;
    $method = array_key_exists('method', $_REQUEST) ? $_REQUEST['method'] : null;
    if ($sessionId === null || $endPoint === null || $method === null) {
        throw new Exception('Invalid arguments.  Please pass sessionId, endPoint, and method.');
    }
    $offline = array_key_exists('offline', $_REQUEST) ? $_REQUEST['offline'] : null;
    if ($offline == 1) {
        ignore_user_abort(true);
    }

    // initiate memcache and Intacct API session
    $sessKey = "DDS_SESSION_" . $sessionId;
    $memcache = new Memcache();
    $memcache->connect("localhost", 11211);
    if ($memcache->get($sessKey) === false) {
        $sess = new api_session();
        $sess->connectSessionId($sessionId, $_SERVER['tm_senderid'], $_SERVER['tm_senderpwd']);
        $sessKey = "DDS_SESSION_" . $sess->sessionId;
        $memcache->set($sessKey, $sess, 3600);
    }

    switch($method) {

    case 'getDdlSql':
        echo DdsController::getSchemaDdl($sess);
        break;
    case 'runDdsJob':
        // TODO: Need to accept Cloud Storage as argument
        // did we get the required arguments?
        $object = array_key_exists('object', $_REQUEST) ? $_REQUEST['object'] : null;
        if ($object === null) {
            throw new Exception("The method runDdsJob requires the argument 'object'.");
        }

        $wait = array_key_exists('wait', $_REQUEST) ? $_REQUEST['wait'] : false;

        $jobType = array_key_exists('jobType', $_REQUEST) ? $_REQUEST['jobType'] : null;
        if ($jobType === null) {
            throw new Exception("The method runDdsJob requires the argument 'jobType'.");
        }

        if ($jobType === api_post::DDS_JOBTYPE_CHANGE) {
            $timestamp = (array_key_exists('timestamp', $_REQUEST) ? $_REQUEST['timestamp'] : null);
            if ($timestamp === null || (strtotime($timestamp) === false)) {
                throw new Exception("The jobType " . api_post::DDS_JOBTYPE_CHANGE . " requires a valid timestamp");
            }
        }

        DdsController::runDdsJob($object, $jobType, $sess, $timestamp, $wait);
        break;
    case 'generateDdsObjectList':
        DdsController::generateDdsObjectList($sess);
        break;
    case 'trackDdsJob':
        $ddsJobKey = (array_key_exists('ddsJobKey', $_REQUEST)) ? $_REQUEST['ddsJobKey'] : null;
        if ($ddsJobKey === null) {
            throw new Exception("the trackDdsJob method requires ddsJobKey.");
        }
        DdsController::trackDdsJob($ddsJobKey, $sess);
        break;
    default:
        throw new Exception("Method $method is not implemented.");
    }

    echo "ok";

} catch (Exception $ex) {
    echo '[EXCEPTION] ' . $ex->getMessage() . "\n";
    echo $ex->getTraceAsString() . "\n";
    echo '[LAST REQUEST] ' . api_post::getLastRequest() . "\n";
    echo '[LAST RESPONSE] ' . api_post::getLastResponse() . "\n";
}