<?php 
SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                 //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                 //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Draw.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                 //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库





//公共变量 开始==============================================
$_DrawCtrl = new DrawCtrl();                            //Main对象
$openid = $_SESSION["openid"];                       //openid
$q = $_GET["q"];                                               //answer题目号码
$Title =$_GET["word"];                                      //设置页面Title

 

//判断画主是否重新打开这个界面
$arr = $_DrawCtrl->If_判断是否画主重新打开这个页面($q, $openid);
$expire_time = $arr["expire_time"];                 //过期时间
$shengyu_count = $arr["shengyu_count"];      //剩余红包数量
$price = $arr['price'];                                      //单价
$flag = 0;                                                      //能否修改的标识
$nickname = $_SESSION["nickname"];  

//微信类 开始==============================================
$ko=new WX_INT();
$signPackage = $ko->GetSignPackage();   //获取分享接口 相关信息

  

//逻辑开始==================================================
if(count($arr))
{
    //用户回来修改数据、但允许修改的是满足其中一个前提：
    //..1、当前时间大于过期时间
    //..2、红包剩余金额为0
    if(time() > $expire_time || $shengyu_count == 0)
    {
        $flag = 1;
    }
    else
    {
        $flag = 0;
    }
}

?>


<?php 	include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php';?>

<?php 
       CssLoader::Jqm();      //加载jqm.css
 ?>
 <style type="text/css">
 #KaiShiZhizuo{background-color:#2ED146;color:#fff;max-width:45%;border-color:#ddd;text-shadow:0 1px 0 #f3f3f3;border-radius:.3125em;font-weight:700;-moz-user-select:none;cursor:pointer;display:block;font-size:16px;margin:.5em 0;overflow:hidden;padding:.7em 1em;position:relative;text-align:center;text-overflow:ellipsis;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,.15);background-clip:padding-box;border-style:solid;border-width:1px;margin:0 auto;text-shadow:0 0 0 #000;text-decoration:none}
