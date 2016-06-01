<?php

require_once "Wx_Class.php";
$ko=new WX_INT();
$openid=$_POST['openid'];
$access_token=$_POST['access_token'];
$mes=$ko->GetWeixinMessage($openid,$access_token);
echo $mes['nickname'];

  ?>