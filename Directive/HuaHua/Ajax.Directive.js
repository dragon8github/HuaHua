var AjaxDir = {};

//遵循模式===========================================

AjaxDir.ReadMe = function()
{
	console.log("避免污染全局空间，请开发者遵循我的做法");
}

AjaxDir.JqmLoad = function()
{
    $.mobile.loading( "show", {
            text: "loading",
            textVisible: true,
            theme: 'b',
            textonly: false,
            html: ''
    });
}



AjaxDir.JqmAlert = function(str)
{
	$("#Cy-Tp-Alert-Content").html(str);
	$("#Cy-Tp-Alert").popup('open'); 
}

AjaxDir.Ajax_全局设置 = function ()
{
    $.ajaxSetup
    ({
        //公共参数
        timeout: 10000,
        type: "POST",
        //发送请求前触发
        beforeSend: function (xhr)
        {
        	AjaxDir.JqmLoad();
            console.log("AJAX请求发送之前beforeSend_xhr:");
            console.log(xhr);
        },
        //请求成功后触发,通常不写这个
        success: function (data)
        {
        	AjaxDir.JqmAlert("发送成功");
            console.log("AJAX请求发送成功之后success:");
            console.log(data);
        },
        //请求失败遇到异常触发
        error: function (xhr, status, e)
        {
        	alert(	xhr.responseText);
        	
        	if(status == "error")
    		{
        		AjaxDir.JqmAlert("请求错误 错误信息：</br>" + xhr.responseText);
    		}
        	else if (status == 'timeout')
            {
            	AjaxDir.JqmAlert("请求超时：</br>" + xhr.responseText);
            } 
            else     //其他错误情况以后调试过程中认知并且加入
            { 
            	AjaxDir.JqmAlert("请求失败：</br>" + xhr.responseText);
            } 
            console.log("AJAX出错了error_xhr:");
            console.log(xhr);
            console.log("AJAX出错了error_status:");
            console.log(status);
            console.log("AJAX出错了error_e:");
            console.log(e);
        },
        //完成请求后触发。即在success或error触发后触发
        complete: function (xhr, status)
        {
        	$.mobile.loading( "hide" );
            console.log("AJAX已完成_xhr");
            console.log(xhr);
            console.log("AJAX已完成_status:");
            console.log(status);
        },
    })
}


//页面逻辑============================================
$(function()
{
	 //关闭JQM的AJAX模式
	$.mobile.ajaxEnabled = false;  //禁止ajax跳转，设置data-ajax="false"无效时可以使用一下
	//开启全局AJAX
	AjaxDir.Ajax_全局设置();
})