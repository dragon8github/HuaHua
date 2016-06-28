<?php
//session_start();
//setcookie("user",time());
/*******************数据库资料********/
 header("Content-type: text/html; charset=utf-8");
 $data2 = json_decode(file_get_contents("Module/HuaHua/access_token.json"));
            $access_token = $data2->access_token;




//由页面初始化安装生成的用于以后访问数据库的文件
$con_server="127.0.0.1";
$con_name="root";
$con_password="asfdWf12312";
$database_name="huahua";
$con = mysql_connect($con_server,$con_name,$con_password);
mysql_select_db($database_name, $con);
mysql_query("set names utf8");
/*******************数据库资料********/


//define your token  
define("TOKEN", "kobo0824");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();


$GLOBALS['access_token'] = $access_token;



$wechatObj->responseMsg();
class wechatCallbackapiTest
{
    public function valid()
   {
      $echoStr = $_GET["echostr"];

      //valid signature , option
      if($this->checkSignature()){
         echo $echoStr;
         exit;
      }
   }

   public  function https_post($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
   public function downloadImageFromWeiXin($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        return array_merge(array('body' => $package), array('header' => $httpinfo));
    }
   public function responseMsg()
   {
      //get post data, May be due to the different environments
      $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
       $access_token=$GLOBALS['access_token'];
      //extract post data
      if (!empty($postStr)){

         $postObj = simplexml_load_string($postStr,
             'SimpleXMLElement', LIBXML_NOCDATA);
         $fromUsername = $postObj->FromUserName;
         $toUsername = $postObj->ToUserName;
         $keyword = trim($postObj->Content);
         $MsgType= $postObj->MsgType;
         $PicUrl= $postObj->PicUrl;
         $Event= $postObj->Event;
         $EventKey= $postObj->EventKey;

         $time = time();

         $textTpl = "<xml>
                               <ToUserName><![CDATA[%s]]></ToUserName>
                               <FromUserName><![CDATA[%s]]></FromUserName>
                               <CreateTime>%s</CreateTime>
                               <MsgType><![CDATA[%s]]></MsgType>
                               <Content><![CDATA[%s]]></Content>
                               <FuncFlag>0</FuncFlag>
                               </xml>";
         $tssst="";

							if($MsgType=='text')
									 {
										
												
												  $contentStr = "大家一起来玩吧\r\n猜画有奖\r\n<a href='http://t.cn/R5Hvm7U'>http://t.cn/R5Hvm7U</a>\r\n点链接开始玩";
													 $msgType = "text";
												  $resultStr = sprintf($textTpl, $fromUsername,
											$toUsername, $time, $msgType, $contentStr);
										echo $resultStr;
										
									}

        //通过扫描关注的  将为场景值+钱  并且为新用户 创建基础数据
						  if($MsgType=="event" and $Event=="subscribe"){
				
							  $contentStr="大家一起来玩吧\r\n猜画有奖\r\n<a href='http://t.cn/R5Hvm7U'>http://t.cn/R5Hvm7U</a>\r\n点链接开始玩";
							  $result = mysql_query("select * from `user` where openid='$fromUsername'");
							  $row= mysql_num_rows($result);
								// $time=time();
											  if(!$row)
											  {
												  $qrscene=substr($EventKey,8);
												  //为推荐人 加上金钱
												  $qrscene=(int)($qrscene);
												  if($qrscene){
												  
												   //先判断用户当天获取的总金额
													
											   
												  mysql_query("insert into `user` (openid,balance,register_time,channel) values('$fromUsername',0,'$time',$qrscene)");
												   
												  $contentStr="大家一起来玩吧\r\n猜画有奖\r\n<a href='http://t.cn/R5Hvm7U'>http://t.cn/R5Hvm7U</a>\r\n点链接开始玩";
												 }
												   
											}
									
					
					
									
									  $textTpl="<xml>
												   <ToUserName><![CDATA[%s]]></ToUserName>
												   <FromUserName><![CDATA[%s]]></FromUserName>
												   <CreateTime>%s</CreateTime>
												   <MsgType><![CDATA[%s]]></MsgType>
												   <Content><![CDATA[%s]]></Content>
												   <FuncFlag>0</FuncFlag>
												   </xml>";
					
									 
									  //触发打开相册
									  $msgType = "text";
					
									  $resultStr = sprintf($textTpl, $fromUsername,
										  $toUsername, $time, $msgType, $contentStr);
									  echo $resultStr;
									  return;
							  }

          }
            //关注事件  关注生成用户数据(用户专属二维码)

   }

   private function checkSignature()
   {
      $signature = $_GET["signature"];
      $timestamp = $_GET["timestamp"];
      $nonce = $_GET["nonce"];

      $token = TOKEN;
      $tmpArr = array($token, $timestamp, $nonce);
      sort($tmpArr);
      $tmpStr = implode( $tmpArr );
      $tmpStr = sha1( $tmpStr );

      if( $tmpStr == $signature ){
         return true;
      }else{
         return false;
      }
   }
}



?> 