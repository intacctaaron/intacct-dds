<?php
/**
 * Created by PhpStorm.
 * User: aharris
 * Date: 4/18/14
 * Time: 8:00 PM
 */

header('Content-Type:text');

require_once 'intacctws-php/api_session.php';
require_once 'intacctws-php/api_post.php';

require_once 'DdsLoader/DdsController.php';

try {

    $memcache = new Memcache();
    $memcache->connect('localhost', '11211');

    $key = "test_loadObjects_sess";
    $sess = $memcache->get($key);
    if ($sess === false) {
        $sess = new api_session();
        $sess->connectCredentials(
            $_SERVER['IntacctCompanyId'],
            $_SERVER['IntacctUserId'],
            $_SERVER['IntacctPwd'],
            $_SERVER['SenderId'],
            $_SERVER['SenderPwd']
        );
        $memcache->set($key, $sess, 300);
    }

    DdsController::generateDdsObjectList($sess);

} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "[REQUEST] " . api_post::getLastRequest();
    echo "[RESPONSE] " . api_post::getLastResponse();

}