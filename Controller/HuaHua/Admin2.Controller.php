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

         
        
        //条件语句,A.balance AS user_balance
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
                                                    0,100
                            ",$DESC);  
        
        
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    public function get_获取真实正确的需要提现的数据($openid)
    {
        //选择表
        $this->Sql->table = 'statements';
        //条件语句
        $mysql = sprintf("
                                    SELECT 
                            					sum(price) / 100 as user_balance  
                            		FROM 
                            					statements
                            		where 
                            					happen_time >(SELECT happen_time from statements where type = '4' and uid = '%s' order by happen_time desc limit 1)
                            			AND
                            					flag = '1'
                            			AND
                            					(
                                                        uid = '%s' AND (type = '5' or type = '6' or type = '8')
                                    			OR
                                    					bid = '%s' AND type = '7'
                                                )         
                            ",$openid,$openid,$openid);
        
        //发送语句
        $rett=  $this->Sql->query($mysql);
        
        return $rett[0]["user_balance"];
    }
    
    
    public function get_今天昨天总共的提现情况()
    {
        //选择表
        $this->Sql->table = 'statements';
        //条件语句
        $mysql =" 
                                    SELECT IFNULL(sum(price),0)  FROM `statements` WHERE DATE_FORMAT( FROM_UNIXTIME( `happen_time` ) , '%Y-%m-%d' ) = DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) and type = '4'
                                    UNION ALL
                                    SELECT IFNULL(sum(price),0)  FROM statements WHERE DATE_FORMAT( FROM_UNIXTIME( `happen_time` ) , '%Y-%m-%d' ) = UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) and type = '4'
                                    UNION ALL
                                    SELECT IFNULL(sum(price),0)  FROM statements where type = '4'
                       ";
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    public function get_今天昨天总共的答题情况()
    {
        //选择表
        $this->Sql->table = 'statements';
        //条件语句
        $mysql = "
                                SELECT count(*) FROM statements where (type = '7' or type = '5' or type = '8') and DATE_FORMAT( FROM_UNIXTIME( `happen_time` ) , '%Y-%m-%d' ) = DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) 
                                UNION ALL
                                SELECT count(*) FROM statements where (type = '7' or type = '5' or type = '8') and DATE_FORMAT( FROM_UNIXTIME( `happen_time` ) , '%Y-%m-%d' ) = UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) 
                                UNION ALL
                                SELECT count(*) FROM statements where (type = '7' or type = '5' or type = '8') 
                        ";
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    public function get_今天昨天总共的出题情况()
    {
        //选择表
        $this->Sql->table = 'statements';
        //条件语句
        $mysql = "SELECT count(*) FROM statements where type = '1'  and DATE_FORMAT( FROM_UNIXTIME( `happen_time` ) , '%Y-%m-%d' ) = DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) and flag = '1' 
                         UNION ALL
                         SELECT count(*) FROM statements where type = '1'  and DATE_FORMAT( FROM_UNIXTIME( `happen_time` ) , '%Y-%m-%d' ) =UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY))  and flag = '1' 
                         UNION ALL
                         SELECT count(*) FROM statements where type = '1'  and flag = '1' 
                       ";
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    
   
}


//接受请求==================================

?>