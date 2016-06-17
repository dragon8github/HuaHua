<?php

SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Lib/Class/Log.class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Query.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库

 

//页面逻辑 开始==============================================
$_query = new QueryCtrl();

$openid = $_SESSION["openid"];

$statements_arr = $_query->get_获取statements($openid);

// $answer_details_arr = $_query->get_获取answer_details($openid);
// $question_arr = $_query->get_获取question($openid);
// $user_arr = $_query->get_获取user($openid);



var_dump($statements_arr);
// var_dump($answer_details_arr);
// var_dump($question_arr);
// var_dump($user_arr);

exit();

?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title></title>
	<link href="https://cdn.bootcss.com/bootstrap/3.3.6//css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	
		<table class="table table-hover table-bordered" style="vertical-align: middle">
      <thead>
        <tr>
          <th>#</th>
          <th>用户提现总额</th>  
          <th>用户余额</th>
          <th>编号</th>
          <th>用户头像</th>
          <th>用户名</th>
          <th>用户id</th>     
          <th>题目答案</th>
          <th>题目画像</th>          
        </tr>
      </thead>
      <tbody>
       <?php 
            for($i = 0;$i<count($statements_arr);$i++)
            {
               
        ?>
        <tr>                 
              <td><?php echo $i; ?></td>
               
        </tr>
      <?php } ?>
      </tbody>
    </table>
	
	<?php
    	JsLoader::Jquery();    //加载jquery
    	JsLoader::Layer();
	?>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</body>
</html>