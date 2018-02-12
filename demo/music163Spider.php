<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 2018/2/3
 * Time: 下午10:18
 */

require_once __DIR__ . '/../autoloader.php';
use phpspider\core\phpspider;
use phpspider\core\db;
use phpspider\common\Config;
/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = array(
    'name' => 'music163.com',
    'tasknum' => 6,
    'log_show' => true,
    'save_running_state' => false,
    'domains' => array(
        'music.163.com'
    ),
    'scan_urls' => array(
        "http://music.163.com/user/home?id=1",
//        "http://music.163.com/#/user/home?id=%s",
    ),
    'list_url_regexes' => array(
    ),
    'content_url_regexes' => array(
        "http://music.163.com/user/home\?id=\d+",
//        "http://music.163.com/user/home\?id=1",
    ),
    'export' => array(
        'type' => 'db',
        'table' => 'userinfo',
    ),
    'db_type' => 'update',
    'db_key' =>'userinfoid',
    'export' => array(
        'type' => 'db',
        'table' => 'userinfo',
    ),
    'db_config' => array(
        'host'  => '127.0.0.1',
        'port'  => 3306,
        'user'  => 'root',
        'pass'  => '',
        'name'  => 'music163',
    ),
    'queue_config' => array(
    'host'      => '127.0.0.1',
    'port'      => 6379,
    'pass'      => '',
    'db'        => 2,
    'prefix'    => 'music163',
    'timeout'   => 30,
    ),
    'fields' => array(
        //headImage 头像
        array(
            'name' => "headImage",
            'selector' => "//dt[contains(@id,'ava')]//img",

        ),
        //age 年龄
        array(
            'name' => "age",
            'selector' => "//span[contains(@id,'age')]//@data-age",

        ),
        //gender性别
        array(
            'name' => "gender",
            'selector' => "//h2[@id='j-name-wrap']//i/@class",
            'repeated' =>true,

        ),
        //addUp 累计听歌
        array(
            'name' => "addUp",
            'selector' => "//div[@id='rHeader']//h4",

        ),
        //level 用户等级
        array(
            'name' => "level",
            'selector' => "//span[contains(@class,'lev u-lev u-icn2 u-icn2-lev')]",

        ),
        //nickname 昵称
        array(
            'name' => "nickname",
            'selector' => "//span[contains(@class,'tit f-ff2 s-fc0 f-thide')]",

        ),
        //fans 粉丝
        array(
            'name' => "fans",
            'selector' => "//strong[contains(@id,'fan_count')]",

        ),
        //attention 关注
        array(
            'name' => "attention",
            'selector' => "//strong[contains(@id,'follow_count')]",

        ),
        //action 动态
        array(
            'name' => "action",
            'selector' => "//strong[contains(@id,'event_count')]",

        ),
        //area 地区
        array(
            'name' => "city",
            'selector' => "//div[contains(@class,'inf s-fc3')]//span",
            'repeated' =>true,

        ),
        //province
        array(
            'name' => "province",
            'selector' => "//div[contains(@class,'inf s-fc3')]//span",
            'repeated' =>true,

        ),
        //introduction 个人介绍
        array(
            'name' => "introduction",
            'selector' => "//div[contains(@class,'inf s-fc3 f-br')]",

        ),
        //userinfoid
        array(
            'name'=>'userinfoid',
            'selector'=>"//li[contains(@class,'fst')]//a//@href",
            'repeated' =>true,
        ),
    ),
);

$spider = new phpspider($configs);

