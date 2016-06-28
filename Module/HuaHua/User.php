<?php 
SESSION_START(); 


//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                 //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/User.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                 //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库



//公共变量 开始==============================================
$_UserCtrl = new UserCtrl();
$Title = "用户中心";                                            //设置页面Title
$openid = $_SESSION["openid"];                       //openid
$nickname = $_SESSION["nickname"];              //昵称
$headimgurl = $_SESSION["headimgurl"];        //头像
$balance = 0;                                                   //余额



//业务逻辑 开始=================================================
$arr_ls = $_UserCtrl->get_获取流水列表();
 




// //判断openid是否存在用户表，没有的话先插入
// if(!$_UserCtrl->Openid是否存在用户表中())
// {
//     //新用户
//     $_UserCtrl->Insert_新增用户($openid,$nickname,$headimgurl);   //添加到数据库
// }
// else 
// {
//     //老用户    
//     $_UserCtrl->Is_如果用户存在过期的信息就归还金钱();
//     $arr =  $_UserCtrl->get_获取用户资料();
//     $balance = $arr["balance"];  //余额    
// }


  

$_UserCtrl->SET_用户($openid, $nickname, $headimgurl);

 

$_UserCtrl->Is_如果用户存在过期的信息就归还金钱();
$arr =  $_UserCtrl->get_获取用户资料();
$balance = $arr["balance"];  //余额 

 

/*
$Is_OkforBalance = false;
$myhour = date("G");
if($myhour  < 18 || $myhour > 9)
{
	$Is_OkforBalance = true;
}*/
 

?>


<?php 	include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php';?>


<script type="text/javascript">document.documentElement.style.fontSize ="50px"</script>

<?php 

CssLoader::LoadCss("Copy", "User.css");

?>

<body>
<style type="text/css">
.ssyy i{display:inline-block;height:40px;text-indent:-999em;width:26px}
.layermanim h3{margin:0}
.ui-loader-default{display:none}
.ui-mobile-viewport{border:none}
.ui-page{padding:0;margin:0;outline:0}
#sdfw3e{background-color:#3aa7ff;color:#fff;max-width:45%;border-color:#ddd;text-shadow:0 1px 0 #f3f3f3;border-radius:.3125em;font-weight:700;-moz-user-select:none;cursor:pointer;display:block;font-size:16px;margin:.5em 0;overflow:hidden;padding:.7em 1em;position:relative;text-align:center;text-overflow:ellipsis;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,.15);background-clip:padding-box;border-style:solid;border-width:1px;margin:0 auto;text-shadow:0 0 0 #000}
</style>
			
            
            <div style="min-height: 638px;" class="content content-user-index">
                <div class="banner">
                    <div class="account">
                        <img class="avatar" src="<?php echo $headimgurl ?>">
                        <div class="txt">
                            <span class="nick"> <?php echo $nickname ?></span>
                            <span class="tip">欢迎回来~</span>
                        </div>
						
                    </div>
                    <div class="stat clearfix" style="position:relative;padding: 30px 0 20px;">
					<span style="font-size:12px;position:absolute;top:60px;left:18px;color:#999999">微信规则限制最少需要1元才能提现</span>
					<span style="font-size:12px;position:absolute;top:74px;left:18px;color:#999999">平台将收取您收入的5%作为平台运营资金</span>
					<!--<span style="font-size:12px;position:absolute;top:55px;left:18px;color:#999999">提现时间为9:30～18:00</span> -->
                        <div class="yyee" style="position:relative;margin-top:10px" >余额: <?php echo $balance/100; ?></div> 
            			<div class="ssyy"><span id="laod" style="display: none;">30</span><span id="old_jine" style="display: none;">30</span><i style="background-position: 0px -120px;"></i><i style="background-position: 0px 0px;"></i></div>
                        <div class="xgmm"><a href="javascript:;" id="tixian">提现</a></div>        </div>
                </div>
                
                <div class="navigate clearfix"> 
                 
                   <?php 
                     for($i = 0;$i<count($arr_ls);$i++)
                     {
                         $img = $arr_ls[$i]["wx_litpic"];
                         $des_date = date('Y-m-d',$arr_ls[$i]["happen_time"]);
                         $des_date2 = date('H:i:s',$arr_ls[$i]["happen_time"]);
                         $zhengfu = $_UserCtrl->get_根据不同的type获取正负($arr_ls[$i]["realtype"]);
                         $jine = $arr_ls[$i]["price"];
                         
                         if($jine == 0)
                         {
                             continue;
                         }
                         
                 ?>
                       	   <a class="item" href="javascript:;">
                        		<div class="ff icon"><img src="<?php echo $img; ?>" width="100%" height="45px" /></div>
                    			<span class="des_tit"><?php echo $des_date; ?><span class="des_tit2"><?php echo $des_date2; ?></span></span>
                                <span class="tit"><?php echo $zhengfu; ?>  <span style="color:red;"><?php echo $jine / 100; ?>元</span> </span>
                            </a>
                 <?php 
                    }
                 ?>           
                </div>
            </div>
</body>
</html>

<?php 
	JsLoader::Jquery();    //加载jquery
	JsLoader::Jqm();       //加载jqm
	//JsLoader::Layer();     //加载layer
	JsLoader::Layermobile();
	JsLoader::weixin();   //加载微信官方JS
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
	JsLoader::LoadDirective('HuaHua', 'WeiXin.Directive.js');   //加载个人封装的微信JS指令
	JsLoader::LoadDirective('HuaHua', 'User.Directive.js');
?>

<script>

$(function()
{
	
	$("#nottixian").click(function(){
		alert("由于微信官网接口升级过程中出现了故障，导致我们没有办法充值给你们提现，微信客服反馈正在修复中，今天之内应该能好");
		return false;
	})
	
})

</script>
