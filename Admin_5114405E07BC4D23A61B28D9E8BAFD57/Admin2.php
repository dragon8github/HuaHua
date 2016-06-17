<?php

SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Lib/Class/Log.class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Admin2.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库



//页面逻辑 开始==============================================
$_Admin2Ctrl = new Admin2Ctrl();

$arr = $_Admin2Ctrl->get_所有信息();

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
          <th>用户id</th>
          <th>用户名</th>  
          <th>用户头像</th>
          <th><a href="?orderby=yonghuyue">用户余额</a></th>
          <th><a href="?orderby=yonghutixianzonge">用户提现总额</a></th>              
          <th><a href="?orderby=daticishu">答题次数</a></th>      
          <th><a href="?orderby=chuticishu">出题次数</a></th>
        </tr>
      </thead>
      <tbody>
        <?php 
            for($i = 0;$i<count($arr);$i++)
            {
                $openid = $arr[$i]["openid"];
                $wx_name = $arr[$i]["wx_name"];
                $wx_litpic = $arr[$i]["wx_litpic"];
                $balance = $arr[$i]["balance"];
                $mysum = $arr[$i]["mysum"];
                $myanswer_count = $arr[$i]["myanswer_count"];
                $myquestion_cont = $arr[$i]["myquestion_cont"];
        ?>
        <tr>                 
              <td><?php echo $i; ?></td>
              <td><?php echo $openid; ?></td>
              <td><?php echo $wx_name; ?></td>
              <td><img width="100" height="100" src="<?php echo $wx_litpic; ?>" /></td>
              <td><?php echo $balance; ?></td>
              <td><?php echo $mysum; ?></td>
              <td><?php echo $myanswer_count; ?></td>
              <td><?php echo $myquestion_cont; ?></td>              
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
<script type="text/javascript" >
	$(function()
	{ 
		
	}) 


</script> 

