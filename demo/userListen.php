<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 2018/2/9
 * Time: 下午5:03
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

$text = Config::USERTOPSONGARRAY;

$page = 1;
//$index = 64;
$pageSize = 1000;

$endTime = time()+3600/2;
$num = 1;
$child = [];
$fileName = __DIR__.'/../common/listen.txt';
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
        $sql = 'select id,userinfoid from userinfo order by id asc limit '.($index-1)*$pageSize.','.$pageSize;
        $res = db::query($sql)->fetch_all();
//       $res = [1,2,3,4,5,6,7,8];
        while ($res) {
            //  echo 'in2';
            //var_dump($res);
            $time1 = microtime(1);
            foreach ($res as $key) {
                $text['uid'] = $key[1];
                $getFllow = Config::USERLIKELISTTOP;
                echo getJson($page, $text, $getFllow, 'allData',$key[1]);
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
            $sql = 'select id,userinfoid from userinfo order by id asc limit ' . ($index - 1) * $pageSize . ',' . $pageSize;
            $res = db::query($sql)->fetch_all();
            $time2 = microtime(1);
            echo '*****timeUsedByThisPage==========' . ($time2 - $time1) . '=========*****'.PHP_EOL;
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
        $ress = pcntl_waitpid($pid, $status, WNOHANG);
        if ( -1 == $ress || $ress > 0) {
            unset($child[$k]);
        }
    }
}

echo 'end '.microtime(true).PHP_EOL;
exit();


function getJson($page = 1,$text = [],$getFllow,$keys,$userinfoid = 0){

    $text['offset'] = $text['limit'] * ($page-1);
    $entry = new Secret();
    $filed = $entry->createParam(json_encode($text));
    $json =  requests::post($getFllow,$filed);
    $json = json_decode($json,true);

    if($json['code'] == 200) {
        if($json[$keys]) {
            $insertStr = [];
            foreach ($json[$keys] as $key) {

                // score => allData[][score]
                // songid=> allData[][song][id]
                // songName => allData[][song][name]
                // songAuth => allData[][song][ar][0][name]

                $key['song']['name'] = str_replace("'","\'",$key['song']['name']);
                $key['song']['name'] = str_replace('"','\"',$key['song']['name']);
                $key['song']['ar'][0]['name'] = str_replace("'","\'",$key['song']['ar'][0]['name']);
                $key['song']['ar'][0]['name'] = str_replace('"','\"',$key['song']['ar'][0]['name']);
                $insertStr[] = "($userinfoid," . $key['song']['id'] . ',"' . $key['song']['name'] . '","' . $key['song']['ar'][0]['name'] . '",'.$key['score'].")";

            }
            $insertStr = implode(',',$insertStr);
            $sql = 'insert  into `userinfo_listen` (userinfoid,songid,songName,songAuthor,score) VALUES ' . $insertStr . '; ';
            db::query($sql);
            //file_put_contents(__DIR__ . '/../common/listen_' . $userinfoid . '.sql', $sql);
        }
    }else {
        file_put_contents(__DIR__.'/../common/listen.log',$userinfoid.' no open'.PHP_EOL,8);

    }

    return 1;
}
