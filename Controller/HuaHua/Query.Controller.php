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
        //返回
        return $arr;
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
        //返回
        return $arr;
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
        return $arr;
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
        //返回
        return $arr;
    }
    
    
}


//接受请求==================================

IF(@$_GET["action"] == "query")
{
    $openid = $_POST["openid"];
        
    $_query = new QueryCtrl();
    $_query->get_获取answer_details($openid);
    $_query->get_获取question($openid);
    $_query->get_获取statements($openid);
    $_query->get_获取user($openid);
    
}


?>