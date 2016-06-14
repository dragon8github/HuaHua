<?php  
SESSION_START();
$_SESSION["APP_ROOT"] = dirname(__FILE__);
$_SESSION["STATIC_ROOT"] = "http://".dirname($_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF']);




//测试人员的调试模式
if(@$_GET["model"] == "test")
{ 
    $_SESSION["openid"] = "oYNn6wg0qYDkqNVomc78AUctYfRM";
    $_SESSION["nickname"] = "李钊鸿";      //昵称
    $_SESSION["headimgurl"] = "http://wx.qlogo.cn/mmopen/ficCQMgzCd1j85R9jDHHZ5pg"; //头像
    //$Wxurl = "http://huahua.ncywjd.com/Module/HuaHua/Draw.php?q=1&word=金蝉脱壳";
     $Wxurl = "http://huahua.ncywjd.com/Module/HuaHua/Guess.php?q=578";
    //$Wxurl = "http://huahua.ncywjd.com/Module/HuaHua/UserList.php"; 
    //$Wxurl = "http://huahua.ncywjd.com/Module/HuaHua/List.php"; 
    //$Wxurl = "http://huahua.ncywjd.com/Module/HuaHua/User.php";
    header("Location:".$Wxurl);  
    exit();
}   

if(@$_GET["model"] == "admin")
{ 
    $_SESSION["openid"] = "oYNn6wg0qYDkqNVomc78AUctYfRM";
    $_SESSION["nickname"] = "李钊鸿";      //昵称
    $_SESSION["headimgurl"] = "http://wx.qlogo.cn/mmopen/ficCQMgzCd1j85R9jDHHZ5pg"; //头像
    header("Location:"."http://huahua.ncywjd.com/Admin_5114405E07BC4D23A61B28D9E8BAFD57");
    exit();
}


$p = @$_GET["p"];           //页面名称，如list,user,draw,guess
$q = "";                            //参数名称，如q=1
//遍历除了p参数以外所有的参数（但事实上，微信只接受一个参数，后续解决这个问题）
foreach($_GET as $key => $value)
{
    //初代开发者的约定，如果有需要可以替换掉键名p
    if($key != "p")
    {
            if($q == "")
            {
                //说明是第一个参数
                $q .=  sprintf("?%s=%s",$key,$value);
            }
            else 
            {
                $q .=  sprintf("&%s=%s",$key,$value);
            }
    }
}

 
if($p != null)
{
    $state = rand(0,9999);  //随机数解决缓存问题；
    $appid = "wx92ea69e479013e3d";
    $url= sprintf("http://huahua.ncywjd.com/Module/HuaHua/%s.php%s",$p,$q);
    $Wxurl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
    header("Location:".$Wxurl);
}
    
?>