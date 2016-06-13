<?php 
SESSION_START(); 

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                 //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Maidan.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                 //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库



//公共变量 开始==============================================
$Title = "分享历史";                                    //设置页面Title
$openid = $_SESSION["openid"];                //openid
$_MaidanCtrl = new MaidanCtrl();


//业务逻辑 开始=================================================

$arr = $_MaidanCtrl->get_获取分享列表();



?>


<?php 	include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php';?>
<script type="text/javascript">document.documentElement.style.fontSize ="50px"</script>
<?php
        CssLoader::LoadCss("Copy", "User.css");
?>
<body>
            <style type="text/css">
                  .ssyy i{display:inline-block;height:40px;text-indent:-999em;width:26px}
            </style>

            
            <div style="min-height: 638px;" class="content content-user-index">
                	<div class="navigate clearfix">
                           		 <?php 
                           		   for($i = 0;$i<count($arr);$i++)
                           		   {
                           		       $url =  sprintf("http://huahua.ncywjd.com/Home.php?p=guess&q=".$arr[$i]["id"]);
                           		       $pic = $arr[$i]['question_pic'];
                           		       $des_date = date('Y-m-d H:i:s',$arr[$i]["release_time"]);
                           		       $answer =  $arr[$i]["answer"] ;
                       		       ?>  
                               		   <a class="item" href="<?php echo $url; ?>">
                                        		<div class="ff icon"><img src="<?php echo  $pic;  ?>" width="100%" height="45px" /></div>
                                    			<span class="des_tit"><?php echo $des_date;?></span>
                                                <span class="tit"><?php echo $answer;?></span>
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
	
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
	JsLoader::LoadDirective('HuaHua', 'UserHistory.Directive.js');
?>