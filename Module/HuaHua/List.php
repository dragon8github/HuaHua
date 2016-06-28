<?php 
SESSION_START(); 

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                             //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                 //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/List.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                              //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                //加载JS组件库
$ko=new WX_INT();
$signPackage = $ko->GetSignPackage();   //获取分享接口 相关


//公共变量 开始==============================================

$Title = "选题";                                          //设置页面Title
$openid = $_SESSION["openid"];               //openid
$_ListCtrl = new ListCtrl();                         //核心控制器
$arr = array();                                           //Main列表
$LengQueTime = -60;                               //默认冷却时间
$nickname = $_SESSION["nickname"];      //昵称
$headimgurl = $_SESSION["headimgurl"]; //头像

//页面逻辑 开始==============================================

if(!$_ListCtrl->Openid是否存在用户表中())
{
        //...新用户        
        $arr =   $_ListCtrl->get_获取随机的十条历史记录和这十条记录的id();   //获取随机的十条数据以及偷偷带上的question 
        $ids = Lee::get_获取数组中指定键的值按照逗号隔开返回($arr, "id");     //获取这十条数据的id
        $_ListCtrl-> SET_用户($ids,$openid,$nickname,$headimgurl);  
       // $_ListCtrl->Insert_新增用户($ids,$nickname,$headimgurl);                //添加到数据库      
        $LengQueTime = -$arr[0]["LengQueTime"];                                     //冷却时间
}  
else
{
        //...旧用户
        $arr = $_ListCtrl-> Get_返回历史数据如果历史数据为空则返回随机数据并且更新到用户资料中();
        $LengQueTime = $arr["LengQueTime"];         //返回剩余冷却时间
        array_pop($arr);                                              //出栈（将暗渡陈仓的LengQueTime抛出核心列表）
        //Lee::alert("距离冷却时间：".$LengQueTime);
        
}

?>


<?php 	include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php';?>


<style type="text/css">

#box{height:55px;position:relative;}
#refresh{text-decoration:none;background-color:#2ED146;color:#fff;max-width:45%;border-color:#ddd;text-shadow:0 1px 0 #f3f3f3;border-radius:.3125em;font-weight:700;-moz-user-select:none;cursor:pointer;display:block;font-size:16px;margin:.5em 0;overflow:hidden;padding:.7em 1em;position:relative;text-align:center;text-overflow:ellipsis;white-space:nowrap;background-clip:padding-box;border-style:solid;border-width:1px;margin:0 auto;text-shadow:0 0 0 #000}

</style>

<?php 
       CssLoader::Jqm();      //加载jqm.css
 ?>
	
<html>
	<body>
    	<div data-role="page">
        	    <div role="main" class="ui-content">

	                 <div id="box" style="text-align:center">
                            <a id="refresh"   href="#" >刷新题库</a>     
							<span style="font-size:12px;color:ff0000">总成语库约3000条，如不适用请刷新</span> 
                     </div>
        	    	<?php for ($i=0;$i<count($arr);$i++){ 	?>
        	    	          <a href="Draw.php?q=<?php echo $arr[$i]["id"]; ?>&word=<?php echo $arr[$i]["answer"]; ?>" class="ui-btn ui-shadow"><?php echo $arr[$i]["answer"]; ?></a>
        	    	<?php  }  ?>	
        	    </div>
    	 </div>
 	</body>
</html>
    	 
    	 
<?php 
	JsLoader::Jquery();    //加载jquery
	JsLoader::Jqm();       //加载jqm
		JsLoader::weixin();   //加载微信官方J
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
		JsLoader::LoadDirective('HuaHua', 'WeiXin.Directive.js');   //加载个人封装的微信JS指
	JsLoader::LoadDirective('HuaHua', 'List.Directive.js');
?>
<script type="text/javascript">
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

//价值（）元，快来猜，手快有手慢无
wx.ready(function ()  
{
	var mylink = "<?php echo $_SESSION["STATIC_ROOT"].'/home.php?p=list';?>";	
	var wxshare_title = "猜画有奖，<?php echo $nickname ?>邀请您一起来玩";	
	var wxshare_imgUrl = "<?php echo $_SESSION["STATIC_ROOT"].'/Img/ic.jpg';?>";
	var des="猜画有奖，乐在其中";	
	share(mylink,wxshare_title,wxshare_imgUrl,des);
});

</script>
<script>


var time = <?php echo $LengQueTime ?>;

if(time < 0)
{
	//冷却时间
	var abstime = Math.abs(time);
	//开始倒计时
	showtime(abstime);
	$("#refresh").text(abstime+"秒后可刷新");
	//执行倒计时循环

		var aa=setInterval(function()
		{	
			if(abstime!=1)
			{
	

			abstime=abstime-1;
			$("#refresh").text(abstime+"秒后可刷新");
			
			}else
			{
			$("#refresh").text("刷新题库");
			clearInterval(aa);
			}
		},1000);

}  
else
{

	//可以刷新题库
	$("#refresh").text("刷新题库");
	//...发送一个请求给后端 
	$("#refresh").bind("tap",Send);
}
</script>
