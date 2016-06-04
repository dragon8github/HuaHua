
<?php
 /*******************数据库资料********/
session_start();
date_default_timezone_set("Asia/Shanghai");
//由页面初始化安装生成的用于以后访问数据库的文件
$con_server="127.0.0.1";
$con_name="root";
$con_password="asfdWf12312";
$database_name="huahua";
$con = mysql_connect($con_server,$con_name,$con_password);
mysql_select_db($database_name, $con);
mysql_query("set names utf8");


 ?>

 
 <?php
 $id=$_REQUEST['id'];
 $result_1 = mysql_query("delete from `question_library` where id='$id'");

  ?>
 
