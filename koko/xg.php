<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
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
 <table>
 
 <?php
 $result_1 = mysql_query("select * from `question_library`");
while($row =mysql_fetch_array($result_1)){  

$answer=$row['answer'];
$id=$row['id'];

echo "<tr><td width='80'>$id</td><td width='100'>$answer</td><td width='80'><a class='dd' val=$id href='#'>删除</a></td></tr>";
}
  ?>
 
 
 </table>
 <script type="text/javascript" src="js.js"></script>
 <script type="text/javascript">
 $(document).ready(function()
 {
 	$(".dd").click(function()
	{
	var id=$(this).attr("val");
		$.ajax({

	   type: "POST",
	
	   url: "del.php",
	
	   data: "id="+id,
	
	   success: function(msg){
			
		 alert("删除成功");
		 location.reload();

	
	   }})
	   return false;

});
	
	
 
 })
 </script>
</body>
</html>
