<?php 
require_once "Wx_Class.php";
$ko=new WX_INT();
$code = $_GET["code"]; 
echo 'code:'.$code;
$json_obj=$ko->getOpenid($code);    //获取openid
$openid=$json_obj['openid'];
$acc_token=$json_obj['access_token'];

$refresh_token=$json_obj['refresh_token'];



	  
?>
<script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>


	
	<script type="text/javascript" src="jquery.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function()
	{
			$("#zhifu").click(function()
			{
				$.ajax({
					   type: "POST",
					   url: "zhifu.php",
					   data: "openid=<?php echo $openid; ?>",
					   success: function(msg){
						 alert(msg);
					   }
					});
			})
			
	})
	</script>
	

	
		<div id='zhifu' style="width:200px;height:200px;border:solid 1px #000000">提现接口</div>