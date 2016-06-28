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
    

    public function Update_更新用户($openid,$name,$pic)
    {
        //选择数据库
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf("openid = '%s' ",$this->Openid);
        //数组对象
        $data['wx_name'] = $name;                               //微信名称
        $data['wx_litpic'] = $pic;                                  //微信头像
        //插入数据库
        $this->Sql->where($where)->save($data);
    }    

    public function Insert_新增用户($openid,$name,$pic)
    {
        //选择数据库
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
        //数组对象
        $data['openid'] = $openid;                                  //微信号
        $data['wx_name'] = $name;                               //微信名称
        $data['wx_litpic'] = $pic;                                  //微信头像
        $data['balance'] = '';                                        //账户余额
        $data['register_time'] = time();                        //注册时间
        $data['update_time'] = time();                         //刷新冷却时间
        $data['question'] = '';                                         //历史题库
        //插入数据库
        $this->Sql->add($data);
    }
    
    public function SET_用户($myopenid,$name,$pic)
    {
        //用户表
        $this->Sql-> table = 'user';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf("openid = '%s' ",$this->Openid);
        //发送语句
        $ret = $this->Sql->field("openid,wx_litpic")->where($where)->find();
        //获取结果
        $wx_litpic = $ret["wx_litpic"];  //微信头像（推广用户默认为空）
        $openid = $ret["openid"];       //openid(推广用户不为空)
    
        //如果为新用户
        IF($openid == "")
        {
            $this-> Insert_新增用户($myopenid,$name,$pic);
        }
        //如果为推广用户
        else if($openid != "" && $wx_litpic == "")
        {
            $this-> Update_更新用户($myopenid,$name,$pic);
        }
    }
    
    public  function get_money()
    {
        //选择表
        $this->Sql->table = 'question';
        //重置
        $this->Sql->reset();
        //条件语句 //  `model` is null
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
                                          AND 
                                                           `model`  = 1
                                        ORDER BY
                                        		            price 
                                        DESC
            
                                        LIMIT 30
            
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
                                        AND 
                                                           `model` = 1
                                        ORDER BY
                                        		            release_time
                                        DESC
                        
                                        LIMIT 30
                                  ",$this->Openid) ;
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    public function get_many()
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
                                        AND
                                                           `model` = 1
                                        ORDER BY
                                        		            COUNT
                                        DESC
                        
                                        LIMIT 30
    
                                  ",$this->Openid) ;
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    public function get_根据userid取用户资料($uid)
    {
        $this->Sql->table = "user";
        $this->Sql->reset();
        $where = sprintf(" openid = %s ",$uid); 
        return $this->Sql->field("wx_name,wx_litpic")->where($where)->find();
    }
    
    public function get_排行()
    {
        $this->Sql->table = "statements";
        $this->Sql->reset();
        $mysql = sprintf("
                                       SELECT A.wx_name, A.wx_litpic, B . * 
                                        FROM (
                                        SELECT uid, COUNT( * ) AS daticishu
                                        FROM statements
                                        WHERE TYPE IN ( 5, 7 ) 
                                        AND flag =  '1'
                                        GROUP BY uid
                                        ORDER BY COUNT( * ) DESC 
                                        LIMIT 20
                                        ) AS B
                                        JOIN user AS A ON uid = openid
                                        LIMIT 0 , 20
                             ");
        $retr = $this->Sql->query($mysql);        
        
        $datiuid = $retr[0]["uid"];               //uid
        $dati_arr = $this->get_根据userid取用户资料($datiuid);
        $dati_wx_name = $dati_arr["wx_name"];   //微信名
        $dati_wx_litpic = $dati_arr["wx_litpic"];      //头像
        $daticishu = $retr[0]["daticishu"];   //答题次数
        
        $mysql2 = sprintf("
                                        SELECT A.wx_name, A.wx_litpic, B . * 
                                        FROM (
                                        SELECT uid, COUNT( * ) AS chuticishu
                                        FROM statements
                                        WHERE TYPE = '1'
                                        AND flag =  '1'
                                        GROUP BY uid
                                        ORDER BY COUNT( * ) DESC 
                                        LIMIT 20
                                        ) AS B
                                        JOIN user AS A ON uid = openid
                                        LIMIT 0 , 20
                             ");
        $retr2 = $this->Sql->query($mysql2);        
        $chutiuid = $retr2[0]["uid"];                           //uid
        $chuti_arr = $this->get_根据userid取用户资料($chutiuid);
        $chuti_wx_name = $chuti_arr["wx_name"];   //微信名
        $chuti_wx_litpic = $chuti_arr["wx_litpic"];      //头像
        $chuticishu = $retr2[0]["chuticishu"];            //答题次数
        
        
        
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