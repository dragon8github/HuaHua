<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class QueryCtrl
{
    private  $Sql;	          //该sql类的全局对象
    
    private $Openid;      //用户微信号
    
    public static   $answer_details_arr = array();
    
    public static  $question_arr = array();
    
    public static  $user_arr = array();
    
    public static  $statements_arr = array(); 
    
    public static $Tips_arr = array();
    
    function __construct()
    {
        
        //引入核心sql类库
        include $_SESSION["APP_ROOT"].'/Lib/Class/Mysql.class.php';
     
        
        //引入数据库配置
        $dsn = include $_SESSION["APP_ROOT"].'/Lib/Config/Sql.config.php';
                	
        //返回数据库对象
        $this->Sql =  Mysql::start($dsn); 
     
    }   
    
    
    
    
    public function get_获取用户生成的tips大全($openid)
    {
        //选择表
        $this->Sql->table = 'statements';
        //重置
        $this->Sql->reset();
        
        $mysql = sprintf("
                                        SELECT
                                        		      *
                                         FROM
                                         		     daojuinfo
                                         WHERE
                                     		         openid = '%s'
             order by id  desc
                                 ",$openid);
        //发送语句
        $arr =  $this->Sql->query($mysql);
        //赋值
        $this::$Tips_arr = $arr;
    }

    
    public function get_获取statements($openid)
    {
        //选择表 
        $this->Sql->table = 'statements';
        //重置
        $this->Sql->reset();
    
        $mysql = sprintf("
                                        SELECT
                                        		      *
                                         FROM
                                         		     statements
                                         WHERE
                                     		         bid = '%s'
                                         	OR
                                         		     uid = '%s' 
             order by self_id  desc  
                                 ",$openid,$openid);
        //发送语句
        $arr =  $this->Sql->query($mysql);
        //赋值
        $this::$statements_arr = $arr;
    }
    
    public function get_获取user($openid)
    {
        //选择表
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
    
        $mysql = sprintf("
                                        SELECT 
                                        		*
                                         FROM 
                                         		user
                                         WHERE
                                         		openid = '%s'
                                 order by id desc
                                 ",$openid);
        //发送语句
        $arr =  $this->Sql->query($mysql);
        //返回余额
        $this::$user_arr = $arr;                
    }
    
    public function get_获取question($openid)
    {
        //选择表
        $this->Sql->table = 'question';
        //重置
        $this->Sql->reset();
    
        $mysql = sprintf("
                                SELECT 
                                		      A.*,B.answer AS answer1
                                 FROM 
                                 		     question AS A
                                 JOIN
                                 		     question_library AS B
                                   ON
                       		                 A.answer = B.id
                                 WHERE	
                                 		     A.uid = '%s'
                                order by 
                                            A.id 
                                    desc
                                 ",$openid);
        //发送语句
        $arr =  $this->Sql->query($mysql);
        //返回余额
        $this::$question_arr = $arr;  
    }
    
    public function get_获取answer_details($openid)
    {
        //选择表
        $this->Sql->table = 'answer_details';
        //重置
        $this->Sql->reset();
    
        $mysql = sprintf("
                                        SELECT 
                                        		      *
                                         FROM 
                                         		     answer_details
                                         WHERE
                                         		     user_id = '%s'
                                        order by 
                                                     id 
                                            desc
                                 ",$openid);
                
        //发送语句
        $arr =  $this->Sql->query($mysql);
        //返回余额
        $this::$answer_details_arr = $arr;    
    }
    
    public   function  update_balance($openid,$money)
    {
        //选择表
        $this->Sql->table = 'user';
        
        //重置
        $this->Sql->reset();
        
        $data["balance"] = $money;
        
        $where = sprintf(" openid = '%s' ",$openid);
        
        $this->Sql->where($where)->save($data);
        
        exit();
    }
     
    public function get_自动完成搜索($q)
    {
        //选择表
        $this->Sql->table = 'user';
        //条件语句
        $where = " wx_name like '%".$q."%'   ";        
        //查询语句
        $rett = $this->Sql->where($where)->limit(10)->field("wx_name,wx_litpic,openid")->select();        
        //序列化返回
        exit(json_encode($rett));
    }
    
    
}


//接受请求==================================

IF(@$_GET["action"] == "query")
{
    $_query =  new QueryCtrl();
    $openid = $_POST["openid"];
    $_query->get_获取answer_details($openid);
    $_query->get_获取question($openid);
    $_query->get_获取statements($openid);
    $_query->get_获取user($openid);
    $_query->get_获取用户生成的tips大全($openid);
}


IF(@$_GET["type"] == "autoComplete")
{
    $_query = new QueryCtrl();
    $q = $_GET["q"];
    $_query->get_自动完成搜索($q);
}

IF(@$_POST["type"] == "update_balance")
{
    $_query = new QueryCtrl();
    $openid = $_POST["openid"];
    $balance = $_POST["balance"];
    $_query->update_balance($openid,$balance);
}

?>