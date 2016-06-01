<?php 
SESSION_START(); 

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                             //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                 //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/List.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                              //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                //加载JS组件库



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
        $_ListCtrl->Insert_新增用户($ids,$nickname,$headimgurl);                //添加到数据库
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

#box{height:55px;position:relative;display:none;}
#refresh{left:50%;position:absolute;top:0;}

</style>

<?php 
       CssLoader::Jqm();      //加载jqm.css
 ?>
	
<html>
	<body>
    	<div data-role="page">
        	    <div role="main" class="ui-content">

	                 <div id="box">
                            <a id="refresh"   href="#" class="ui-btn ui-icon-refresh ui-btn-icon-notext ui-corner-all">No text</a>      
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
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
	JsLoader::LoadDirective('HuaHua', 'List.Directive.js');
?>

<script>


var time = <?php echo $LengQueTime ?>;

if(time < 0)
{
	//冷却时间
	var abstime = Math.abs(time);
	//开始倒计时
	showtime(abstime);
}
else
{
	//可以刷新题库
	$("title").text("可以刷新题库");
	//显示刷新按钮
	$("#box").slideDown('slow');		
	//...发送一个请求给后端
	$("#refresh").bind("tap",Send);
}
</script>