$spider->on_start = function($phpspider)
{
//    $arr = [1,9003,49259087,82005166,18866762,2814503,80709138];
//    $phpspider->add_scan_url("http://music.163.com/user/home?id=".$arr[0]);
//    $phpspider->add_scan_url("http://music.163.com/user/home?id=".$arr[1]);
//    $phpspider->add_scan_url("http://music.163.com/user/home?id=".$arr[2]);
//    $phpspider->add_scan_url("http://music.163.com/user/home?id=".$arr[3]);
//    $phpspider->add_scan_url("http://music.163.com/user/home?id=".$arr[4]);
//    $phpspider->add_scan_url("http://music.163.com/user/home?id=".$arr[5]);
//    $phpspider->add_scan_url("http://music.163.com/user/home?id=".$arr[6]);
};
$spider->on_scan_page = function ($a,$b,$c){
    return false;
};
$spider->on_list_page = function ($a,$b,$c){
    return false;
};
$spider->on_content_page = function ($a,$b,$phpspider){
    //添加文件锁
    $fileName = __DIR__.'/../common/page.txt';
    $log = __DIR__.'/../common/debug_log.log';
    $collected_url_num =  $phpspider->get_collected_url_num();//拿到已经抓取对数量
    $need_collect_num =   $phpspider->get_collect_url_num();
    $pageSize  = 1000;
    $page = 600;
    $alreadyPage = 501;
    $flag = false;
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
            $need_collect = ($index-1) * $pageSize;
            $collected_url_num = $collected_url_num + ($alreadyPage-1) * $pageSize;
            if(($need_collect - $phpspider::$tasknum < $collected_url_num || $need_collect_num == 0) && $index <= $page){
                 file_put_contents($fileName,$index+1);
                 $flag = true;
                $time = date('Y-m-d H:i:s');
                $logs = $time.' Add Collect Page As '.$index.' ';
                file_put_contents($log,$logs.PHP_EOL,FILE_APPEND);
            }
        }
        fclose($fp);
        $index = isset($index)?$index:0;
        if($flag && $index <= $page){//需要拿数据
            $logs = '***In**'.microtime(1).'****index**'.$index;
            file_put_contents($log,$logs.PHP_EOL,FILE_APPEND);
            $db = $phpspider::$db_config;
            db::set_connect('default',$db);
            $sql = 'select id,userinfoid from '.' `userinfo` order by id asc limit '. (isset($index)?($index-1)*$pageSize:0) .','.$pageSize;
            $res = db::query($sql)->fetch_all();
            db::clear_link();
            $url_h = Config::MUSICUSERURI;
            if($res) {
                foreach ($res as $key) {
                    $url = sprintf($url_h, $key[1]);
                    $phpspider->add_url($url);
                }
            }
        }else{
           // $logs = '***Out**'.microtime(1).'****Collected_Nums = '.$collected_url_num.'*need_collect_num = '.$need_collect_num.'';
          //  file_put_contents($log,$logs.PHP_EOL,FILE_APPEND);
        }
    }
    return false;
};

$spider->on_extract_field = function ($fieldname,$data,$page)
{

    switch ($fieldname){
        case 'userinfoid':
            $reg = '/\d+/';
            preg_match($reg,is_array($data)?$data[0]:$data,$res);
            $result = (int)$res[0]>0?(int)$res[0]:99;
            //$log = 'Id = '.(is_array($result)?json_encode($result):$result).PHP_EOL.json_encode($data).PHP_EOL;
            //file_put_contents(__DIR__.'/../common/debug_log.log',$log,FILE_APPEND);
            break;
        case 'city'://获取城市
            $res = is_array($data)?$data[0]:$data;

            $res = str_replace('所在地区：','',$res);
            $res = explode(' - ',$res);
            $result = isset($res[1])?$res[1]:'';
            break;
        case 'province':
            $res = is_array($data)?$data[0]:$data;
            $res = str_replace('所在地区：','',$res);
            $res = explode(' - ',$res);
            $result = isset($res[0])?$res[0]:'';
            break;
        case 'level':
            $result = (int)str_replace('<i class="right u-icn2 u-icn2-levr"/>','',$data);
            break;
        case 'addUp':
            $reg = '/\d+/';
            preg_match($reg,$data,$res);
            $result = $res[0];
            break;
        case 'gender':
            $res = is_array($data)?$data[1]:$data;
            $result = (int)substr($res,-2,2);
            break;
        case 'age':
            $result = substr($data,0,-3);//timestamp
            break;
        default :
            $result = $data;
            break;
    }
    return  $result;
};
$spider->start();
