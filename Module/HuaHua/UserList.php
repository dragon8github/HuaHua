<?php 
SESSION_START(); 

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                 //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/UserList.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                 //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库



//公共变量 开始==============================================
$_UserListCtrl = new UserListCtrl();
$Title = "收益流水";                                    //设置页面Title
$openid = $_SESSION["openid"];                //openid
 


//业务逻辑 开始=================================================
$arr = $_UserListCtrl->get_获取流水列表();
?>


<?php 	include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php';?>


<script type="text/javascript">document.documentElement.style.fontSize ="50px"</script>

<?php CssLoader::LoadCss("Copy", "User.css") ?>

<body>
            <style type="text/css">
                  .ssyy i{display:inline-block;height:40px;text-indent:-999em;width:26px}
            </style>

            <footer class="footer-navigate">
              <a class="item " href="User.php">
                    <span class="ff icon"></span>
                    <span class="title">用户中心</span>
                </a>
                <a class="item active" href="javascript:;">
                    <span class="ff icon"></span>
                    <span class="title">收益流水</span>
                </a>
                <a class="item " href="UserHistory.php">
                    <span class="ff icon"></span>
                    <span class="title">历史列表</span>
                </a>
            </footer>
            
            <div style="min-height: 638px;" class="content content-user-index">
                <div class="navigate clearfix">
                 <?php 
                     for($i = 0;$i<count($arr);$i++)
                     {
                         $img = $_UserListCtrl->get_根据不同的type获取不同的图片($arr[$i]["realtype"]);
                         $des_date = date('Y-m-d',$arr[$i]["happen_time"]);
                         $zhengfu = $_UserListCtrl->get_根据不同的type获取正负($arr[$i]["realtype"]);
                         $jine = $arr[$i]["price"];
                 ?>
                       	   <a class="item" href="javascript:;">
                        		<div class="ff icon"><img src="<?php echo $img; ?>" width="100%" height="45px" /></div>
                    			<span class="des_tit"><?php echo $des_date; ?></span>
                                <span class="tit"><?php echo $zhengfu; ?> ￥ <?php echo $jine; ?></span>
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
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
	JsLoader::LoadDirective('HuaHua', 'UserList.Directive.js');
?>