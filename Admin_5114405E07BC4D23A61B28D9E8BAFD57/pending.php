<?php

SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Lib/Class/Log.class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Pending.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库



//页面逻辑 开始==============================================
$_PendingCtrl = new PendingCtrl();

$arr = $_PendingCtrl->get_所有信息();

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
          <th>订单号</th>
          <th>Openid</th>  
          <th>微信名</th>
          <th>用户头像</th>
          <th>申请余额</th>
          <th>申请时间</th>
          <th>参考金额</th>
          <th>用户当前余额</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php 
            for($i = 0;$i<count($arr);$i++)
            {
                $orderid = $arr[$i]["orderid"];
                $id = $arr[$i]["uid"];
                $wx_name = $arr[$i]["wx_name"];
                $wx_litpic = $arr[$i]["wx_litpic"];
                $balance = $arr[$i]["shenqing_balance"] / 100;         
                $release_time = $arr[$i]["happen_time"];
                $Cankao_balance = $_PendingCtrl->get_获取真实正确的需要提现的数据($id);
                $balance1 = $arr[$i]["user_balance"] / 100;
        ?>
        <tr>                 
              <td><?php echo $i; ?></td>
              <td><?php echo $orderid; ?></td>
              <td><?php echo $id; ?></td>
              <td><?php echo $wx_name; ?></td>
              <td><img width="100" height="100" src="<?php echo $wx_litpic; ?>" /></td>
              <td><?php echo $balance; ?></td>
              <td><?php echo date("Y-m-d H:i:s",$release_time); ?></td>
              <td><?php echo $Cankao_balance; ?></td>
              <td><?php echo $balance1; ?></td>
              <td>
                <button type="button" class="btn btn-warning qingkongyue"  data-orderid = "<?php echo $orderid; ?>" data-id="<?php echo $id; ?>" data-username="<?php echo $wx_name; ?>"  data-balance = "<?php echo $balance1; ?>"  >清空余额</button>
                <button type="button" class="btn btn-primary shenhetongguo "  data-orderid = "<?php echo $orderid; ?>"  data-id="<?php echo $id; ?>"  data-username="<?php echo $wx_name; ?>" data-balance = "<?php echo $balance; ?>"  >审核通过</button>   
              </td>
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
		$(".qingkongyue").click(function()
		{
			var self = $(this);
			var v = $(this).attr("data-balance");
			var id = $(this).attr("data-id");
			var username = $(this).attr("data-username");
			
			layer.open({
				title: "清空确认",
				content: "你确定清空openid为 :" + id + " <br /> 用户名为：" + username + " <br /> 余额为：" + v,
				btn: ["确定", "取消"],
				yes: function() 
				{
					layer.load(0, { shade: [0.8, '#fff'], time: 10 * 1000 });
					$.ajax
					({
						type: "post",
						data: {
							type: "clear_balance",
							openid: id
						},
						success: function(data) 
						{
							layer.msg("修改完成");
							self.parent().html("修改完成");
						}
					}) 
					layer.closeAll();
				}
			})
		})


		$(".shenhetongguo").click(function()
		{	
			var self = $(this);
			var v = $(this).attr("data-balance");
			var id = $(this).attr("data-id");
			var username = $(this).attr("data-username");
			var myorderid = $(this).attr("data-orderid");	

			
			layer.open
			({
            			title: "审核通过确定",
            			content: "你确定更新openid为 :" + id + " <br /> 用户名为：" + username + " <br /> 更新余额为：" + v,
            			btn: ["确定", "取消"],
            			yes: function() 
            			{
            				layer.load(0, { shade: [0.8, '#fff'], time: 10 * 1000 });
            				$.ajax
            				({
            					type: "post",
            					data: {
            						type: "Add_balance",
            						openid: id,
            						balance: v,
            						orderid:myorderid
            					},
            					success: function(data) 
            					{
                					var json = JSON.parse(data);
                					if(json.Status == "失败")
                					{                	 
                    					var content = json.Msg;
                						if(typeof(obj.Msg) == "object") { content = content[0]; };
                    					layer.msg(content);		           
                    					return false;
                    				} 
                					else
                					{
            							layer.msg(json.Msg);
            							self.parent().html("修改完成");
                					}
            					}
            				}) 
            				layer.closeAll();
            			}
			})			
		})
	}) 


</script> 

