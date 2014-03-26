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

include_once 'DdsDbManager.php';

class DdsController
{

    /**
     * Generate the DDL SQL for all objects and return as text
     *
     * @param api_session $sess
     * @return string
     */
    public static function getSchemaDdl(api_session $sess)
    {
        $ddlSql = DdsDbManager::getSchemaDdl($sess);
        return $ddlSql;
    }

    /**
     * Get the list of exposed DDS objects and create a record for each in the DDS Admin Application
     *
     * @param api_session $sess
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
     * @param $object
     * @param $jobType
     * @param api_session $sess
     * @param null $timestamp
     * @throws Exception
     */
    public static function runDdsJob($object, $jobType, api_session $sess, $timestamp = NULL)
    {
        if ($jobType !== api_post::DDS_JOBTYPE_ALL && $jobType !== api_post::DDS_JOBTYPE_CHANGE) {
            throw new Exception("Invalid job type.  Use one of the api_post DDS constants.");
        }

        // Get the configured Cloud Storage destination
        $destinationObj = api_post::readByName('dds_preference', 'CLOUD_STORAGE', 'value', $sess);

        // Run the job
        api_post::runDdsJob($sess, $object, $destinationObj['value'], $jobType, $timestamp);

    }
}