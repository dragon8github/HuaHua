<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class UserHistoryCtrl
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
    
    public function get_获取用户余额()
    {
        //选择表
        $this->Sql->table = 'user';
         //条件语句
        $where = sprintf("openid = '%s' ",$this->Openid);
        //发送语句
        $arr =  $this->Sql->field("balance")->where($where)->find();
        //返回余额
        return $arr["balance"];
        
    }
    
    public function get_获取分享列表()
    {
        //选择表
        $this->Sql->table = 'question';
        //条件语句
        $mysql = sprintf("SELECT a.id,question_pic,b.answer,a.release_time FROM question AS a JOIN question_library AS b ON a.answer = b.id WHERE a.uid =  '%s'",$this->Openid) ;
         
        //发送语句
        return $this->Sql->query($mysql);
    }
    
}


//接受请求==================================

?>