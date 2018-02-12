<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 2018/2/7
 * Time: 上午10:05
 */
require_once __DIR__ . '/../autoloader.php';

use phpspider\core\requests;
use phpspider\common\Config;
use phpspider\common\Secret;


requests::set_cookie(Config::MUSIC163,Config::MUSICCOOKIE);
requests::set_referer(Config::MUSICREFERER);
requests::set_useragent(Config::USERAGENT);

$id = 329304046;

$text = Config::USERTOPSONGARRAY;
$text['uid'] = $id;
$getFllow = Config::USERLIKELISTTOP;
$secret = new Secret();
$filed = $secret->createParam(json_encode($text));
$json = requests::post($getFllow,$filed);
$filename = __DIR__.'/../common/listen_80709138.text';
file_put_contents($filename,$json);
exit('success');


//arry struct

// score => allData[][score]
// songid=> allData[][song][id]
// songName => allData[][song][name]
// songAuth => allData[][song][ar][0][name]