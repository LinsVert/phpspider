<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 2018/1/31
 * Time: 下午5:41
 */
namespace phpspider\common;

class Config{
    //song->comment->user->likesong(top 10)->attention->loop(likesong attention->slice)
    //Tactic：
    //MaxDeepth 5
    //Get comment list by a entry-song
    //Use users from comment
    //loop users and get user like song top 10 then get top 10 songs comments just host comment
    //get users attentions and loop
    //loop deepth max 5


    //now start
    const MUSIC163 = 'http://music.163.com';
    const MUSICREFERER = 'http://music.163.com';
    const MUSICTASTE = 'http://music.163.com/#/discover/recommend/taste';
    const USERAGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36';
   // const MUSICCOOKIE = 'UM_distinctid=16024d14729b26-0c36b9bf0fe3b-17396d57-13c680-16024d1472afa7; __gads=ID=7dca19e4b82284a1:T=1512446183:S=ALNI_MbUmTO-ZyjeCKwqvbPnHYz_Zw5RFQ; vjuids=1b5280f9a.16024d14b0b.0.c20b62f0d2945; _ntes_nnid=c79473e4d1145571df974d2ef59ef723,1512446184210; _ntes_nuid=c79473e4d1145571df974d2ef59ef723; P_INFO=m15168272264_1@163.com|1512457263|0|other|00&99|jis&1508884316&other#zhj&330100#10#0#0|151264&1|ty&xyq&study&note_client&mailuni|15168272264@163.com; _iuqxldmzr_=32; __remember_me=true; vjlast=1512446184.1516088558.23; vinfo_n_f_l_n3=a8f383254b472438.1.1.1512446184220.1512446208614.1516088683343; __e_=1516628178079; usertrack=ezq0plpopjbCRYF4D3p3Ag==; _ga=GA1.2.1936073037.1516807735; mail_psc_fingerprint=70ffc9f1e6e25bbdc251cc041c118fd4; __utmc=94650624; __utmz=94650624.1517297788.5.2.utmcsr=baidu|utmccn=(organic)|utmcmd=organic; JSESSIONID-WYYY=lO%5CGIXrn%5Cd8HzNbZbHjEcYaOs%2BKgF%2FVEqYYGlB2rB%2BhztlcEM27lookcC93Yky%2BsbGwrHJHC2xgEZ9Q%2F34bdmWVD5w1CZl6varTPVBbh6ze%2F1H0iqhbqoUAXymHmvKBHim6enTIJCt%2BO2JpeD84HtOrmvDANee5GjE3l3zKSMSjUr0EN%3A1517395562473; __utma=94650624.996639985.1513922555.1517297788.1517393763.6; MUSIC_U=b7b4c61980fde82c7055eefd1196be455f27daa0ce4b3ded355a85bf8db9bf0a057de73157b3d7ec8fbb57dd6d571cfb31b299d667364ed3; __csrf=b4c54c3bdb9ee0814ed41024cbe9008a; __utmb=94650624.5.10.1517393763';
    const MUSICCOOKIE = '_iuqxldmzr_=32; _ntes_nnid=a48c228187ebfa28dd79abdcc46d72b3,1517653891761; _ntes_nuid=a48c228187ebfa28dd79abdcc46d72b3; __utmc=94650624; __utmz=94650624.1517653892.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); JSESSIONID-WYYY=B2VOWErCyt9Y1gIkKEHncfs2PORiYn%2FRycz2Dikn4nt3cB%5CkKKoGMS6tpTT2J61eXhlzI%2BMcWCtNSnefuZvSgmJmxtG1YO4%5Ct8STfJr7W7MpWEbDZCjxoDVU2DuhBsTP0Tqa3x0aymzhIIhdnjYvQn4YQI22vI8SOzfeOp%5C1WPQsbaT5%3A1517659175161; __utma=94650624.1295343954.1517653892.1517653892.1517658054.2; __utmb=94650624.3.10.1517658054';
    const MUSICSONGSJSON = 'http://music.163.com/weapi/v2/discovery/recommend/songs?csrf_token=b4c54c3bdb9ee0814ed41024cbe9008a';
    const MUSICSSONGURI = 'http://music.163.com/#/song?id=%s';
    const MUSICUSERURI = 'http://music.163.com/user/home?id=%d';
    const MUSICCOOMENT = 'http://music.163.com/weapi/v1/resource/comments/%s?csrf_token=7160c6fa46f866c0ef3a4fc1dd4903b4';
    const USERLIKELISTTOP = 'http://music.163.com/weapi/v1/play/record?csrf_token=414353219ad7aac1fa13a76fcc82101a';
    const USERSONGLIST = 'http://music.163.com/weapi/user/playlist?csrf_token=8be29bd43ed53285c1c31b03f8ba17db';
    const USERFLLOWS = 'http://music.163.com/weapi/user/getfollows/%s?csrf_token=5e0743e63f711ccebce2ead20ad4173e';//关注列表
    const USERFANS = 'http://music.163.com/weapi/user/getfollows?csrf_token=';//关注列表
    const USERLIKELISTTOPPAGAM = '';
    const PLAYLISTARRAY = ["offset"=> 0, "uid"=> '%s', "limit"=> 10, "csrf_token"=> 'csrf'];//歌曲列表 加密参数
    const USERTOPSONGARRAY = ['uid'=>0,'csrf_token'=>'csrf','type'=>0];//用户听歌排行 加密参数 code -2 用户关闭了
    const COMMONAYYAY = [];
    const COMMENTARRAY = ['rid'=>'','offset'=>0,'total'=>'true','limit'=>10,'csrf_token'=>''];//歌曲评论 加密参数
    const SONGID = 139774;//歌曲id
    const COMMENTID = 'R_SO_4_%d';
    const MAXCOMMENT = 10;//最多评论条数
    const JSONCOMMENT = ['code','comments','hotComment','isMusician','more','moreHot','topComments','total','userId'];
    const PLAYLIST = 'http://music.163.com/playlist?id=%d';//喜欢的音乐列表
    const USERINFO = 'http://music.163.com/user/home?id=438590907';//用户详情


}