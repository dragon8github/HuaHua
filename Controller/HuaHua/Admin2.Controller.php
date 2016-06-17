<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class Admin2Ctrl
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
    
    public function get_所有信息()
    {
        //选择表
        $this->Sql->table = 'question';
        
        $DESC = "mysum";
        
        $orderby = @$_GET["orderby"];

        IF(@$orderby == "yonghuyue")
        {
            $DESC = "balance";
        }
        ELSE  IF(@$orderby == "yonghutixianzonge")
        {
            $DESC = "mysum";
        }
        ELSE  IF(@$orderby == "chuticishu")
        {
            $DESC = "myquestion_cont";
        }
        ELSE  IF(@$orderby == "daticishu")
        {
            $DESC = "myanswer_count";
        } 
         
        
        //条件语句
        $mysql = sprintf("
                                    SELECT 
                                    	           A.openid,A.wx_name,A.wx_litpic,A.balance,mysum,myanswer_count,myquestion_cont
                                     FROM 
                                                    user AS A
                                LEFT JOIN 
                                     	          (SELECT uid,sum(price) as mysum FROM statements where type = '4' group by uid) AS B
                                          ON
                                     	          A.openid = B.uid
                                LEFT JOIN
                                     	          (SELECT uid,count(*) as myanswer_count from statements where type in (7,5) and flag = '1' group by uid ) AS C
                                          ON
                                     	          A.openid = C.uid	
                                LEFT JOIN
                                     	          (SELECT uid,count(*) as myquestion_cont from question group by uid) AS D
                                          ON
                                     	          A.openid = D.uid
                                    WHERE
                                    	           mysum > 0            
                                
                               ORDER BY                  
                                                    %s  DESC                                      
                                     LIMIT 
                                                    0,30
                            ",$DESC);  
        
        
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    
   
}


//接受请求==================================

?>