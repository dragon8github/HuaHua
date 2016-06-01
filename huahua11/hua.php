<!DOCTYPE html>
<html >

	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="format-detection" content="telephone=no">
		<meta name="format-detection" content="address=no">
		<meta name="Content-Security-Policy" content="default-src:*">
		<meta name="viewport" content="height=device-height, width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
	
		<title>乌龙摆尾</title>
		<script type="text/javascript" src="jquery.min.js"></script>
	
		<script type="text/javascript">
			function keyPress() {
				var keyCode = event.keyCode;
				if ((keyCode >= 48 && keyCode <= 57))
				{
					event.returnValue = true;
				} else {
					event.returnValue = false;
				}
			}
			$(document).ready(function() {

var line_color="#ff0000";
$("#xiangpicha").click(function()
{
line_color="#ffffff";

})
				

				$("#chongzhi").click(function()
						{
							
							
						});
							/*var timestamp=new Date().getTime();
							function onBridgeReady(){
								WeixinJSBridge.invoke(
										'getBrandWCPayRequest', {
											"appId": "wx0b62213ee8ee0c90",     //公众号名称，由商户传入
										"timeStamp":timestamp,         //时间戳，自1970年以来的秒数
										"nonceStr" : "e61463f8efa94090b1f366cccfbbb444", //随机串
										"package" : "prepay_id=u802345jgfjsdfgsdg888",
										"signType" : "MD5",         //微信签名方式：
										"paySign" : "70EA570631E4BB79628FBCA90534C63FF7FADD89" //微信签名
							},
							function(res){
								if(res.err_msg == "get_brand_wcpay_request:ok" ) {}     // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。
										}
							);
							}
							if (typeof WeixinJSBridge == "undefined"){
								if( document.addEventListener ){
									document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
								}else if (document.attachEvent){
									document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
									document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
								}
							}else{
								onBridgeReady();
							}
*/
						//})
	$("#ok").click(function()
	{
		$("#edit_area").hide();
		$("#release_area").show();
		
	})
		$("#czhb").blur(function()
				{
					var val1=$("#czhb").val();
					var val2=$("#hbgs").val();
					var val=val1*val2;
					$("#xcz").text(val);
				})
		$("#hbgs").blur(function()
		{
			var val1=$("#czhb").val();
			var val2=$("#hbgs").val();
			var val=val1*val2;
			$("#xcz").text(val);
		})

				$(".line_s").click(function()
						{
						line_color="#ff0000";
							$(".line_s").removeClass("current");
							$(".line_b").removeClass("current");
							$(this).addClass("current");

						})
				$(".line_b").click(function()
				{
				line_color="#ff0000";
					$(".line_s").removeClass("current");
					$(".line_b").removeClass("current");
					$(this).addClass("current");

				})
		//调整划线大小
	$("#clear_canvas").click(function()
	{
		
		var ctx = $("#canvas")[0].getContext("2d");
			ctx.clearRect(0,0,$("#canvas")[0].width,$("#canvas")[0].height);
		
	})

	//get canvas
	var canvas = document.getElementById("canvas");
	//full screen
	canvas.width = window.innerWidth;
	canvas.height = window.innerHeight * 0.8;

	var touchable = 'createTouch' in document;
	if (touchable) {
		canvas.addEventListener('touchstart', onTouchStart, false);
		canvas.addEventListener('touchmove', onTouchMove, false);
	} else {
		alert("touchable is false !");
	}
	//上一次触摸坐标
	var lastX;
	var lastY;
	var ctx = canvas.getContext("2d");
	ctx.lineWidth = 2; //画笔粗细
	
	function onTouchStart(event) {
		event.preventDefault();
		ctx.strokeStyle = line_color; //画笔颜色</p> <p>//触摸开始事件
		lastX = event.touches[0].clientX;
		lastY = event.touches[0].clientY;
		ctx.lineWidth = $(".current").attr("val"); //画笔粗细
		drawLine(lastX, lastY, event.touches[0].clientX, event.touches[0].clientY);
	}
	//触摸滑动事件
	function onTouchMove(event) {
		try {
			event.preventDefault();
			ctx.strokeStyle = line_color; //画笔颜色</p> <p>//触摸开始事件
			ctx.lineWidth = $(".current").attr("val"); //画笔粗细
			drawLine(lastX, lastY, event.touches[0].clientX, event.touches[0].clientY);
			lastX = event.touches[0].clientX;
			lastY = event.touches[0].clientY;
		} catch (err) {
			alert(err.description);
		} //画圆
	}

	function drawRound(x, y) {
		ctx.fillStyle = line_color;
		ctx.beginPath();
		ctx.arc(x, y,$(".current").attr("val"), 0, Math.PI * 2, true);
		ctx.closePath();
		ctx.fill();
	}
	//画线
	function drawLine(startX, startY, endX, endY) {
		ctx.beginPath();
		ctx.lineCap = "round";
		ctx.moveTo(startX, startY);
		ctx.lineTo(endX, endY);
		ctx.stroke();
	}

})</script>

	</head>

	<body style="background-color: #ffffff;">
		
		<canvas id="canvas" style="border:dashed 1px #333333;"></canvas>
	
		<div id="edit_area"><button class="line_s current" val="2">小</button><button class="line_b" val="5">大</button><button id="xiangpicha">橡皮擦</button> <button id="clear_canvas">清除画布</button><button id="ok">完成制作</button></div>
			<div style="font-size:20px;">成语：<volist name="list" id="vo">乌龙摆尾</volist></div>
		<div id="release_area" style="display:none">
			<p>猜中红包金额：<input type="text" style="ime-mode:disabled;" onpaste="return false;"  onkeypress="keyPress()" id="czhb" value="1" /></p>
			<p>红包个数：<input type="text" style="ime-mode:disabled;" onpaste="return false;"  onkeypress="keyPress()" id="hbgs" value="1" /></p>
			<p>道具1金额<input type="text" style="ime-mode:disabled;" onpaste="return false;"  onkeypress="keyPress()" /></p>
			<p>道具2金额<input type="text" style="ime-mode:disabled;" onpaste="return false;"  onkeypress="keyPress()" /></p>
			<p>共需充值：<span id="xcz"></span>元</p>
			<button id="chongzhi">充值完成发布</button>

		</div>
	</body>

</html>