<?php
/**
 * Created by PhpStorm.
 * User: aharris
 * Date: 3/10/14
 * Time: 1:00 PM
 */

require_once 'intacctws-php/api_session.php';
require_once 'intacctws-php/api_post.php';

require_once 'DdsLoader/DdsDbManager.php';

echo "<pre>";

try {

    $sess = new api_session();
    $sess->connectCredentials(
        $_SERVER['IntacctCompanyId'],
        $_SERVER['IntacctUserId'],
        $_SERVER['IntacctPwd'],
        $_SERVER['SenderId'],
        $_SERVER['SenderPwd']
    );

    echo DdsDbManager::getSchemaDdl($sess);


} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "\nLAST REQUEST:\n" . api_post::getLastRequest() . "\n";
    echo "LAST RESPONSE:\n" . api_post::getLastResponse();
}

echo "</pre>";
