<?php 
SESSION_START(); 

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                 //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Maidan.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                 //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库



//公共变量 开始==============================================
$Title = "看图猜成语";                                    //设置页面Title
$openid = $_SESSION["openid"];                //openid
$nickname = $_SESSION["nickname"];              //昵称
$headimgurl = $_SESSION["headimgurl"];        //头像
$_MaidanCtrl = new MaidanCtrl();


$_MaidanCtrl->SET_用户($openid, $nickname, $headimgurl);

//微信类 开始==============================================
$ko=new WX_INT();
$signPackage = $ko->GetSignPackage();   //获取分享接口 相关信息
//业务逻辑 开始=================================================




  

$px=isset($_GET['px'])?$_GET['px']:0;
//0 最新  1奖金最高 2最多人答
if($px==0)
{
$arr = $_MaidanCtrl->get_New();

}else if($px==1)
{
$arr = $_MaidanCtrl->get_money();
}else
{
$arr = $_MaidanCtrl->get_many();
}






?>


<?php 	include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php';?>
<script type="text/javascript">document.documentElement.style.fontSize ="50px"</script>
<?php
        CssLoader::LoadCss("Copy", "User.css");
?>

<style type="text/css">
.tab_li li{width:27%;float:left;text-align:center;line-height:48px}
.dds{margin:0 3%;color:99ff}
.dds a{color:#0099ff}
.dddssss{border-bottom:solid 2px #09F}
.tab_li{height:50px;background-color:#FFF}
.itemss{width:46%;float:left;height:250px;overflow:hidden;border:solid 1px #ccc;border-radius:5px;margin:2% 0 0 2%;background-color:#FFF}
.des_tittt{height:20px;line-height:20px;text-align:center;margin:5px 0 0 0;padding:0;color:red}
.des_tittt span{color:red}
.tittt{height:20px;line-height:20px;margin:0;padding:0;text-align:center;color:#999}
.bhnn{height:200px;overflow:hidden;margin:10px;border-radius:5px}
.dds a{display:block}
.bhnn{border:solid 1px #f0f0f0;
height:180px;}
#cotne{background-color:#CCC}
#dds1{background-color:#f0f0f0}
#dds2{background-color:#f0f0f0}
#dds3{background-color:#f0f0f0}
.uujj{color:red}
.good_ny{background-color:#3aa7ff;color:#fff;max-width:60%;border-color:#ddd;text-shadow:0 1px 0 #f3f3f3;border-radius:.3125em;font-weight:700;-moz-user-select:none;cursor:pointer;display:block;font-size:16px;margin:.5em 0;overflow:hidden;padding:.7em 1em;position:relative;text-align:center;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,.15);background-clip:padding-box;border-style:solid;border-width:1px;margin:0 auto;text-shadow:0 0 0 #000}

</style>
<body>
            <style type="text/css">
                  .ssyy i{display:inline-block;height:40px;text-indent:-999em;width:26px}
            </style>

            
            <div style="min-height: 638px;" id="cotne" class="content content-user-index">
			<ul class="tab_li"><li class="dds <?php if($px==0) echo "dddssss"; ?>"><a href="http://huahua.ncywjd.com/Home.php?p=Maidan&px=0">最新作品</a></li><li class="dds <?php if($px==1) echo "dddssss"; ?>"><a href="http://huahua.ncywjd.com/Home.php?p=Maidan&px=1">奖金最高</a></li><li class="dds <?php if($px==2) echo "dddssss"; ?>"><a href="http://huahua.ncywjd.com/Home.php?p=Maidan&px=2">最多人玩</a></li></ul>
                	<div id="dds1" class="navigate clearfix currr">
					<p style="text-align:center;color:#FF0000;margin:6px 0 0 0;padding:0">以下题目为小编推荐的有诚意的题目</p>
					<p style="text-align:center;color:#FF0000;margin:6px 0 0 0;padding:0">★根据提示来答题成功率提升500%★</p>
                           		 <?php 
                           		   for($i = 0;$i<count($arr);$i++) 
                           		   {
                           		       $url =  sprintf("http://huahua.ncywjd.com/Home.php?p=guess&q=".$arr[$i]["id"]);
                           		       $pic = $arr[$i]['question_pic'];
                           		       $des_date = date('Y-m-d H:i:s',$arr[$i]["release_time"]);
                           		       $answer =  $arr[$i]["answer"] ;
									    $count =  $arr[$i]["COUNT"] ;
										$price =  $arr[$i]["price"]/100;
										
                       		       ?>  
								  
								   
								  
                               		   <a class="itemss" href="<?php echo $url; ?>">
									 
                                        		<div  class="bhnn"><img class="jkkkk" src="<?php echo  $pic;  ?>" width="100%"  /></div>
												  <p class="des_tittt"><?php echo "猜中奖".$price."元";?></p>
                                                <p class="tittt"><?php echo "共".$count."人在玩";?></p>
                                    			
                                       </a> 
									  <?php if(($i+1)%6==0)
								   {
								   	echo "<p style='text-align:center;float:left;width:100%;margin:11px 0 0 0'><a class='good_ny' style='width:90%' >分享给好友大家一起玩</a><p><div style='clear:both'></div>";
								   }
								   ?>  
									  
									 
                               <?php 
                                    }
                               ?> 
                    </div>
					
					
            </div>
			<div style="height:36px;width:100%"></div>
			<div is="null" class="TabBar-tabBar-33b"><div is="null" class="TabBar-content-1Fe7 base-receptacle-1K2a"><a is="null" class="TabBar-link-1LoE TabBar-active-ritd" href="http://huahua.ncywjd.com/home.php?p=List"><span is="null" class="TabBar-icon-2cSh"><svg width="24" height="24" class="SVGIcon-icon-20S2" viewBox="0 0 24 24"><title></title><path d="M1 4.995C1 3.893 1.902 3 2.995 3h17.01C21.107 3 22 3.893 22 4.995v14.01C22 20.107 21.098 21 20.005 21H2.995C1.893 21 1 20.107 1 19.005V4.995zM4.5 8.25c0-.414.332-.75.753-.75h12.494c.416 0 .753.333.753.75 0 .414-.332.75-.753.75H5.253c-.416 0-.753-.333-.753-.75zm0 3.5c0-.414.332-.75.753-.75h12.494c.416 0 .753.333.753.75 0 .414-.332.75-.753.75H5.253c-.416 0-.753-.333-.753-.75zm0 3.5c0-.414.333-.75.752-.75h8.496c.415 0 .752.333.752.75 0 .414-.333.75-.752.75H5.252c-.415 0-.752-.333-.752-.75z" fill-rule="evenodd"/></svg></span><span is="null" class="TabBar-name-SMTF">去画题</span></a><a is="null" class="TabBar-link-1LoE" href="http://huahua.ncywjd.com/home.php?p=User"><span is="null" class="TabBar-icon-2cSh"><svg width="24" height="24" class="SVGIcon-icon-20S2" viewBox="0 0 24 24"><title></title><path d="M11.5 23C17.3 23 22 18.3 22 12.5S17.3 2 11.5 2 1 6.7 1 12.5 5.7 23 11.5 23zm1.768-8.732c3.017-3.017 4.773-6.54 3.89-7.425-.885-.884-4.444.907-7.426 3.89-3.017 3.016-4.773 6.54-3.89 7.424.885.884 4.41-.872 7.426-3.89zm-2.83-.707c.587.588 1.537.588 2.123 0 .588-.583.588-1.533 0-2.12-.583-.584-1.533-.584-2.12 0-.584.587-.584 1.537 0 2.12z" fill-rule="evenodd"/></svg></span><span is="null" class="TabBar-name-SMTF">去提现</span></a></div></div>
<style type="text/css">
.TabBar-content-1Fe7{
	background-color: hsla(0, 0%, 100%, 0.96);
    bottom: 0;
    box-shadow: 0 -1px 0 0 rgba(0, 0, 0, 0.05);
	border-top:#ccc 1px solid;
    box-sizing: border-box;
    height: 50px;
    position: fixed;
    width: 100%;
    z-index: 2;
}
.TabBar-content-1Fe7 > a{
	width:50%;
	text-align:center;
	display:block;
	float:left;
}
.TabBar-content-1Fe7 > a span{
	line-height:20px;
	color: rgba(103, 103, 139, 0.6);
    font-size: 10px;
	display:block;
	margin-top:3px;
}
.TabBar-content-1Fe7 > a span:nth-of-type(2){
	margin-top:1px;
}
.TabBar-content-1Fe7 > a span svg{
	fill: currentcolor;
    margin: 0;
}
/*
.TabBar-tabBar-33b {
    background-color: hsla(0, 0%, 100%, 0.96);
    bottom: 0;
    box-shadow: 0 -1px 0 0 rgba(0, 0, 0, 0.05);
    box-sizing: border-box;
    height: 50px;
    position: fixed;
    width: 100%;
    z-index: 2;
}

.TabBar-content-1Fe7, .TabBar-link-1LoE {
    display: flex;
    height: inherit;
}
.base-receptacle-1K2a {
    margin: 0 auto;
    max-width: 600px;
}

.TabBar-active-ritd {
    color: #1185fe;
}
.TabBar-link-1LoE {
    align-items: center;
    color: rgba(103, 103, 139, 0.6);
    flex: 1 1 0;
    flex-direction: column;
    font-size: 10px;
    justify-content: center;
    text-decoration: none;
}
.TabBar-content-1Fe7, .TabBar-link-1LoE {
    display: flex;
    height: inherit;
}

.TabBar-icon-2cSh {
    align-items: center;
    border: 1px solid transparent;
    border-radius: 50%;
    display: flex;
    height: 22px;
    padding: 1px;
    width: 22px;
}

.TabBar-tabBar-33b svg {
    fill: currentcolor;
    margin: 0;
}
.SVGIcon-icon-20S2 {
    margin-right: 5px;
}*/
</style>
	<div id="zhezhaocheng" style="background-color:#191919;display:none;position:absolute;left:0;top:0"><img width="100%" src="<?php echo $_SESSION["STATIC_ROOT"]?>/Img/83358PICrqB_1024.jpg" /></div>
</body>
</html>

<?php 
	JsLoader::Jquery();    //加载jquery
	JsLoader::weixin();   //加载微信官方JS
	JsLoader::LoadDirective('HuaHua', 'WeiXin.Directive.js');   //加载个人封装的微信JS指
	JsLoader::LoadDirective('HuaHua', 'UserHistory.Directive.js');
?>
<script type="text/javascript">
			$(document).ready(function()
			{
				//$(".itemss").height($(".itemss").width());
			
				$(".good_ny").click(function() {
		$("#zhezhaocheng").width($(document).width());
		$("#zhezhaocheng").height($(document).height());
		$("#zhezhaocheng").show();
		$('html, body').animate({scrollTop:0}, 'slow');
	})
	$("#zhezhaocheng").click(function() {
		$(this).hide();
	})
			
			
			
				
				
				
			})
			</script>
			<script>



wx.config
({
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp:'<?php echo $signPackage["timestamp"];?>',
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: ['onMenuShareAppMessage','onMenuShareTimeline','startRecord','stopRecord','uploadVoice','downloadVoice'
        // 所有要调用的 API 都要加到这个列表中
    ]
});

 wx.ready(function ()  
{
	var mylink = "<?php echo $_SESSION["STATIC_ROOT"].'/home.php?p=Maidan';?>";	
	var wxshare_title ="猜画有奖，动动脑防止老年痴呆";	
	var wxshare_imgUrl = "<?php echo $_SESSION["STATIC_ROOT"].'/Img/ic.jpg';?>";
	var des="猜画有奖，动动脑防止老年痴呆";	
	share(mylink,wxshare_title,wxshare_imgUrl,des);
});
</script>