<?php

require_once "Wx_Class.php";
$ko=new WX_INT();
$openid=$_POST['openid'];
 $fhz=$ko->Zhifu(100,$openid);
 exit($fhz);

  ?>