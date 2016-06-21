<?php 
session_start();
$url="http://hh.ncywjd.com/index.php";
//$str_url=urlencode($url);

$appid = "wx911ae27f5e1197c3";
$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';

header("Location:".$url);


?>