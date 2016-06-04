// 注意：所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
// 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
// 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html





function share(mylink,title,imgUrl,des)
{
	// 在这里调用 API
    wx.onMenuShareAppMessage({
        title: title || '精彩活动入口', // 分享标题：猜画有奖，李钊鸿 画了一副成语画给你，大家一起来玩吧！
        desc: des, // 分享描述
        link: mylink, // 分享链接
        imgUrl: imgUrl || 'http://www.nhcskx.com/nnnnnn.jpg', // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });

    
    wx.onMenuShareTimeline
    ({
        title: title || '精彩活动入口', // 分享标题：猜画有奖，李钊鸿 画了一副成语画给你，大家一起来玩吧！
        link:mylink, 			// 分享链接
        imgUrl: imgUrl || 'http://www.nhcskx.com/nnnnnn.jpg', // 分享图标
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
}

//调用微信JS api 支付
function jsApiCall(json,mydata,callback)
{
		WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				json,
				function(res)
				{
					//alert(res.err_code+res.err_desc+res.err_msg);
					callback(res,mydata); 
				}
			);
}

//参数1：微信支付的json，
//参数2：回调函数所需要的参数
//参数3：回调函数
function callpay(json,mydata,callback)
{
	if(typeof json == "string")
	{
		json = JSON.parse(json);
	}
	
	if (typeof WeixinJSBridge == "undefined")
	{
	    if( document.addEventListener )
	    {
	        document.addEventListener('WeixinJSBridgeReady', function(){jsApiCall(json,mydata,callback)}, false);
	    }
	    else if (document.attachEvent)
	    {
	        document.attachEvent('WeixinJSBridgeReady', function(){jsApiCall(json,mydata,callback)}); 
	        document.attachEvent('onWeixinJSBridgeReady', function(){jsApiCall(json,mydata,callback)});
	    }
	}
	else
	{
		jsApiCall(json,mydata,callback);
	}
}