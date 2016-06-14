<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class MaidanCtrl
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
    
    
    
    public  function get_money()
    {
        //选择表
        $this->Sql->table = 'question';
        //重置
        $this->Sql->reset();
        //条件语句
        $mysql = sprintf("            
                                    SELECT 
                                    		                * 
                                    FROM 
                                         		           question AS A
                                  LEFT JOIN 
                                        		           (SELECT question_id, COUNT(*) AS COUNT FROM visitor GROUP BY  question_id) AS C 
                                        ON 
                                        		            A.id = C.question_id
                                        WHERE
                                        	 	            flag <> '2' 
                                         AND 
                                     	 	                expire_time > UNIX_TIMESTAMP() 
                                        AND
                                                            price > 0
                                          AND
                                                            shengyu_count > 0
                                        ORDER BY
                                        		            price 
                                        DESC
                                  ",$this->Openid) ;
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    public function get_New()
    {
        //选择表
        $this->Sql->table = 'question';
        //重置
        $this->Sql->reset();
        //条件语句
        $mysql = sprintf("
                                        SELECT 
                                    		               * 
                                    FROM 
                                        		            question AS A
                                    LEFT  JOIN 
                                        		            (SELECT question_id, COUNT(*) AS COUNT FROM visitor GROUP BY  question_id) AS C 
                                        ON 
                                        		            A.id = C.question_id
                                        WHERE
                                        	 	            flag <> '2'
                                         AND
                                     	 	                expire_time > UNIX_TIMESTAMP()
                                         AND
                                                            price > 0
                                        AND
                                                            shengyu_count > 0
                                        ORDER BY
                                        		            release_time
                                        DESC
            
                                  ",$this->Openid) ;
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    public function get_获取答题花销比例()
    {
        $this->Sql->table = "setting";
        $this->Sql->reset();
        $dd=$this->Sql->where("id=1")->select();
        $prop =  @$dd[0]["model_prop"];
        return $prop;
    }
    
}


//接受请求==================================

?>