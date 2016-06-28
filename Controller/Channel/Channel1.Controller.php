<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class Channel1_Ctrl
{
    private  $Sql;	          //该sql类的全局对象
    
    private $Openid;      //用户微信号
    
    function __construct()
    {
        //引入核心sql类库
        include $_SESSION["APP_ROOT"].'/Lib/Class/Mysql.class.php';
    
        //引入数据库配置
        $dsn = include $_SESSION["APP_ROOT"].'/Lib/Config/Sql.config.php';
        	
        //Openid
        $this->Openid = $_SESSION["openid"];
        	
        //返回数据库对象
        $this->Sql =  Mysql::start($dsn);
    }

    public function get_今天的信息()
    {
        //选择表
        $this->Sql->table = "user";
        //自定义语句
        $mysql = "SELECT count(*) AS count,sum(balance) AS balance from `user` where DATE_FORMAT( FROM_UNIXTIME( `register_time` ) , '%Y-%m-%d' ) = DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) AND channel = '0'";
        //获取今天的数据(引流人数，发生总金额)
        $rett = $this->Sql->query($mysql);
        //获取人数
        $count = $rett[0]["count"];
        //获取总金额
        $balance = $rett[0]["balance"];        
        
        return array("count" => $count,"balance"=>$balance);
    }
    
    public function get_昨天的信息()
    {
        //选择表
        $this->Sql->table = "user";
        //自定义语句
        $mysql = "SELECT sum(balance) AS balance,count(*) AS count from `user` where DATEDIFF(now() , FROM_UNIXTIME(register_time)) = 1 AND channel = '0'";
        //获取今天的数据(引流人数，发生总金额)
        $rett = $this->Sql->query($mysql);
        //获取人数
        $count = $rett[0]["count"];
        //获取总金额
        $balance = $rett[0]["balance"];
    
        return array("count" => $count,"balance"=>$balance);
    }
    
    public function get_所有的信息()
    {
        //选择表
        $this->Sql->table = "user";
        //自定义语句
        $mysql = "SELECT sum(balance) AS balance,count(*) AS count from `user` where channel = '0'";
        //获取今天的数据(引流人数，发生总金额)
        $rett = $this->Sql->query($mysql);
        //获取人数
        $count = $rett[0]["count"];
        //获取总金额
        $balance = $rett[0]["balance"];
    
        return array("count" => $count,"balance"=>$balance);
    }
    
  
    
   
}


?>