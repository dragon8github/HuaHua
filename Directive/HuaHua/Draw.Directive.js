var DrawDir = {};

//遵循模式===========================================
DrawDir.ReadMe = function()
{
	console.log("避免污染全局空间，请开发者遵循我定义函数做法");
}

DrawDir.UpdateWxResult = function(res,myData)
{
	//	get_brand_wcpay_request：ok 支付成功 
	//	get_brand_wcpay_request：cancel 支付过程中用户取消 
	//	get_brand_wcpay_request：fail 
	//alert("得到微信返回的对象是：" + res.err_msg); //获取微信返回结果
	
	if(res.err_msg.indexOf("ok") >= 0)
	{
		//支付成功之后，我要将数据更新到指定的Id中去
		var myid = myData.id;
		var Prop = myData.Prop;
		var HongBaoJinE = myData.HongBaoJinE;
		var HongBaoCount = myData.HongBaoCount;
		var order = myData.order;
		
		//alert(myid + "|" + Prop + "|" + HongBaoJinE + "|" + HongBaoCount + "|" + order);
		
		$.ajax
		({
			data:
			{
				type:"UpdateQuestionWithId",
				id:myid,
				price_count:HongBaoJinE,
				hongbao_count:HongBaoCount,
				prop:Prop,
				order:order
			},
			success:function(Resultdata)
			{
				//alert(data);
				var json = JSON.parse(Resultdata);
				if(json.Status == '成功')
				{
					var url = "http://huahua.ncywjd.com/Home.php?p=guess&q="+myid;	//分享的链接.需要和微信接口对接
					share(url);
					AjaxDir.JqmAlert("制作成功");
					$("#cy-tp-dialog").popup('close'); 
					$("#KaiShiZhizuo").removeClass("ui-state-disabled").text("分享到朋友圈").bind("tap",DrawDir.ShareFriend);
				}
				else
				{
					AjaxDir.JqmAlert(json.Msg);
				}
			}
		})
	}
	else if(res.err_msg.indexOf("fail") >= 0)
	{
		//...失败
		return false;   
	}
	else if(res.err_msg.indexOf("cancel") >= 0)
	{
		//...取消
		return false;   
	}
	
	
}

//Dialog点击确定
DrawDir.DialogYes = function(e)
{
	var myData = {};																//发送给回调函数的参数
	myData.id = e.data.id;														//这个ID得传送到支付成功之后，作为guess?q=id的参数	
	myData.Prop = e.data.Prop;												//这个ID得传送到支付成功之后，作为guess?q=id的参数
	myData.HongBaoJinE = $("#HongBaoJinE").val();				//红包金额
	myData.HongBaoCount = $("#HongBaoCount").val();		//红包个数
	
	//alert(myData.id + "," + myData.Prop + "," + myData.HongBaoJinE + "," + myData.HongBaoCount);
	
	//数据验证
	if(HongBaoJinE.length == 0)
	{
		layer.tips("请输入整数型的数据",$("#HongBaoJinE"),
		{
			tips: [2, '#000'],
            time: 4000
		})
		return false;
	}
	else if(HongBaoCount.length == 0)
	{
		layer.tips("请输入整数型的数据",$("#HongBaoCount"),
		{
			tips: [2, '#000'],
            time: 4000
		})
		return false;
	}
	
	$.ajax
	({
			data:
			{
				type:"weixinzhifu",  
				price:myData.HongBaoJinE,
				stype:'1'
			},  
			success:function(dddddd)
			{ 
				var obj = JSON.parse(dddddd);
				var order = obj["Result"].order;			//流水订单号
				var wxjson = obj["Result"].wxjson;		//微信核心json
				myData.order = order;							//将流水号插入数据集中传递给回调函数
				
				//alert(order + "|" + wxjson);
				callpay(wxjson,myData,DrawDir.UpdateWxResult);
			}
	})
}




DrawDir.DialogNo = function(e)
{
	var id = e.id;		//获取id，作为分享页面的链接
	var url = "http://huahua.ncywjd.com/Home.php?p=guess&q="+id;	//分享的链接.需要和微信接口对接
	//...点击取消
	$("#KaiShiZhizuo").removeClass("ui-state-disabled").text("分享到朋友圈").bind("tap",DrawDir.ShareFriend);
}

DrawDir.ShareFriend = function()
{
	alert("弹出蒙蔽");
}


//非遵循模式=========================================
function ReadMe()
{
	console.log("不建议这样使用，但特殊情况譬如为了开发速度也无需顾虑");
}
var line_color="#ff0000";
// 从 canvas 提取图片 image  
function convertCanvasToImage(canvas) {  
    //新Image对象，可以理解为DOM  
    var image = new Image();  
    // canvas.toDataURL 返回的是一串Base64编码的URL，当然,浏览器自己肯定支持  
    // 指定格式 PNG  

		 image.src = canvas.toDataURL("image/jpeg"); 
		 return image;  
		
    
    
}  
//get canvas
var canvas = document.getElementById("canvas");

