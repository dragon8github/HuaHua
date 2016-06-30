<?php

SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/Channel/ChannelLogin.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库
?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title></title>
	<link href="https://cdn.bootcss.com/bootstrap/3.3.6//css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container col-xs-12" id="login_container">
    	<div class="row clearfix">
    		<div class="col-xs-12 column">
    			<form class="form-horizontal" role="form">
    				<div class="form-group">
    					 <label for="inputEmail3" class="col-xs-3 control-label login_label">用户名：</label>
    					<div class="col-xs-9">
    						<input class="form-control" name="username" id="username" value="channel_01" type="text" placeholder="请输入用户名" />
    					</div>
    				</div>
    				<div class="form-group">
    					 <label for="inputPassword3" class="col-xs-3 control-label login_label">密码：</label>
    					<div class="col-xs-9">
    						<input class="form-control" name="password" value="channel_01" id="password" type="password" />
    					</div>
    				</div>
    				<div class="form-group">
    					<div class="col-xs-offset-3 col-xs-9">
    						<div class="checkbox">
    							 <label><input type="checkbox"  id="IsWeek" /> 一周内免登录</label>
    						</div>
    					</div>
    				</div>
    				<div class="form-group">
    					<div class="col-xs-offset-3 col-xs-9">
    						 <button type="button" class="btn btn-info" onclick = "loginAction()">登录</button>
    					</div>
    				</div>
    			</form>
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


loginAction = function()
{
	parent.layer.load(0,{time:5000});
	var _IsWeek = 0; 
	if($("#IsWeek").prop("checked") == true) _IsWeek = 1;
	$.ajax
	({                                                                                                                                              
		type:"POST",
		data:{"type":"login","username":$("#username").val(),"password":$("#password").val(),"week":_IsWeek},
		success:function(data)
		{
			console.log(data);
			var json = JSON.parse(data);
			var Status = json.Status;
			var Result = json.Result;
			var Msg = json.Msg;
			if(Status == "成功")
			{
				layer.msg( Msg, {time:1000}, function(){ window.location.href= Result;});
			}
			else if(Status == "失败")
			{
				layer.msg(Msg);
			}
		},
		complete:function(){parent.layer.closeAll("loading")}		
	})	
}
	

</script> 

