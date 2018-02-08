<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 2018/1/31
 * Time: 下午5:36
 */

if(PHP_SAPI !='cli') exit('only cli!');
require_once __DIR__ . '/../autoloader.php';
use phpspider\core\requests;
use phpspider\common\config;
use phpspider\common\Secret;
use phpspider\core\db;

//db
db::set_connect('default',['host'=>'127.0.0.1','user'=>'root','pass'=>'','port'=>3306,'name'=>'music163']);
//init
requests::set_cookie(Config::MUSIC163,Config::MUSICCOOKIE);
requests::set_referer(Config::MUSICREFERER);
requests::set_useragent(Config::USERAGENT);

$startUid = 468295043;

$getFllow = Config::USERFLLOWS;
$getFllow = sprintf($getFllow,$startUid);
$text = ['offset'=>0,'csrf_token'=>'','limit'=>20];

$getFllowed = Config::USERFANS;
$fansText = ['offset'=>0,'csrf_token'=>'','limit'=>20,'uid'=>$startUid,'total'=>true];



//$json =  requests::post($getFllow,$filed);
$page = 1;

$referer = 'http://music.163.com/user/fans?id=468295043';
requests::set_referer($referer);
//echo getJson($page,$fansText,$getFllowed,'followeds');
$index = 64;
$pageSize = 1000;
$sql = 'select id,userinfoid from userinfo limit '.($index-1)*$pageSize.','.$pageSize;
$res = db::query($sql)->fetch_all();
$endTime = time()+3600;
//$up = db::update('userinfo',['isCatch'=>0]);exit;
echo 'in';
while ($res){
    echo 'in2';
    //var_dump($res);
    $time1 = microtime(1);
    foreach ($res as $key){

        $getFllow = Config::USERFLLOWS;
        $getFllow = sprintf($getFllow,$key[1]);
        echo getJson($page,$text,$getFllow,'follow');
       // $up = db::update('userinfo',['isCatch'=>1],['id'=>$key[0]]);
    }
    $index++;
    echo $index;
    $sql = 'select id,userinfoid from userinfo  limit '.($index-1)*$pageSize.','.$pageSize;
    $res = db::query($sql)->fetch_all();
    $time2 = microtime(1);
    echo '*****timeUsedByThisPage=========='.($time2-$time1).'=========*****';
    if(time()>$endTime) break;
}
exit();


function getJson($page = 1,$text = [],$getFllow,$keys){
    $insert = [];
    $text['offset'] = $text['limit'] * ($page-1);
    $entry = new Secret();
    $filed = $entry->createParam(json_encode($text));
    $json =  requests::post($getFllow,$filed);
    $json = json_decode($json,true);
   // echo requests::$error;
   // var_dump($json);
   // exit();
    if($json['code'] == 200) {
        if($json[$keys]){
            foreach ($json[$keys] as $key){
            $insert[] = '('.$key['userId'].')';
            $insertStr = implode(',',$insert);
            $sql = 'insert into `userinfo` (`userinfoid`) VALUES ' . $insertStr .' ON DUPLICATE KEY UPDATE userinfoid=VALUES(userinfoid); ';
            //file_put_contents(__DIR__.'/../common/fans_'.microtime(1).'.json',$json);
            db::query($sql);
         }
        }

    }else {
        file_put_contents(__DIR__.'/../common/fans_'.microtime(1).'.json',$json);
        //return requests::$error;
    }

    if($json['more'] == true){
        $page++;
        getJson($page,$text,$getFllow,$keys);
    }
    return 1;
}