//full screen
canvas.width = window.innerWidth;
canvas.height = window.innerHeight -80;

var touchable = 'createTouch' in document;
if (touchable) {
    canvas.addEventListener('touchstart', onTouchStart, false);
    canvas.addEventListener('touchmove', onTouchMove, false);
} 
else {
   // alert("touchable is false !");
}

//上一次触摸坐标
var lastX;
var lastY;
var ctx = canvas.getContext("2d");
ctx.fillStyle="#F9F9F9";
ctx.fillRect(0,0,canvas.width,canvas.height);

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
    }
    catch(err)
    {
        alert("异常错误："+err.description);
    } //画圆
}
function drawRound(x, y) {
    ctx.fillStyle = line_color;
    ctx.beginPath();
    ctx.arc(x, y, $(".current").attr("val"), 0, Math.PI * 2, true);
    ctx.closePath();
    ctx.fill();
}
//画线
function drawLine(startX, startY, endX, endY) {
	//alert(canvas.offsetHeight);
    ctx.beginPath();
    ctx.lineCap = "round";
    ctx.moveTo(startX, startY);
    ctx.lineTo(endX, endY);
    ctx.stroke();
}
function keyPress() {
    var keyCode = event.keyCode;
    if ((keyCode >= 48 && keyCode <= 57)) {
        event.returnValue = true;
    } else {
        event.returnValue = false;
    }
}









//页面逻辑============================================
$(function()
{
	
	
		$("#KaiShiZhizuo").tap(function()
		{
			var c=document.getElementById("canvas");
			var pic=convertCanvasToImage(c);
			
			var obj = {};
			obj.type = "TiJiaoQuestion";
			obj.question_pic = pic.attributes[0].nodeValue;
				
				$.ajax
				({
					data:obj,
					success:function(data)
					{
						var json = JSON.parse(data);
						var file = json["Result"].file;
						var htmlimg = "<img src='"+file+"' width='99%' />";
						$("#controlgroup").hide();
						$("#replace_images").html(htmlimg);
						if(json.Status == "成功") 
						{
							var Result  =json["Result"].id;		
							var Prop = json["Result"].prop;
							//$("#cy-tp-dialog").popup("open");	
							
							//进行页面跳转
							self.location='../../Home.php?p=guess&q='+Result; 
							
							$("#KaiShiZhizuo").unbind("tap").addClass("ui-state-disabled");							//防止重复提交
							$("#Cy-tp-DialogYes").bind("tap",{'id':Result,'Prop':Prop},DrawDir.DialogYes);		//Dialog确定按钮
							$("#Cy-tp-DialogNo").bind("tap",{'id':Result},DrawDir.DialogNo);					    //Dialog取消按钮
							$("#HongBaoJinE,#HongBaoCount").keyup(function()
							{
									var v = $(this).val();
									if(v.length == 0 || parseInt(v) != v || v == 0)
									{
										$("#Cy-tp-DialogYes").addClass("ui-state-disabled");
										layer.tips("请输入整数型的数据",$(this),
										{
											tips: [2, '#000'],
								            time: 4000
										})
									}
									else
									{
										if($(this).attr("id") == "HongBaoJinE")
										{
											$("#DaoJuJinE").val((v * Prop).toFixed(1));
										}
										$("#Cy-tp-DialogYes").removeClass("ui-state-disabled");
									}
							})
						}
						else if(json.Status == "失败")
						{
							AjaxDir.JqmAlert(json.Msg);
						}
					}
				})
		})
		  $(".xiangpicha").click(function() 
		  {
			 // alert('huabu');
			        line_color = "#F9F9F9";
		  })
	    $("#czhb").blur(function() {
	        var val1 = $("#czhb").val();
	        var val2 = $("#hbgs").val();
	        var val = val1 * val2;
	        $("#xcz").text(val);
	    }) 
	    $("#hbgs").blur(function() {
	        var val1 = $("#czhb").val();
	        var val2 = $("#hbgs").val();
	        var val = val1 * val2;
	        $("#xcz").text(val);
	    })
	    $("#radio-choice-c").click(function() 
		{
	        line_color = "#ff0000";
	        $("#radio-choice-c").removeClass("current");
	        $("#radio-choice-d").removeClass("current");
	        $(this).addClass("current");
	    })  
		
	    $("#radio-choice-d").click(function() 
		{
	        line_color = "#ff0000";
	        $("#radio-choice-c").removeClass("current");
	        $("#radio-choice-d").removeClass("current");
	        $(this).addClass("current");
	    })
	    //调整划线大小
	    $(".clear_canvas").click(function() 
		{
	        var ctx = $("#canvas")[0].getContext("2d");
	        ctx.clearRect(0, 0, $("#canvas")[0].width, $("#canvas")[0].height);
	    })
})

