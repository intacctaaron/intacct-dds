<?php
/**
 * File DdsController.php contains the class DdsController
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2014 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

require_once 'DdsDbManager.php';
require_once 'intacctws-php/api_ddsJobAry.php';

/**
 * Class DdsController
 */
class DdsController
{

    /**
     * Generate the DDL SQL for all objects and return as text
     *
     * @param api_session $sess Connected api_session object
     *
     * @return string
     *
     */
    public static function getSchemaDdl(api_session $sess)
    {
        $ddlSql = DdsDbManager::getSchemaDdl($sess);
        return $ddlSql;
    }

    /**
     * Get the list of exposed DDS objects and create a record for each in the DDS Admin Application
     *
     * @param api_session $sess Connected api_session object
     *
     * @return null
     */
    public static function generateDdsObjectList(api_session $sess)
    {
        // get all the objects
        $objects = api_post::getDdsObjects($sess);
        $insObjs = array();

        foreach ($objects as $object) {
            /**
             * @var api_objDef $objDef
             */
            $objDef = api_post::inspect($object, true, $sess);

            $insObj = array(
                'dds_object' => array(
                    'name' => $object,
                    'label' => $objDef->SingularName
                )
            );

            $insObjs[] = $insObj;
        }

        api_post::upsert('dds_object', $insObjs, 'name', 'id', $sess);
    }

    /**
     * Run a DdsJob on an object
     *
     * @param string      $object       integration name for object on which to run the job
     * @param string      $jobType      One of the valid api_post::DDS_JOBTYPE* constants
     * @param string      $cloudStorage The name of a valid Cloud Storage destination
     * @param api_session $sess         Connected api_session object
     * @param null        $timestamp    iso 8601 valid timestamp value
     * @param bool        $wait         Whether or not to wait for the job to complete before returning control
     *
     * @return null
     * @throws Exception
     */
    public static function runDdsJob(
        $object, $jobType, $cloudStorage, api_session $sess, $timestamp = null, $wait = false
    ) {
        if ($jobType !== api_post::DDS_JOBTYPE_ALL && $jobType !== api_post::DDS_JOBTYPE_CHANGE) {
            throw new Exception("Invalid job type.  Use one of the api_post DDS constants.");
        }

        // test the file configuration
        //$fileConf = new api_ddsFileConfiguration('{|}', null, true, api_ddsFileConfiguration::DDS_FILETYPE_WINDOWS);

        // Run the job
        /** @var $ddsJob api_ddsJob */
        $ddsJob = api_post::runDdsJob($sess, $object, $cloudStorage, $jobType, $timestamp);
        api_post::create(array($ddsJob->toApiArray()), $sess);
        $ddsJobKey = $ddsJob->getKey();

        if ($wait === false) {
            // kick off a "Check on the job process"
            // this needs to be an async process that comes back in through the API

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['SERVER_NAME'] . "/" . $_SERVER['REQUEST_URI']);

            return;
        } else {
            DdsController::trackDdsJob($ddsJobKey, $sess);
        }

    }

    /**
     * Wait on a DDS Job to complete and update the DDS app
     *
     * @param int         $ddsJobKey Key to the DDS job
     * @param api_session $sess      Connected api_session object
     *
     * @return null
     */
    public static function trackDdsJob($ddsJobKey, api_session $sess)
    {
        // check on the job!
        // set some sort of max try.  How long to keep trying??
        $maxWait = 60*60; // one hour
        $cliff = 60;
        $shortTerm = 5;
        $longTerm = 120;

        // wait 5 seconds?  Going down to 1 minute after 1 minute?
        $totesTime = 0;
        $startTime = time();

        while ($totesTime < $maxWait) {

            $ddsJobAry = api_post::read('DDSJOB', $ddsJobKey, '*', $sess);
            $ddsJob = new api_ddsJobAry($ddsJobAry);
            /** @var $ddsJob api_ddsJob */

            if ($ddsJob->getStatus() == api_ddsJob::DDS_STATUS_FAILED
                || $ddsJob->getStatus() == api_ddsJob::DDS_STATUS_COMPLETED
            ) {
                // do something about the job getting finished
                // start the extractor?
                api_post::upsert('dds_job', array($ddsJob->toApiArray()), 'name', 'id', $sess);
                $filesAry = $ddsJob->toApiArrayFiles();
                $filesForApi = array();
                foreach ($filesAry as $file) {
                    $filesForApi[] = $file;
                    if (count($filesForApi) == 100) {
                        api_post::create($filesForApi, $sess);
                        $filesForApi = array();
                    }
                }
                if (count($filesForApi) > 0) {
                    api_post::create($filesForApi, $sess);
                }


                api_post::create($ddsJob->toApiArrayFiles(), $sess);
                return;
            } else {
                // write an update to the DDS Job Manager
                api_post::upsert('dds_job', array($ddsJob->toApiArray()), 'name', 'id', $sess);
                if ((time() - $startTime) > $cliff) {
                    sleep($longTerm);
                } else {
                    sleep($shortTerm);
                }

                $totesTime = (time() - $startTime);
            }
        }
    }
}