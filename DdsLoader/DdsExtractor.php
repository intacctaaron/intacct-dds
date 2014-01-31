<?php
/**
 * File DdsExtractor.php contains the class DdsExtractor.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2014 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

/**
 * Class DdsExtractor
 *
 * DdsExtractor kicks off DDS Jobs
 */
class DdsExtractor
{

    /**
     * @var api_session $session An active Intacct API session
     */
    private $session;

    /**
     * @var string $cloudStorage A valid CloudStorage destination in the Intacct company
     */
    private $cloudStorage;

    /**
     * Construct an instance of DdsExtractor
     *
     * @param api_session $session      An active Intacct API session
     * @param string      $cloudStorage A valid CloudStorage destination in the Intacct company
     */
    public function __construct(api_session $session, $cloudStorage)
    {
        $this->session = $session;
        $this->cloudStorage = $cloudStorage;
    }

    /**
     * Extract all records for a given object
     *
     * @param string $object Object to extract
     *
     * @return null
     */
    public function getAll($object)
    {
        api_post::runDdsJob($this->session, $object, $this->cloudStorage, api_post::DDS_JOBTYPE_ALL);
    }

    /**
     * Extract all changed records from a given timestamp
     *
     * @param string $object    Object to extract
     * @param string $timestamp Timestamp from which to extract.  ISO8601 timestamps work best.
     *
     * @return null
     */
    public function getChanged($object, $timestamp)
    {
        api_post::runDdsJob($this->session, $object, $this->cloudStorage, api_post::DDS_JOBTYPE_CHANGE, $timestamp);
    }
}