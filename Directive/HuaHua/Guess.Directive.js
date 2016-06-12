var GuessDir = {};

GuessDir.SetTimeOutObj =  new Array();
 
//遵循模式===========================================
GuessDir.ReadMe = function()
{
	console.log("避免污染全局空间，请开发者遵循我的做法");
}

//微信回调：正式提交答案
GuessDir.UpdateWxResult3 = function(res,myData)
{
	if(res.err_msg.indexOf("ok") >= 0)
	{				
		$.ajax
		({
			data: 
				{
					type:"TiJiaoDaAn",
					content:myData.content,
					order:myData.order,
					money:myData.money 
				},
			success:function(mydata)
			{
				var json = JSON.parse(mydata);
				var price = json["Result"].price;
				var tips =  json["Result"].tips;		//其实可以不要这个的，反正要刷新页面。但是还是准备好以防万一吧
				var info = "";
				if(price != 0) { info = "获得 ￥" + (price/100) + "元红包，请前往<a href='http://mp.weixin.qq.com/s?__biz=MzI3MTIxOTU1Mg==&mid=100000002&idx=2&sn=6e5b8b35f2d2724fab8b5f42a8d53bed#rd'>用户中心</a>查看"; }
				$("#submit").addClass("ui-state-disabled").unbind("tap",Send);	 
				if(json.Status == "成功")
				{
					if(json.Result.flag == 0)
					{
						//...回答错误
						layer.open ({  title:"信息", content:"回答错误 </br> 请查看答案提示，稍后再接再厉",btn: ['好的'],yes:function(index) { location.reload(); layer.close(index); },end:function(index) { location.reload(); layer.close(index); }  });
					}
					else
					{
						//...回答正确
						layer.open ({ title:"信息", content:"回答正确 </br>" + info,btn: ['好的'],yes:function(index) { location.reload(); layer.close(index); },end:function(index) { location.reload(); layer.close(index); } });
					}
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

//微信回调：正式添加红包或者更新画画题目的数据
GuessDir.UpdateWxResult2 = function(res,myData)
{	
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
				if (json.Status == '成功') { $("#reputHongBao").addClass("ui-state-disabled").unbind("click"); layer.open ({ title: "信息", content:json.Msg,btn: ['好的'],yes:function(index) { location.reload(); layer.close(index); },end:function(index) { location.reload(); layer.close(index); }  }); }
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


//购买道具，购买道具，购买道具
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
			data: { type:"GouMaiDaoJu", order:myData.order,money:myData.money,uid:myData.uid,tips_index:myData.tips_index,tips:myData.tips },
			success:function(Resultdata) 
			{
				
				
				//如果用户购买了道具，应该立即清空倒计时
				for(var i = 0;i<GuessDir.SetTimeOutObj.length;i++) { clearTimeout(GuessDir.SetTimeOutObj[i]); }
				//修改倒计时样式
		    	//$("title").text("你可以答题了"); $("#submit").text("提交").removeClass("ui-state-disabled");
				//正式后端数据
				var json = JSON.parse(Resultdata);
				//成功
				if (json.Status == '成功')
				{
					if (myData.tips != "") 
					{
						var tipsHtml = "";
						var tipsArr =myData.tips.split("");
						for(var j = 0;j<tipsArr.length;j++)
						{
							tipsHtml += "<span class='tipsFont'>" + tipsArr[j] +"</span>";
						}
						var tipsHtml = "<div class='tipsFontPanel'>" + tipsHtml + "</div>";
						
						$("#chengyutishi").show();
						$("#panelbody").append(tipsHtml);
						
						//修改成语提示
						var textlength = $("#panelbody").text().replace(/\s/g, "").length;
						$("#chengyunum").text(textlength);
						$("#chengyunum2").text(textlength/4);
					}
				}
				//失败
				else { AjaxDir.JqmAlert(json.Msg); }
			}
		})
	}
	else if(res.err_msg.indexOf("fail") >= 0)
	{
		//...失败
		layer.open ({  title:"信息", content:"微信支付失败",yes:function(index) {  layer.close(index); } });
		return false;   
	}
	else if(res.err_msg.indexOf("cancel") >= 0)
	{
		//...取消
		return false;   
	}
}


//提交画画的红包和模式的数据更新
GuessDir.DialogYes2 = function()
{

	var myData = {};																//发送给回调函数的参数
	myData.HongBaoJinE = $("#HongBaoJinE").val();				//红包金额
	myData.HongBaoCount = $("#HongBaoCount").val();		//红包个数
	
	//数据验证
	if(myData.HongBaoJinE.length == 0 ||  /^[1-9]+/.test(myData.HongBaoJinE) == false)
	{
		layer.open({ title: '信息', content: '金额必须为正整数',btn:["好的"],yes:function(){layer.closeAll();}  });
		//layer.tips("请输入整数型的数据",$("#HongBaoJinE"), { tips: [2, '#000'], time: 4000 }) 
		return false;
	} 
	else if(myData.HongBaoCount.length == 0 || /^[1-9]+/.test(myData.HongBaoCount) == false  )
	{
		layer.open({ title: '信息', content: '红包数量必须为正整数',btn:["好的"],yes:function(){layer.closeAll();} });
		//layer.tips("请输入整数型的数据",$("#HongBaoCount"), { tips: [2, '#000'], time: 4000 })
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
				callpay(wxjson,myData,GuessDir.UpdateWxResult2);
			}
	})
}




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
		//layer.tips("请输入四字成语",$("#search"), { tips: [1, '#000'], time: 4000 })
		return false;
	} 
	else
	{
		/* 这里根据应该根据模式来判断 
		 * 首先同样AJAX发送后台，先插入一条流水数据，然后返回一个Orderno.流水的type=7
		 * IF(model == "XXXX")
		 * {
		 * 		$.ajax
				({   
						data: { type:"DaTiHuaXiao"},
						success:function(data)
						{  
							var obj = JSON.parse(data);
							var order = obj["Result"].order;			//流水订单号
							var money = obj["Result"].money;	    //需要花销的金额
							var myobj = new Object();				   //新建一个对象
							myobj.order = order;						   //将”流水号“插入数据集中传递给回调函数
							myobj.content = v;						   //将”用户提交的答案“插入数据集中传递给回调函数
						    myobj.money = money;				   //将”答题花销“插入数据集中传递给回调函数	
							callpay(wxjson,myobj,GuessDir.UpdateWxResult3);
						}
				})
			}
		 * */
		
		$.ajax
		({   
				data: { type:"DaTiHuaXiao"},
				success:function(data)
				{  
					var obj = JSON.parse(data);
					var order = obj["Result"].order;			//流水订单号
					var money = obj["Result"].money;	    //需要花销的金额
					var myobj = new Object();				   //新建一个对象						
				    var wxjson = obj["Result"].wxjson;		//微信核心json
				    myobj.order = order;						   //将”流水号“插入数据集中传递给回调函数
					myobj.content = v;						   //将”用户提交的答案“插入数据集中传递给回调函数
				    myobj.money = money;				   //将”答题花销“插入数据集中传递给回调函数
				    
					callpay(wxjson,myobj,GuessDir.UpdateWxResult3);
				}
		})
			
		/*
		$.ajax
		({
			data: { type:"TiJiaoDaAn", content:v },
			success:function(mydata)
			{
				var json = JSON.parse(mydata);
				var price = json["Result"].price;
				var info = "";
				//if(price != 0) { info = "获得 ￥" + (price/100) + "元红包，请前往<a href='http://huahua.ncywjd.com/Home.php?p=user'>用户中心</a>查看"; }
				if(price != 0) { info = "获得 ￥" + (price/100) + "元红包，请前往<a href='http://mp.weixin.qq.com/s?__biz=MzI3MTIxOTU1Mg==&mid=100000002&idx=2&sn=6e5b8b35f2d2724fab8b5f42a8d53bed#rd'>用户中心</a>查看"; }
				$("#submit").addClass("ui-state-disabled").unbind("tap",Send);	 
				if(json.Status == "成功")
				{
					if(json.Result.flag == 0)
					{
						//...回答错误
						layer.open ({  title:"信息", content:"回答错误 </br> 你可以购买并使用道具帮助你解决问题",btn: ['好的'],yes:function(index) { location.reload(); layer.close(index); },end:function(index) { location.reload(); layer.close(index); }  });
					}
					else
					{
						//...回答正确
						layer.open ({ title:"信息", content:"回答正确 </br>" + info,btn: ['好的'],yes:function(index) { location.reload(); layer.close(index); },end:function(index) { location.reload(); layer.close(index); } });
					}
				} 
			}
		})
		*/
	}
}

