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


 

//判断openid是否存在用户表，没有的话先插入
if(!$_UserCtrl->Openid是否存在用户表中())
{
    //新用户
    $_UserCtrl->Insert_新增用户($openid,$nickname,$headimgurl);   //添加到数据库
}
else 
{
    //老用户
    $arr =  $_UserCtrl->get_获取用户资料();
    $balance = $arr["balance"];  //余额
    $_UserCtrl->Is_如果用户存在过期的信息就归还金钱();
}




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

            <footer class="footer-navigate">
              <a class="item active" href="javascript:;">
                    <span class="ff icon"></span>
                    <span class="title">用户中心</span>
                </a>
                <a class="item " href="UserList.php">
                    <span class="ff icon"></span>
                    <span class="title">收益流水</span>
                </a>
                <a class="item " href="UserHistory.php">
                    <span class="ff icon"></span>
                    <span class="title">历史列表</span>
                </a>
            </footer>
            
            <div style="min-height: 638px;" class="content content-user-index">
                <div class="banner">
                    <div class="account">
                        <img class="avatar" src="<?php echo $headimgurl ?>">
                        <div class="txt">
                            <span class="nick"> <?php echo $nickname ?></span>
                            <span class="tip">欢迎回来~</span>
                        </div>
                    </div>
                    <div class="stat clearfix">
                        <div class="yyee">余额: <?php echo $balance/100; ?></div> 
            			<div class="ssyy"><span id="laod" style="display: none;">30</span><span id="old_jine" style="display: none;">30</span><i style="background-position: 0px -120px;"></i><i style="background-position: 0px 0px;"></i></div>
                        <div class="xgmm"><a href="javascript:;" id="tixian">提现</a></div>        </div>
                </div>
                
                <div class="navigate clearfix">
                 
                    <a class="item" href="UserHistory.php">
                        <div class="ff icon"></div>
            			<span class="des_tit">分享链接历史</span>
                        <span class="tit">分享列表</span>
                    </a>
            
                    <a class="item" href="UserList.php">
                        <div class="ff icon"></div>
            			<span class="des_tit">所有收益详情</span>
                        <span class="tit">收益流水</span>
            			
                    </a>
                    
                    <a class="item" href="javascript:;">
                        <div class="ff icon"></div>
            			<span class="des_tit">所有提现详情</span>
                        <span class="tit">提现记录</span>
            		
                    </a>
                    
                    <a class="item" href="javascript:;">
                        <div class="ff icon"></div>
            			<span class="des_tit">关注方便下次进入</span>
                        <span class="tit">关注微信</span>
                    </a>
                </div>
            </div>
</body>
</html>

<?php 
	JsLoader::Jquery();    //加载jquery
	JsLoader::Jqm();       //加载jqm
	JsLoader::Layer();     //加载layer
	JsLoader::weixin();   //加载微信官方JS
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
	JsLoader::LoadDirective('HuaHua', 'WeiXin.Directive.js');   //加载个人封装的微信JS指令
	JsLoader::LoadDirective('HuaHua', 'User.Directive.js');
?>
