var UserDir = {};

//遵循模式===========================================
UserDir.ReadMe = function()
{
	console.log("避免污染全局空间，请开发者遵循我的做法");
}

//非遵循模式=========================================
function ReadMe()
{
	console.log("不建议这样使用，但特殊情况譬如为了开发速度也无需顾虑");
}


//页面逻辑============================================
$(function()
{
	$("#tixian").tap(function()
	{
			$("#tixian").text("提现中");
			
			$.ajax
			({
				   //发送请求前触发
			        beforeSend: function (xhr)
			        {
			        	layer.open({type: 2});
			        },
					data:{ type:"UserYuE" },
					success:function(data)
					{
						layer.closeAll(); $("#tixian").text("提现"); var obj = JSON.parse(data);
						if(obj.Status == "成功")
						{
							//layer.open ({ type:0, title:"信息", content:obj.Msg, icon:6, closeBtn: 0, area:'300px',offset:"30%" ,btn1:function(index) { window.location.reload(); }, end:function() { window.location.reload(); } });
							layer.open({ title: '信息', content:obj.Msg, btn: ['好的'],yes:function(index) { location.reload(); layer.close(index); } });
						}
						else
						{
							var content = obj.Msg;
							if(typeof(obj.Msg) == "object") { content = content[0]; }
							//layer.open ({ type:0, title:"信息",obj.Msg, icon:2, closeBtn: 2, area:'300px',offset:"30%" , btn1:function(index) { layer.closeAll();} });
							layer.open({ title: '信息', content:content, btn: ['好的'], yes:function(index) { location.reload(); layer.close(index); } });
						}
					},
					  //请求失败遇到异常触发
			        error: function (xhr, status, e)
			        {
			        	alert(	xhr.responseText);
			        }
			})
			
	})
	
	
	
	
})

