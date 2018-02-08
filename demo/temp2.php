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
use phpspider\core\selector;
$id = 80709138;
$url = Config::MUSICUSERURI;
$url = sprintf($url,$id);
//echo $url;
requests::set_referer(Config::MUSICREFERER);
requests::set_useragent(Config::USERAGENT);
//requests::set_cookie(Config::MUSIC163,Config::MUSICCOOKIE);


//$html = requests::get($url);
//if(requests::$error){
//    echo requests::$error;
//}else {
//    $file = __DIR__.'/../common/userinfo.html';
//    file_put_contents($file,$html);
//    echo 'success';
//
//}
//exit;

$xpath = "//div[contains(@class,'inf s-fc3 f-br')]";//introduction string
$xpaths = "//div[contains(@class,'inf s-fc3')]//span";//area city array
//$res = $res[0];
//$res = str_replace('所在地区：','',$res);
//$res = explode(' - ',$res);
$xpath = "//strong[contains(@id,'event_count')]";//event int
$xpath = "//strong[contains(@id,'follow_count')]";//follows int
$xpath = "//strong[contains(@id,'fan_count')]";//fans int
$xpath = "//span[contains(@class,'tit f-ff2 s-fc0 f-thide')]";//nickname string
$xpath = "//span[contains(@class,'lev u-lev u-icn2 u-icn2-lev')]";//level int
//$res = str_replace('<i class="right u-icn2 u-icn2-levr"/>','',$res);
$xpath = "//div[@id='rHeader']//h4";//addUp int
//$reg = '/\d+/';
//preg_match($reg,$res,$res);
//$res = $res[0];
$xpath = "//h2[@id='j-name-wrap']//i/@class";//sex int 1 man 2 female
//$res = $res[1];
//$res = (int)substr($res,-2,2);
$xpath = "//span[contains(@id,'age')]//@data-age";//age 702835200000 90后 820080000000 95hou 1003160807914 00hou 可能没有 946656000000
//$res = substr($res,0,-3);//timestamp
$xpath = "//dt[contains(@id,'ava')]//img";//header string

$file = __DIR__.'/../common/userinfo.html';
$html = file_get_contents($file);

$res = selector::select($html,$xpath);
$res = substr($res,0,-3);
var_dump($res);