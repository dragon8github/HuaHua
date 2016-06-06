<?php

SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Lib/Class/Log.class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Admin.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库



//页面逻辑 开始==============================================
$_AdminCtrl = new AdminCtrl();

$arr = $_AdminCtrl->get_所有信息();

?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title></title>
	<link href="https://cdn.bootcss.com/bootstrap/3.3.6//css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	
	<table class="table table-hover" style="vertical-align: middle">
      <thead>
        <tr>
          <th>#</th>
          <th>编号</th>
          <th>用户头像</th>
          <th>用户名</th>
          <th>题目答案</th>
          <th>题目画像</th>
          <th>金额</th>
          <th>发布时间</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php 
            for($i = 0;$i<count($arr);$i++)
            {
                $id = $arr[$i]["AID"];
                $username = $arr[$i]["wx_name"];
                $answer = $arr[$i]["answer"];
                $wx_litpic = $arr[$i]["wx_litpic"];
                $question_pic = $arr[$i]["question_pic"];
                $price_count = $arr[$i]["price_count"];
                $release_time = $arr[$i]["release_time"];
                if($release_time != "") { $release_time = date("Y-m-d H:i:s",$release_time); }
        ?>
        <tr>                
              <td><?php echo $i; ?></td>
              <td><?php echo $id; ?></td>
              <td><img width="300" height="280" src="<?php echo $wx_litpic; ?>" /></td>
              <td><?php echo $username; ?></td>
              <td><?php echo $answer; ?></td>
              <td><img width="300" height="280" src="<?php echo $question_pic; ?>" /></td>
              <td>￥ <?php echo $price_count; ?></td>
              <td><?php echo $release_time ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
	
	<script src="https://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</body>
</html>
<script type="text/javascript" >
            
	$(function(){ 
			

		}) 


</script> 