#k_h1{height:26px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/h1.png);margin:0 auto;background-repeat:no-repeat;padding:5px 5px 5px 35px;background-position:5px center;float:left}
#k_h2{height:26px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/h2.png);margin:0 auto;background-repeat:no-repeat;padding:5px 5px 5px 35px;background-position:5px center;float:left}
#k_h3{height:26px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/h3.png);margin:0 auto;background-repeat:no-repeat;padding:5px 5px 5px 15px;background-position:5px center;float:left}
#k_h4{height:26px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/h4.png);margin:0 auto;background-repeat:no-repeat;padding:5px 5px 5px 21px;background-position:5px center;float:left}
#k_h5{height:26px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/h5.png);margin:0 auto;background-repeat:no-repeat;padding:5px 5px 5px 27px;background-position:5px center;float:left}
.cjjssll{background-color:#FFF;border:solid 1px #DBDBEA;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px}
.cjjssll22{background-color:#FFF;border:solid 1px #DBDBEA;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px}
#k_c1{width:40px;height:40px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/c1.png);margin:0 auto;background-repeat:no-repeat;padding:5px;background-position:center}
#k_c2{width:40px;height:40px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/c2.png);margin:0 auto;background-repeat:no-repeat;padding:5px;background-position:center}
#k_c3{width:40px;height:40px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/c3.png);margin:0 auto;background-repeat:no-repeat;padding:5px;background-position:center}
#k_c4{width:40px;height:40px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/c4.png);margin:0 auto;background-repeat:no-repeat;padding:5px;background-position:center}
#k_c5{width:40px;height:40px;background-image:url(<?php echo $_SESSION["STATIC_ROOT"];?>/Img/c5.png);margin:0 auto;background-repeat:no-repeat;padding:5px;background-position:center}
.fl_left{float:left;height:38px}
.fl_left:nth-of-type(1){width:25%}
.fl_left:nth-of-type(2){width:25%}
.fl_left:nth-of-type(3){width:15%}
.fl_left:nth-of-type(4){width:15%}
.fl_left:nth-of-type(5){width:20%}
.fl_left_n{float:left;width:20%;height:50px}
#gongju{background-color:#f0f0f0}
#k_color{padding:10px 0}
#k_hua{border-bottom:dashed 1px #DBDBEA;padding:10px 0}
.ui-content{padding:1px}
#controlgroup .ui-controlgroup-controls{display:block} 
 </style>
	<body>
        	<div data-role="page" style="min-height: 667px;background: #f0f0f0;">
        	  	<!--<div data-role="header" class="ui-shadow" data-theme = 'a'>
        	  	<a role="button" data-role="button"   data-rel="back" class="ui-btn-left ui-alt-icon ui-nodisc-icon ui-btn ui-icon-carat-l ui-btn-icon-notext ui-corner-all">Back</a>	
        	        <h1><?php echo $Title ?></h1>
        	    </div>--><!-- /header -->
        	  
        	    <div role="main" class="ui-content">
        	    		<div id="gongju" >
									<div id='k_hua'>
										<div class="fl_left"><div id="k_h1" >清屏</div></div>
										<div class="fl_left"><div id="k_h2"  val='8'>橡皮</div></div>
										<div class="fl_left"><div id="k_h3" val="2" >细</div></div>
										<div class="fl_left"><div id="k_h4" val="4" class="cjjssll current" >中</div></div>
										<div class="fl_left"><div id="k_h5" val="6" >粗</div></div>
											<div style="clear:both"></div>
									</div>
									<div id='k_color'>
									<div class="fl_left_n"><div id="k_c1" ></div></div>
									<div class="fl_left_n"><div id="k_c2"  ></div></div>
									<div class="fl_left_n"><div id="k_c3" ></div></div>
									<div class="fl_left_n"><div id="k_c4" ></div></div>
									<div class="fl_left_n"><div id="k_c5" class="cjjssll22" ></div></div>														
								    <div style="clear:both"></div>
									</div>

                        			<div class="ui-grid-solo" style="position:relative;">
                            									<div id="replace_images">
                                    								<canvas id="canvas"  style="border:dashed 1px #333333;width:99.8%; "></canvas>
                                    							</div>
																<p style="font-size:12px;color:#FF0000;text-align:center;margin:8px 0 0 0;padding:0">画得有诚意些，才会有人愿意作答</p>
                                            					<a href="#" data-role="none" id="KaiShiZhizuo" style="margin-top:8px;">生成作品</a>
                        			</div>
        	            </div>
        	    </div><!-- /content -->
        	</div><!-- /page -->	
    	</body>
    </html>
		
<?php 
	JsLoader::Jquery();    //加载jquery
	JsLoader::Jqm();       //加载jqm
	JsLoader::Layer();     //加载layer
	JsLoader::weixin();   //加载微信官方JS
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');        //加载全局AJAX指令
	JsLoader::LoadDirective('HuaHua', 'WeiXin.Directive.js');   //加载个人封装的微信JS指令
	JsLoader::LoadDirective('HuaHua', 'Draw.Directive.js');     //加载本页核心指令
?>

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
	var mylink = "<?php echo $_SESSION["STATIC_ROOT"].'/home.php?p=list';?>";	
	var wxshare_title = "猜画有奖，<?php echo $nickname ?>邀请您一起来玩";	
	var wxshare_imgUrl = "<?php echo $_SESSION["STATIC_ROOT"].'/Img/ic.jpg';?>";
	var des="猜画有奖，乐在其中";	
	share(mylink,wxshare_title,wxshare_imgUrl,des);
});

	$(document).ready(function()
	{
			//点击其他按钮需要修改N
			//橡皮擦
		  $("#k_h2") .click(function() 
		  {
		  		$(".cjjssll22").removeClass("cjjssll22");
				$(".cjjssll").removeClass("cjjssll");
		  	//$("#k_h4").addClass("cjjssll");
		$(this).addClass("cjjssll");
				 // alert('huabu'); //line_color = "#ffffff";
		 		 line_color = "#ffffff";  $(".current").removeClass("current"); $(this).addClass("current");  
		  })
		  
		    //清楚画布
	   $("#k_h1").click(function() 
		{  
		//$(".cjjssll22").removeClass("cjjssll22");
		//$(".cjjssll").removeClass("cjjssll");
		//$("#k_h4").addClass("cjjssll");
		//$("#k_c2").addClass("cjjssll22");
		//$(this).addClass("cjjssll");
		var ctx = $("#canvas")[0].getContext("2d"); 
		//修改清除画布 
		ctx.fillStyle="ffffff";//白色为例子；

   　ctx.fillRect(0, 0, $("#canvas")[0].width, $("#canvas")[0].height);

		
		//ctx.clearRect(0, 0, $("#canvas")[0].width, $("#canvas")[0].height); 
	
		 })
		//最小线
		$("#k_h3").click(function()
		{
			if(!$(".cjjssll22").length)
			{
			$("#k_c5").addClass("cjjssll22");
			line_color = "#33cc66";
			}
			$(".cjjssll").removeClass("cjjssll");
		$(this).addClass("cjjssll");
			// line_color = "#ff0000";
			 $(".current").removeClass("current"); $(this).addClass("current");
		})
			$("#k_h4").click(function()
		{
		if(!$(".cjjssll22").length)
			{
			$("#k_c5").addClass("cjjssll22");
			line_color = "#33cc66";
			}
			$(".cjjssll").removeClass("cjjssll");
		$(this).addClass("cjjssll");
			// line_color = "#ff0000";
			 $(".current").removeClass("current"); $(this).addClass("current"); 
		})
			$("#k_h5").click(function()
		{
		if(!$(".cjjssll22").length)
			{
			$("#k_c5").addClass("cjjssll22");
			line_color = "#33cc66";
			}
			$(".cjjssll").removeClass("cjjssll");
		$(this).addClass("cjjssll");
			// line_color = "#ff0000";
			 $(".current").removeClass("current"); $(this).addClass("current"); 
		})
		$("#k_c1").click(function()
		{
			var ztz=$(".cjjssll").text();
			if(ztz=="橡皮"||ztz=="清屏")
			{
				$(".cjjssll").removeClass("cjjssll");
				$("#k_h4").addClass("cjjssll");
				 $(".current").removeClass("current");
				$("#k_h4").addClass("current");
			}
			$(".cjjssll22").removeClass("cjjssll22");
			$(this).addClass("cjjssll22");
			 line_color = "#000000"; 
		})
		$("#k_c2").click(function()
		{
		var ztz=$(".cjjssll").text();
			if(ztz=="橡皮"||ztz=="清屏")
			{
				$(".cjjssll").removeClass("cjjssll");
				$("#k_h4").addClass("cjjssll");
				 $(".current").removeClass("current");
				$("#k_h4").addClass("current");
			}
		$(".cjjssll22").removeClass("cjjssll22");
			$(this).addClass("cjjssll22");
			 line_color = "#cc0000";
		})
		$("#k_c3").click(function()
		{
		var ztz=$(".cjjssll").text();
			if(ztz=="橡皮"||ztz=="清屏")
			{
				$(".cjjssll").removeClass("cjjssll");
				$("#k_h4").addClass("cjjssll");
				 $(".current").removeClass("current");
				$("#k_h4").addClass("current");
			}
		$(".cjjssll22").removeClass("cjjssll22");
			$(this).addClass("cjjssll22");
			 line_color = "#ffcc00"; 
		})
		$("#k_c4").click(function()
		{
		var ztz=$(".cjjssll").text();
			if(ztz=="橡皮"||ztz=="清屏")
			{
				$(".cjjssll").removeClass("cjjssll");
				$("#k_h4").addClass("cjjssll");
				 $(".current").removeClass("current");
				$("#k_h4").addClass("current");
			}
		$(".cjjssll22").removeClass("cjjssll22");
			$(this).addClass("cjjssll22");
			 line_color = "#0099ff"; 
		})
		$("#k_c5").click(function()
		{
		var ztz=$(".cjjssll").text();
			if(ztz=="橡皮"||ztz=="清屏")
			{
				$(".cjjssll").removeClass("cjjssll");
				$("#k_h4").addClass("cjjssll");
				 $(".current").removeClass("current");
				$("#k_h4").addClass("current");
			}
		$(".cjjssll22").removeClass("cjjssll22");
			$(this).addClass("cjjssll22");
			 line_color = "#33cc66"; 
		})
	
	})
</script>