<?php

SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/Channel/Channel1.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库
if(!isset($_SESSION['id']))
{
exit("非法登录");
}

$id=$_SESSION['id'];
//页面逻辑 开始==============================================
$_Channel_01Ctrl = new Channel1_Ctrl();

$zuot_arr = $_Channel_01Ctrl->get_昨天的信息($id);
$jint_arr = $_Channel_01Ctrl->get_今天的信息($id);
$all_arr = $_Channel_01Ctrl->get_所有的信息($id);

$zuot_balance = $zuot_arr["balance"];
$zuot_count = $zuot_arr["count"];


$jint_arr_balance = $jint_arr["balance"];
$jint_arr_count = $jint_arr["count"];


$all_arr_balance = $all_arr["balance"];
$all_arr_count = $all_arr["count"];

?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title></title>
	<link href="https://cdn.bootcss.com/bootstrap/3.3.6//css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

	
	<div class="container">
	<div class="row clearfix">
		<div class="col-md-12 column">
			<div class="jumbotron">
				<h2>
					粉丝增长
				</h2>
			     <p>
    				<p>今天：<?php echo $jint_arr_count; ?> </p>
    				<p>昨天：<?php echo $zuot_count; ?></p>
    				<p>总共：<?php echo $all_arr_count; ?></p>
				</p>
				
				<hr />
				
				<h2>
					金额收入
				</h2>
			     <p>
    				<p>今天：<?php echo $jint_arr_balance; ?></p>
    				<p>昨天：<?php echo $zuot_balance; ?></p>
    				<p>总共：<?php echo $all_arr_balance; ?></p>
				</p>
				
			</div>
		</div>
	</div>
</div>
	
	
	
	
	<?php
    	JsLoader::Jquery();    //加载jquery
    	JsLoader::Layer();
	?>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</body>
</html>
<script type="text/javascript" >
	$(function()
	{ 
		
	}) 


</script> 

