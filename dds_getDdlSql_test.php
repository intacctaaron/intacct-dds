<?php
/**
 * Created by PhpStorm.
 * User: aharris
 * Date: 3/10/14
 * Time: 1:00 PM
 */

include_once 'intacctws-php/api_session.php';
include_once 'intacctws-php/api_post.php';

include_once 'DdsLoader/DdsDbManager.php';

echo "<pre>";

try {

    $sess = new api_session();
    $sess->connectCredentials('DDS_ATLAS', 'Aaron', 'As123456!', 'intacct_dev', 'isa9Shixa');

    echo DdsDbManager::getSchemaDdl($sess);


} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "\nLAST REQUEST:\n" . api_post::getLastRequest() . "\n";
    echo "LAST RESPONSE:\n" . api_post::getLastResponse();
}

echo "</pre>";
