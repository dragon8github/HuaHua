var GuessDir = {};

GuessDir.SetTimeOutObj =  new Array();
 
//遵循模式===========================================
GuessDir.ReadMe = function()
{
	console.log("避免污染全局空间，请开发者遵循我的做法");
}
GuessDir.UpdateWxResult2 = function(res,myData)
{
	//alert("得到微信返回的对象是：" + res.err_msg); //获取微信返回结果
	
	if(res.err_msg.indexOf("ok") >= 0)
	{
		//先关闭原来的弹窗
		$("#cy-tp-dialog2").popup('close');  
		$.ajax
		({
			data: { type:"ChongXinTianJiaHongBao", order:myData.order,HongBaoJinE:myData.HongBaoJinE,HongBaoCount:myData.HongBaoCount },
			success:function(Resultdata)
			{
				//alert(Resultdata);
				var json = JSON.parse(Resultdata);
				if (json.Status == '成功') { $("#reputHongBao").addClass("ui-state-disabled").unbind("click"); layer.open ({ type: 0, title: "信息", content:json.Msg, icon: 6, closeBtn: 2, offset:'30%', btn1: function(index) { window.location.reload(); }, end: function() { window.location.reload(); } }); }
				else { alert(json.Msg);  }
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


GuessDir.UpdateWxResult = function(res,myData)
{
	//	get_brand_wcpay_request：ok 支付成功 
	//	get_brand_wcpay_request：cancel 支付过程中用户取消 
	//	get_brand_wcpay_request：fail 
	//alert("得到微信返回的对象是：" + res.err_msg); //获取微信返回结果
	
	if(res.err_msg.indexOf("ok") >= 0)
	{
		
		//先关闭原来的弹窗
		$("#cy-tp-dialog").popup('close');  
		$.ajax
		({
			data: { type:"GouMaiDaoJu", order:myData.order,money:myData.money,uid:myData.uid },
			success:function(Resultdata)
			{
				//清空倒计时
				for(var i = 0;i<GuessDir.SetTimeOutObj.length;i++) { clearTimeout(GuessDir.SetTimeOutObj[i]); }
				//倒计时完成
		    	$("title").text("你可以答题了"); $("#submit").text("提交").removeClass("ui-state-disabled");
				//alert(Resultdata);
				var json = JSON.parse(Resultdata);
				if(json.Status == '成功') {$("#chengyutishi").show(); $("#panelbody").append("<p>"+myData.daan+ myData.tips +"</p>"); }
				else { AjaxDir.JqmAlert(json.Msg); }
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
//KO新增 修改道具金额
$("#HongBaoJinE").blur(function()
								{
									var val=$("#HongBaoJinE").val();
									$("#DaoJuJinE").val($("#HongBaoJinE").val()*0.5);
									})


GuessDir.DialogYes2 = function()
{

	var myData = {};																//发送给回调函数的参数
	myData.HongBaoJinE = $("#HongBaoJinE").val();				//红包金额
	myData.HongBaoCount = $("#HongBaoCount").val();		//红包个数
	

	
	//数据验证
	if(myData.HongBaoJinE.length == 0)
	{
		layer.tips("请输入整数型的数据",$("#HongBaoJinE"), { tips: [2, '#000'], time: 4000 }) 
		return false;
	}
	else if(myData.HongBaoCount.length == 0)
	{
		layer.tips("请输入整数型的数据",$("#HongBaoCount"), { tips: [2, '#000'], time: 4000 })
		return false;
	}
	
	$.ajax
	({     //KO 新增加红包个数
			data: { type:"weixinzhifu2", price:myData.HongBaoJinE,cot:myData.HongBaoCount, stype:'1' },
			success:function(dddddd)
			{ 
				var obj = JSON.parse(dddddd);
				var order = obj["Result"].order;			//流水订单号
				var wxjson = obj["Result"].wxjson;		//微信核心json
				myData.order = order;							//将流水号插入数据集中传递给回调函数
				//alert(order + "|" + wxjson);
				callpay(wxjson,myData,GuessDir.UpdateWxResult2);
			}
	})
}



//GuessDir.DialogYes = function()
//{
//	//...确定购买时触发的事件
//	$.ajax
//	({
//			data: { type:"weixinzhifu" },
//			success:function(jsonstrdata)
//			{ 
//				var obj = JSON.parse(jsonstrdata);
//				var order = obj["Result"].order;			//流水订单号
//				var wxjson = obj["Result"].wxjson;		//微信核心json
//				var tips = obj["Result"].tips;				//提示1
//				var tips2 = obj["Result"].tips2;			//提示2
//				var money =  obj["Result"].money;	//价格
//				
//				alert("我是dialogYes:"+money);
//				
//				var myData = {};
//				myData.order = order;							//将流水号插入数据集中传递给回调函数
//				myData.tips = tips;
//				myData.tips2 = tips2;
//				myData.money = money;
//				callpay(wxjson,myData,GuessDir.UpdateWxResult); 
//			}
//	})
//}





//非遵循模式=========================================
function ReadMe()
{
	console.log("不建议这样使用，但特殊情况譬如为了开发速度也无需顾虑");
}



function showtime(t)
{ 		
	//解除绑定事件
	$("#submit").addClass("ui-state-disabled").unbind("tap",Send);	
	//开始倒计时
    for(i=1;i<=t;i++) {
    		var settimeoubobj = window.setTimeout("update_p(" + i + ","+t+")", i * 1000); 
    		GuessDir.SetTimeOutObj.push(settimeoubobj);
    	}
} 



function update_p(num,t) 
{ 
    if(num == t) 
    { 
    	//倒计时完成
    	$("title").text("你可以答题了");
    	$("#submit").text("提交").removeClass("ui-state-disabled");
    } 
    else 
    { 
        printnr = t-num; 
        var content =  printnr +"秒后可重新答题"; 
        $("title").text(content);
        $("#submit").text(content);
    } 
}

function Send()
{
	var v = $("#search").val();
	if(v.length > 4 || v.length == 0 || !/^[\u4E00-\u9FA5]+$/.test(v))
	{
		layer.tips("请输入四字成语",$("#search"), { tips: [1, '#000'], time: 4000 })
		return false;
	} 
	else
	{
		$.ajax
		({
			data: { type:"TiJiaoDaAn", content:v },
			success:function(mydata)
			{
				
				var json = JSON.parse(mydata);
				var price = json["Result"].price;
				var info = "";
				if(price != 0) { info = "获得 ￥" + (price/100) + "元红包，请前往<a href='http://huahua.ncywjd.com/Home.php?p=user'>用户中心</a>查看"; }
				$("#submit").addClass("ui-state-disabled").unbind("tap",Send);	
				if(json.Status == "成功")
				{
					if(json.Result.flag == 0)
					{
						//...回答错误
						layer.open ({ type:0, title:"信息", content:"回答错误 </br> 你可以购买并使用道具帮助你解决问题", icon:2, closeBtn: 2, offset:['30%','10%'], btn1:function(index) { window.location.reload(); }, end:function(){ window.location.reload(); } });
					}
					else
					{
						//...回答正确
						layer.open ({ type:0, title:"信息", content:"回答正确 </br>" + info, icon:6, closeBtn: 2, offset:['30%','10%'], btn1:function(index) { window.location.reload(); }, end:function() { window.location.reload(); } });
					}
				}
			}
		})
	}
}

function reputHongBao()
{
	$("#cy-tp-dialog2").popup("open");			
	$("#Cy-tp-DialogYes2").bind("tap",GuessDir.DialogYes2);		//Dialog确定按钮
}

//页面逻辑============================================
$(function()
{
	//道具购买
	$("#radio-choice-0a").bind("click",function()
	{
		//此处修改为 不弹出层在确定支付 改为直接支付
		var val=$("#radio-choice-0a").attr("data-tipstype");
		
		$.ajax
		({
				data: { type:"weixinzhifu"},
				success:function(jsonstrdata)
				{ 
					var obj = JSON.parse(jsonstrdata);
					var order = obj["Result"].order;			//流水订单号
					var wxjson = obj["Result"].wxjson;		//微信核心json
					var tips = obj["Result"].tips;	
					var tips2 = obj["Result"].tips2;//提示
					var money =  obj["Result"].money;	//价格
					var uid = obj['Result'].uid;				//uid
					
					var myData = {};
					myData.order = order;	
					myData.money = money;
					myData.uid = uid;
					
					//将流水号插入数据集中传递给回调函数
					if(val<1)
					{
						myData.tips = tips;
						myData.daan="答案提示1：";
						$("#radio-choice-0a").attr("data-tipstype","1");
					}
					else if(val<2)
					{
						myData.tips = tips2;
						myData.daan="答案提示2：";
						$("#radio-choice-0a").attr("data-tipstype","2");
					}
					else
					{
						myData.tips = "";
						myData.daan="";
					}
					
				
					callpay(wxjson,myData,GuessDir.UpdateWxResult); 
				}
		})
		//$("#cy-tp-dialog").popup('open'); 
		//$("#Cy-tp-DialogYes").bind("tap",GuessDir.DialogYes);
	})
	
	//提交答案
	$("#submit").bind("click",Send);
})

