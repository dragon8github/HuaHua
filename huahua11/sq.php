<?php 
session_start();
$url="http://huahua.ncywjd.com/huahua11/index.php";
//$str_url=urlencode($url);

$appid = "wx0b62213ee8ee0c90";
$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';

header("Location:".$url);


?>