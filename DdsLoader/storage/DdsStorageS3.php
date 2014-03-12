<?php
/**
 * File DdsStorageS3.php contains the class DdsStorageS3.php
 *
 * @author    Aaron Harris <aharris@intacct.com>
 * @copyright 2000-2013 Intacct Corporation
 *
 * This document contains trade secret data that belongs to Intacct
 * Corporation and is protected by the copyright laws.  Information
 * herein may not be used, copied or disclosed in whole or in part
 * without prior written consent from Intacct Corporation.
 */

class DdsStorageS3
{
    private $bucket;
    private $path;
    private $keyId;
    private $key;

    /**
     * @param $bucket
     * @param $path
     * @param $keyId
     * @param $key
     */
    public function __construct($bucket, $path, $keyId, $key)
    {
        $this->bucket = $bucket;
        $this->path = $path;
        $this->keyId = $keyId;
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getKeyId()
    {
        return $this->keyId;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }


}