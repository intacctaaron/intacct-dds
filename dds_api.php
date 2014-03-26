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

include_once 'intacctws-php/api_post.php';
include_once 'intacctws-php/api_session.php';

include_once 'DdsLoader/DdsController.php';

try {
    // verify the correct URL format
    $sessionId = array_key_exists('sessionId', $_REQUEST) ? $_REQUEST['sessionId'] : NULL;
    $endPoint = array_key_exists('endPoint', $_REQUEST) ? $_REQUEST['endPoint'] : NULL;
    $method = array_key_exists('method', $_REQUEST) ? $_REQUEST['method'] : NULL;
    if ($sessionId === NULL || $endPoint === NULL || $method === NULL) {
        throw new Exception('Invalid arguments.  Please pass sessionId, endPoint, and method.');
    }

    $sess = new api_session();
    $sess->connectSessionId($sessionId, $_SERVER['tm_senderid'], $_SERVER['tm_senderpwd']);

    switch($method) {

        case 'getDdlSql':
            echo DdsController::getSchemaDdl($sess);
            break;
        case 'runDdsJob':
            // did we get the required arguments?
            $object = array_key_exists('object', $_REQUEST) ? $_REQUEST['object'] : NULL;
            if ($object === NULL) {
                throw new Exception("The method runDdsJob requires the argument 'object'.");
            }

            $jobType = array_key_exists('jobType', $_REQUEST) ? $_REQUEST['jobType'] : NULL;
            if ($jobType === NULL) {
                throw new Exception("The method runDdsJob requires the argument 'jobType'.");
            }

            if ($jobType === api_post::DDS_JOBTYPE_CHANGE) {
                $timestamp = (array_key_exists('timestamp', $_REQUEST) ? $_REQUEST['timestamp'] : NULL);
                if ($timestamp === NULL || (strtotime($timestamp) === FALSE)) {
                    throw new Exception("The jobType " . api_post::DDS_JOBTYPE_CHANGE . " requires a valid timestamp");
                }
            }

            DdsController::runDdsJob($object, $jobType, $sess);
            break;
        case 'generateDdsObjectList':
            DdsController::generateDdsObjectList($sess);
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