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
	
	
	<table class="table table-hover table-bordered" style="vertical-align: middle">
      <thead>
        <tr>
          <th>#</th>
          <th>编号</th>
          <th>用户头像</th>
          <th>用户名</th>
          <th>用户id</th>
          <th>题目答案</th>
          <th>题目画像</th>
          <th><a href="?orderby=descmoney">剩余金额</a></th>          
          <th><a href="?orderby=desctime">发布时间</a></th>
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
                $release_time = $arr[$i]["release_time"];
                $userid = $arr[$i]["openid"];
                $model = $arr[$i]["model"];
                if($release_time != "") { $release_time = date("Y-m-d H:i:s",$release_time); }
                $shengyujine =$arr[$i]["shengyujine"];
        ?>
        <tr>                
              <td><?php echo $i; ?></td>
              <td><?php echo $id; ?></td>
              <td><img width="100" height="100" src="<?php echo $wx_litpic; ?>" /></td>
              <td><?php echo $username; ?></td>
              <td><?php echo $userid; ?></td>
              <td><?php echo $answer; ?></td>                
              <td><img width="300" height="280" src="<?php echo $question_pic; ?>" /></td>
              <td>￥ <?php echo $shengyujine / 100; ?></td>
              <td><?php echo $release_time ?></td>
              <?php if($model == "") { ?>
                    <td>
                            <button type="button" class="btn btn-warning jinzhita" data-id="<?php echo $id; ?>" data-model="0">禁止它</button>
                            <button type="button" class="btn btn-primary kaiqita" data-id="<?php echo $id; ?>" data-model="1">开启它</button>                            
                    </td>
              <?php } else if($model == "0") { ?>
                   <td>
                             <button type="button" class="btn btn-danger" data-id="<?php echo $id; ?>">作弊者，已禁止</button>
                            <button type="button" class="btn btn-primary kaiqita" data-id="<?php echo $id; ?>" data-model="1">开启它</button>
                    </td>   
               <?php } else if($model == "1"){ ?>
                    <td>
                            <button type="button" class="btn btn-warning jinzhita" data-id="<?php echo $id; ?>" data-model="0">禁止它</button>
                            <button type="button" class="btn btn-default " data-id="<?php echo $id; ?>" >本题已开启</button>                            
                    </td>
               <?php } ?>
        </tr>
      <?php } ?>
      </tbody>
    </table>
	
	
	<?php
    	JsLoader::Jquery();    //加载jquery
    	JsLoader::Layermobile();
	?>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</body>
</html>
<script type="text/javascript" >
	$(function()
	{ 
		$(".jinzhita,.kaiqita").click(function()
		{
				var self = $(this);
				var id = $(this).attr("data-id");
				var m = $(this).attr("data-model") || "0";
				var content = "你确定" +  $(this).text() + "吗？"; 


				layer.open({
					 title: '信息', 
					 content: content, 
					 btn:["确定","取消"],
					yes:function()
					{
						$.ajax
						({
								type:"post",
								data:
								{
									type:"update",
									question_id : id,
									model:m
								},
								success:function(data)
								{
									//	alert(data);	 
									self.html("修改完成");									
								}
						})
						layer.closeAll();
					} 
				});
		  })
	}) 


</script> 

