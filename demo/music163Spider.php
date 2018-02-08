<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 2018/2/3
 * Time: 下午10:18
 */

require_once __DIR__ . '/../autoloader.php';
use phpspider\core\phpspider;
/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = array(
    'name' => 'music163.com',
    //'tasknum' => 8,
    'log_show' => true,
    'save_running_state' => false,
    'domains' => array(
        'music.163.com'
    ),
    'scan_urls' => array(
        "http://music.163.com/user/home?id=80709138",
//        "http://music.163.com/#/user/home?id=%s",
    ),
    'list_url_regexes' => array(
    ),
    'content_url_regexes' => array(
        "http://music.163.com/user/home\?id=\d+",
//        "http://music.163.com/user/home\?id=1",
    ),
//    'export' => array(
//        'type' => 'db',
//        'table' => 'userinfo',
//    ),
    'db_type' => 'update',
    'db_key' =>'userinfoid',
    'export' => array(
        'type' => 'db',
        'table' => 'test',
//'type' => 'csv',
//        'file' => __DIR__.'/../common/test.csv',
    ),
    'db_config' => array(
        'host'  => '127.0.0.1',
        'port'  => 3306,
        'user'  => 'root',
        'pass'  => '',
        'name'  => 'music163',
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
   // $phpspider->add_url =
};
$spider->on_extract_field = function ($fieldname,$data,$page)
{

    switch ($fieldname){
        case 'userinfoid':
            $reg = '/\d+/';
            preg_match($reg,is_array($data)?$data[0]:$data,$res);
            $result = $res[0];
            break;
        case 'city'://获取城市
            $res = is_array($data)?$data[0]:$data;

            $res = str_replace('所在地区：','',$res);
            $res = explode(' - ',$res);
            $result = $res[1]?$res[1]:'';
            break;
        case 'province':
            $res = is_array($data)?$data[0]:$data;
            $res = str_replace('所在地区：','',$res);
            $res = explode(' - ',$res);
            $result = $res[0]?$res[0]:'';
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
