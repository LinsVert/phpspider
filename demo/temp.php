<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 2018/2/3
 * Time: 下午6:37
 */

require_once __DIR__ . '/../autoloader.php';

use phpspider\core\requests;
use phpspider\common\config;
use phpspider\common\Secret;
use phpspider\core\db;

//db
//db::set_connect('default',['host'=>'127.0.0.1','user'=>'root','pass'=>'','port'=>3306,'name'=>'music163']);
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

$page = 1;

$referer = 'http://music.163.com/user/fans?id=468295043';
requests::set_referer($referer);
//$index = 64;
$pageSize = 1000;

$endTime = time()+3600;
$num = 1;
$child = [];
$fileName = __DIR__.'/../common/index.txt';
$log = __DIR__.'/../common/debug_log.log';
//$up = db::update('userinfo',['isCatch'=>0]);exit;
echo 'start '.microtime(true).PHP_EOL;
for ($i=0;$i<$num;$i++) {
    if($fp=fopen($fileName,'a')){
        $startTime=microtime();
        do{
            $canWrite=flock($fp,LOCK_EX);
            if(!$canWrite){
                usleep(round(rand(0,100)*1000));
            }
        }while((!$canWrite)&&((microtime()-$startTime)<1000));
        if($canWrite){
            $index = file_get_contents($fileName);
            // var_dump($index);
            //fwrite($fp,$index+1);

          file_put_contents($fileName,$index+1);

          $time = date('Y-m-d H:i:s');
          $logs = $time.' start page as '.$index.' ';
            file_put_contents($log,$logs.PHP_EOL,FILE_APPEND);

        }
        fclose($fp);
    }
    $pid = pcntl_fork();
    if ($pid == -1) {
        die('fork error');
    }
    if ($pid > 0) {
        //$id = pcntl_wait($status,WNOHANG);
        $child[] = $pid;
    } else if ($pid == 0) {
        db::set_connect('default',['host'=>'127.0.0.1','user'=>'root','pass'=>'','port'=>3306,'name'=>'music163']);
        $sql = 'select id,userinfoid from userinfo limit '.($index-1)*$pageSize.','.$pageSize;
        $res = db::query($sql)->fetch_all();
//       $res = [1,2,3,4,5,6,7,8];
        while ($res) {
          //  echo 'in2';
            //var_dump($res);
            $time1 = microtime(1);
            foreach ($res as $key) {

                $getFllow = Config::USERFLLOWS;
                $getFllow = sprintf($getFllow, $key[1]);
                echo getJson($page, $text, $getFllow, 'follow');
                // $up = db::update('userinfo',['isCatch'=>1],['id'=>$key[0]]);
            }
            if($fp=fopen($fileName,'a')){
                $startTime=microtime();
                do{
                    $canWrite=flock($fp,LOCK_EX);
                    if(!$canWrite){
                        usleep(round(rand(0,100)*1000));
                    }
                }while((!$canWrite)&&((microtime()-$startTime)<1000));
                if($canWrite){
                    $index = file_get_contents($fileName);
                    // var_dump($index);
                    //fwrite($fp,$index+1);
                   file_put_contents($fileName,$index+1);

                    $time = date('Y-m-d H:i:s');
                    $logs = $time.' Update page as '.$index . ' ';
                    file_put_contents($log,$logs.PHP_EOL,FILE_APPEND);
                }
                fclose($fp);
            }
           // $index++;
            echo $index;
            $sql = 'select id,userinfoid from userinfo  limit ' . ($index - 1) * $pageSize . ',' . $pageSize;
            $res = db::query($sql)->fetch_all();
            $time2 = microtime(1);
            echo '*****timeUsedByThisPage==========' . ($time2 - $time1) . '=========*****';
            if (time() > $endTime) break;
        }
        db::clear_link();
        $id = getmypid();
        echo 'child '.$id.' finished '.microtime(true).PHP_EOL;
        exit(0);

    }
}
while(count($child)){
    foreach($child as $k => $pid) {
        $res = pcntl_waitpid($pid, $status, WNOHANG);
        if ( -1 == $res || $res > 0) {
            unset($child[$k]);
        }
    }
}
echo 'end '.microtime(true).PHP_EOL;
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
    $filler = [1,9003,49259087,82005166,18866762,2814503];
    if($json['code'] == 200) {
        if($json[$keys])foreach ($json[$keys] as $key){
            if(in_array($key['userId'],$filler))continue;
            $insert[] = '('.$key['userId'].')';
            $insertStr = implode(',',$insert);
            //$sql = 'insert into `test` (`userinfoid`) VALUES ' . $insertStr .' ON DUPLICATE KEY UPDATE userinfoid=VALUES(userinfoid); ';
            $sql = 'insert into `userinfo` (`userinfoid`) VALUES ' . $insertStr .' ON DUPLICATE KEY UPDATE userinfoid=VALUES(userinfoid); ';
            //file_put_contents(__DIR__.'/../common/fans_'.microtime(1).'.json',$json);
            db::query($sql);
        }
        //var_dump($json['more']);

    }else {
        file_put_contents(__DIR__.'/../common/fans_'.microtime(1).'.json',json_encode($json)?json_encode($json):$getFllow);
        //return requests::$error;
    }

    if($json['more'] == true){
        $page++;
        getJson($page,$text,$getFllow,$keys);
    }
    return 1;
}
