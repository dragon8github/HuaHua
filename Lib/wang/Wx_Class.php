<?php 
ini_set('date.timezone','Asia/Shanghai');
require_once    $_SESSION["APP_ROOT"]."/Lib/wang/jssdk.php";
require_once    $_SESSION["APP_ROOT"]."/Lib/wang/wxpay/lib/WxPay.Api.php";    //用户充值
require_once    $_SESSION["APP_ROOT"]."/Lib/wang/wxpay/example/WxPay.JsApiPay.php";//用户充值
require_once    $_SESSION["APP_ROOT"].'/Lib/wang/wxpay/example/log.php';
require_once    $_SESSION["APP_ROOT"]."/Lib/wang/wxpay/api_test.php";  //用户提现


class WX_INT  
{  

	public $appid = "wx911ae27f5e1197c3";  
	public $secret= "f9eeb3c923f9f700031b907f5f953a51";
	//服务号页面授权获取openid
	public function getOpenid($code) {
			$appid=$this->appid;  
			$secret=$this->secret;  
			$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 500);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_URL, $get_token_url);
			
			$res = curl_exec($curl);
			curl_close($curl);
			$json_obj = json_decode($res,true);  
			//获取openid
			$access_token = $json_obj['access_token'];  
			$openids = $json_obj['openid'];
			//$openids='11';
			return $json_obj;
				}  
				
	//分享接口
	public function GetSignPackage() 
	{
			$appid=$this->appid;  
			$secret=$this->secret;  
			$jssdk = new JSSDK($appid, $secret);
			$signPackage = $jssdk->GetSignPackage();
			return $signPackage;
	}  
				
				
				
   //获取用户头像 名称等信息
	public function GetWeixinMessage($openids,$access_token) {
			
			$url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openids."&lang=zh_CN";
			$curl = curl_init();
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 500);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_URL, $url);
			
			$res = curl_exec($curl);
			curl_close($curl);
			$json_obj = json_decode($res,true); 
			return $json_obj;
				}  
				
	public function Jspay($body,$attach,$total_fee,$Notify_url,$openId,$orderid)
	{	
		$tools = new JsApiPay();
		//$openId = $this->getOpenid($code);
		$input = new WxPayUnifiedOrder();
		$input->SetBody($body);
		$input->SetAttach($attach);
		$input->SetOut_trade_no($orderid);
		$input->SetTotal_fee($total_fee);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url($Notify_url);  //"http://huahua.ncywjd.com/wxpay/example/notify.php"
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = WxPayApi::unifiedOrder($input);
		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		//printf_info($order);
		//var_dump($order);
		//exit();
		
		$jsApiParameters = $tools->GetJsApiParameters($order);
		
		return $jsApiParameters;
	
	}
	
	public function Zhifu($amount,$openid)
	{
			//echo "<br/>"."-----".$openids."*****";
			$mch_appid=$this->appid;
			$mchid='1332747501';//商户号
			$nonce_str='qyzf'.rand(100000, 999999);//随机数
			$partner_trade_no='HW'.time().rand(10000, 99999);//商户订单号
			$openid=$openid;//用户唯一标识
			$check_name='NO_CHECK';//校验用户姓名选项，NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
			$re_user_name='不知道';//用户姓名
			//$amount=300;//金额（以分为单位，必须大于100）
			$desc='提现';//描述
			$spbill_create_ip=$_SERVER["REMOTE_ADDR"];//请求ip
			//封装成数据
			$dataArr=array();
			$dataArr['amount']=$amount;
			$dataArr['check_name']=$check_name;
			$dataArr['desc']=$desc;
			$dataArr['mch_appid']=$mch_appid;
			$dataArr['mchid']=$mchid;
			$dataArr['nonce_str']=$nonce_str;
			$dataArr['openid']=$openid;
			$dataArr['partner_trade_no']=$partner_trade_no;
			$dataArr['re_user_name']=$re_user_name;
			$dataArr['spbill_create_ip']=$spbill_create_ip;
			
			$sign=getSign($dataArr);
			$data="<xml>
					<mch_appid>".$mch_appid."</mch_appid>
					<mchid>".$mchid."</mchid>
					<nonce_str>".$nonce_str."</nonce_str>
					<partner_trade_no>".$partner_trade_no."</partner_trade_no>
					<openid>".$openid."</openid>
					<check_name>".$check_name."</check_name>
					<re_user_name>".$re_user_name."</re_user_name>
					<amount>".$amount."</amount>
					<desc>".$desc."</desc>
					<spbill_create_ip>".$spbill_create_ip."</spbill_create_ip>
					<sign>".$sign."</sign>
					</xml>";
					$ch = curl_init ();
					$MENU_URL="https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
					curl_setopt ( $ch, CURLOPT_URL, $MENU_URL );
					curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
					curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
					curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
					
					$zs1= $_SESSION["APP_ROOT"]."/Lib/wang/wxpay/cert/apiclient_cert.pem";
                    $zs2= $_SESSION["APP_ROOT"]."/Lib/wang/wxpay/cert/apiclient_key.pem";
					curl_setopt($ch,CURLOPT_SSLCERT,$zs1);
					curl_setopt($ch,CURLOPT_SSLKEY,$zs2);
					// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01;
					// Windows NT 5.0)');
					curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
					curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
					curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
					curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true);
					
					$info = curl_exec ( $ch );
					
					/*
					if (curl_errno ( $ch )) {
						return 'Errno' . curl_error ( $ch );
					}
					*/
					
					curl_close ( $ch );
					
					return $info;
	}
}


//直接使用==================================================

$code = @$_GET["code"];

if(@$code != null && @$_SESSION["openid"] == null)
{
    //实例化
    $ko=new WX_INT();
    //获取json
    $json_obj=$ko->getOpenid($code);
    //获取openid
    $openid = $json_obj['openid'];
    //获取access_token
    $acc_token=$json_obj['access_token'];
    //调用接口获取用户信息
    $user_message=$ko->GetWeixinMessage($openid,$acc_token);
    //微信名称
    $_SESSION["nickname"] =  $user_message['nickname'];
    //微信头像
    $_SESSION["headimgurl"] = substr($user_message['headimgurl'], 0,-1)."64"; 
	
//	$_SESSION['subscribe']=$user_message['subscribe'];
    //openid
    $_SESSION["openid"] = $openid;
    //code
    $_SESSION["code"] = $code;
} 
 
 
 
//  IF($_SESSION["openid"] != "oYNn6wg0qYDkqNVomc78AUctYfRM" && $_SESSION["openid"] != "oYNn6wi2Lg4qvvQDOFFTMXpY6ulY" &&  $_SESSION["openid"] != "oYNn6wq36uud61x_9U3jSDpTN0bo")
 //{
  //    exit("<style type=\"text/css\">#face{margin:0px auto;background: #9ee675;/* for Webkit */background: -webkit-gradient(linear, left top, left bottom, from(#9ee675), to(#78cb4c));/* for Firefox */background: -moz-linear-gradient(top,  #9ee675,  #78cb4c);/* for IE */filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#9ee675',endColorstr='#78cb4c'); color:#fff;border:1px solid #fff;border-radius:200px;text-align:center;width:200px;height:200px;font-size:126px;letter-spacing: 5px;padding-top: 5px;}  *{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: \"微软雅黑\"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 3.8em;text-align: center; font-size: 36px }</style><div style=\"padding: 24px 48px;\"> <h1 id=\"face\">:(</h1><p style='font-size:66px'>程序正在升级中<br /><br />感谢您对我们的支持</p></div>");
 // }  
  

?>