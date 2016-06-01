var ListDir = {};

//遵循模式===========================================
ListDir.ReadMe = function()
{
	console.log("避免污染全局空间，请开发者遵循我的做法");
}

//非遵循模式=========================================
function ReadMe()
{
	console.log("不建议这样使用，但特殊情况譬如为了开发速度也无需顾虑");
}

function Send()
{
	$.ajax
	({
		data:
		{
			type:"refresh"
		},
		success:function(data)
		{
				window.location.reload();
		}
	})
}

function showtime(t)
{ 
	//隐藏刷新按钮
	$("#box").slideUp("slow"); 				
	//解除绑定事件
	$("#refresh").unbind("tap",Send);		
	//开始倒计时
    for(i=1;i<=t;i++) { 
        window.setTimeout("update_p(" + i + ","+t+")", i * 1000); 
    } 
} 
function update_p(num,t) 
{ 
    if(num == t) 
    { 
    	//倒计时完成
    	$("title").text("可以刷新题库");
    	//显示刷新按钮
    	$("#box").slideDown('slow');		
    	//绑定事件
    	$("#refresh").bind("tap",Send);	
    } 
    else 
    { 
        printnr = t-num; 
        var content =  printnr +"秒后可刷新题目"; 
        $("title").text(content);
    } 
} 


//页面逻辑============================================
$(function()
{
	
	
})

