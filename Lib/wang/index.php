<?php 
require_once "Wx_Class.php";
$ko=new WX_INT();
$code = $_GET["code"]; 
echo 'code:'.$code;
$json_obj=$ko->getOpenid($code);    //获取openid
$openid=$json_obj['openid'];
$acc_token=$json_obj['access_token'];

$refresh_token=$json_obj['refresh_token'];
echo '<br>openid:'.$openid;
echo '<br>refresh_token:'.$refresh_token;
echo  '<br>acc_token:'.$acc_token;
$signPackage = $ko->GetSignPackage();   //获取分享接口 相关信息
    //  echo $signPackage["appId"];
    //  echo $signPackage["timestamp"];
      //echo $signPackage["nonceStr"];
     // echo $signPackage["signature"];
	  
	   
	 $jsApiParameters=$ko->Jspay("test","test","10","http://huahua.ncywjd.com/notify.php",$openid);
	
	$user_message=$ko->GetWeixinMessage($openid,$acc_token);
	
	echo $user_message['nickname'];     //微信名称
	echo $user_message['headimgurl'];   //微信头像
	  
?>
<script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>
<script>
    // 注意：所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
    // 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
    // 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
    wx.config({
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp:'<?php echo $signPackage["timestamp"];?>',
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: ['onMenuShareAppMessage','onMenuShareTimeline','startRecord','stopRecord','uploadVoice','downloadVoice'
            // 所有要调用的 API 都要加到这个列表中
        ]
    });

    wx.ready(function () {
        // 在这里调用 API
        wx.onMenuShareAppMessage({
            title: '精彩活动入口', // 分享标题
            desc: '', // 分享描述
            link: 'http://dwz.cn/2Zo8es', // 分享链接
           imgUrl: 'http://www.nhcskx.com/nnnnnn.jpg', // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });

        
        wx.onMenuShareTimeline({

            title: '精彩活动入口', // 分享标题
            link:'http://dwz.cn/2Zo8es', // 分享链接
            imgUrl: 'http://www.nhcskx.com/nnnnnn.jpg', // 分享图标
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });

</script>
 <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	/*function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
				
				alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
		);
	}
	*/
	/*window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};*/
	
	</script>
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
						 
					   }
					});
			})
			
	})
	</script>
	
	<p>微信名:<?php echo $user_message['nickname'];?></p>
	<p><img src="<?php echo $user_message['headimgurl'] ?>" /></p>
	<div onclick="callpay()" style="width:200px;height:200px;border:solid 1px #000000">充值接口</div>
	
	
		<div id='zhifu' style="width:200px;height:200px;border:solid 1px #000000">支付接口</div>