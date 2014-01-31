<?php
/**
 * File DdsConfig.php contains the class DdsConfig.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2013 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

/**
 * Class DdsConfig
 *
 * Get various DDS configuration information from a specific Intacct customer
 */
class DdsConfig
{

    /**
     * @var api_session $session Active Intacct API session
     */
    private $session;

    /**
     * Construct an instance of DdsConfig
     *
     * @param api_session $session Active api_session object
     */
    public function __construct(api_session $session)
    {
        $this->session = $session;
    }

    /**
     * Get a list of supported DDS objects
     *
     * @return array
     */
    public function getDdsObjects()
    {
        return api_post::getDdsObjects($this->session);
    }

    /**
     * Get the DDL for specified object
     * 
     * @param string $object Object for which to retrieve DDL
     *
     * @return String
     */
    public function getObjectDdl($object)
    {
        return api_post::getDdsDdl($this->session, $object);
    }
}