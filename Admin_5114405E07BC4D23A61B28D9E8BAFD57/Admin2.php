<?php

SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Lib/Class/Log.class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/Admin/Admin2.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库



//页面逻辑 开始==============================================
$_Admin2Ctrl = new Admin2Ctrl();

$arr = $_Admin2Ctrl->get_所有信息();

$arr_chuti = $_Admin2Ctrl->get_今天昨天总共的出题情况();
$arr_dati = $_Admin2Ctrl->get_今天昨天总共的答题情况();
$arr_tixian = $_Admin2Ctrl->get_今天昨天总共的提现情况();


//今天的出题总次数
$today_chuti = $arr_chuti[0]["count(*)"];
//昨天的出题总次数
$yestoday_chuti = $arr_chuti[1]["count(*)"];
//出题总次数
$all_chuti = $arr_chuti[2]["count(*)"];

//今天的答题总次数
$today_dati = $arr_dati[0]["count(*)"];
//昨天的答题总次数
$yestoday_dati = $arr_dati[1]["count(*)"];
//答题总次数
$all_dati = $arr_dati[2]["count(*)"];

//今天的提现总金额
$today_tixian= Lee::round($arr_tixian[0]["IFNULL(sum(price),0)"] / 100);
//昨天的提现总金额
$yestoday_tixian = Lee::round($arr_tixian[1]["IFNULL(sum(price),0)"] /100);
//提现总金额
$all_tixian = Lee::round($arr_tixian[2]["IFNULL(sum(price),0)"] / 100);

//获取充值总额
$CHONGZHISUM = $_Admin2Ctrl->get_获取充值总额()[0]["CHONGZHISUM"];


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
          <th>用户充值总额
          <br>总共：<?php echo Lee::round($CHONGZHISUM); ?>  
          </th>
          <th>真实余额（statements）</th>
          <th><a href="?orderby=yonghuyue">用户余额（user）</a></th>
          <th>
          <a href="?orderby=yonghutixianzonge">用户提现总额</a>
          <br>今天：<?php echo $today_tixian; ?>
          <br>昨天：<?php echo $yestoday_tixian; ?>
          <br>总共：<?php echo $all_tixian; ?>      
          </th>              
          <th><a href="?orderby=daticishu">答题次数</a>
          <br>今天：<?php echo $today_dati; ?>
          <br>昨天：<?php echo $yestoday_dati; ?>
          <br>总共：    <?php echo $all_dati; ?>      
          </th>      
          <th><a href="?orderby=chuticishu">出题次数</a>
          <br>今天：<?php echo $today_chuti; ?>
          <br>昨天：<?php echo $yestoday_chuti; ?>
          <br>总共：<?php echo $all_chuti; ?>
          </th>
        </tr>
      </thead>
      <tbody>
        <?php 
            for($i = 0;$i<count($arr);$i++)
            {
                $openid = $arr[$i]["openid"];
                $wx_name = $arr[$i]["wx_name"];
                $wx_litpic = $arr[$i]["wx_litpic"]; 
                $balance = Lee::round( $arr[$i]["balance"] )/ 100;
                $mysum = Lee::round( $arr[$i]["mysum"] )/ 100;
                $myanswer_count = $arr[$i]["myanswer_count"];
                $myquestion_cont = $arr[$i]["myquestion_cont"];                
                $user_balance =  Lee::round( $_Admin2Ctrl->get_获取真实正确的需要提现的数据($openid)); 
                $chongzhi_balance = Lee::round( $_Admin2Ctrl->get_获取用户充值的总额($openid));
        ?>
        <tr <?php /* if($user_balance != $balance) echo "class='danger'"*/ ?>>                 
              <td><?php echo $i; ?></td>
              <td><?php echo $openid; ?></td>
              <td><?php echo $wx_name; ?></td>
              <td><img width="100" height="100" src="<?php echo $wx_litpic; ?>" /></td>
              <td><?php echo $chongzhi_balance; ?></td>
              <td><?php echo $user_balance; ?></td>
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

