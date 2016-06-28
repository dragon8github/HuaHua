<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class ChannelLoginCtrl
{
    private  $Sql;	          //该sql类的全局对象
    
    private $Openid;      //用户微信号
    
    function __construct()
    {
        //引入核心sql类库
        include $_SESSION["APP_ROOT"].'/Lib/Class/Mysql.class.php';
    
        //引入数据库配置
        $dsn = include $_SESSION["APP_ROOT"].'/Lib/Config/Sql.config.php';
        	
        	
        //返回数据库对象
        $this->Sql =  Mysql::start($dsn);
    }
    
    function login($username,$password,$week)
    {
        $this->Sql->table = "channel";
        $where = sprintf(" channel_user = '%s' ",$username);
        $ret = $this->Sql->find("channel_pwd,id")->find();
        $channel_pwd = $ret["channel_pwd"];
        $id = $ret["id"];  //根据Id来划分模块，引导用户前往自己的地址
        if($channel_pwd != "")
        {
            if($ret == $password)
            {
                AJAX("登录成功", "www.baidu.com", "成功");
            }
            else
            {
                AJAX("密码错误", "", "失败");
            }
        }
        else
        {
            AJAX("账号不存在", "", "失败");
        }
    }
}


IF(@$_POST["type"] == "login")
{
    $username = POST("username");
    $password = POST("password");
    $week = POST("week");
    
    $_ChannelLoginCtrl = new ChannelLoginCtrl();
    $_ChannelLoginCtrl ->login($username, $password, $week);
}


?>