function reputHongBao()
{
	$("#cy-tp-dialog2").popup("open");			
	$("#Cy-tp-DialogYes2").bind("click",GuessDir.DialogYes2);		//Dialog确定按钮
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
					var tips_index = obj['Result'].tips_index;
					var money =  obj["Result"].money;	//价格
					var uid = obj['Result'].uid;				//uid
					
					var myData = {};
					myData.order = order;	
					myData.money = money;
					myData.uid = uid;
					myData.tips = tips;
					myData.tips_index = tips_index;  
					
					    
					callpay(wxjson,myData,GuessDir.UpdateWxResult); 
				}
		})
	})
	
	//提交答案
	$("#submit").bind("click",Send);
	
	
	$("#share_hy").click(function() {
		$("#zhezhaocheng").width($(document).width());
		$("#zhezhaocheng").height($(document).height());
		$("#zhezhaocheng").show();
	})
	$("#zhezhaocheng").click(function() {
		$(this).hide();
	})
	
	//KO新增 修改道具金额

	$(".jsj").click(function()
							 {	
							 $(".crrtt").removeClass("crrtt");
								 $(this).addClass("crrtt");
								 var val= $(this).attr("val");
								 $("#HongBaoJinE").val(val);
								 $("#jinddd").text(val*$("#HongBaoCount").val());
								 $("#DaoJuJinE").val(($("#HongBaoJinE").val()*3)/10);
								
								 })
	
	$("#HongBaoCount").blur(function()
									  {
										 
										  var val=$("#HongBaoJinE").val();
										  var cot=$("#HongBaoCount").val();
										  if(cot>100)
										  {
											  $("#HongBaoCount").val(100);
											  }
										   $("#jinddd").text(val*$("#HongBaoCount").val());
										  })
})

