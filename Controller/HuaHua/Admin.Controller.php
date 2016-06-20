<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class AdminCtrl
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
	
		$now_time=time();
        //选择表
        $this->Sql->table = 'question';
        //条件语句
        $mysql = sprintf("
                                SELECT 
                                                mysum,
                                                C.answer,                                                
                                                D.openid,
                                                D.wx_name,
                                                D.wx_litpic,
                                                B.question_pic,
                                                B.release_time,
                                                B.model, 
                                                (B.shengyu_count * B.price) AS shengyujine,
                                                B.id AS AID,
                                                D.balance AS money_balance

                                FROM 
                                                  statements  AS A
                              LEFT  JOIN
                                                 (SELECT uid,sum(price) as mysum FROM statements where type = '4' GROUP BY uid) AS SUM
                                 on
                                        A.uid = SUM.uid                        
                                JOIN 
                                        question AS B 
                                ON 
                                        A.question_id = B.id
                                JOIN 
                                        question_library AS C 
                                ON 
                                        B.answer = C.id
                                JOIN 
                                        `user` AS D 
                                ON 
                                        D.openid = B.uid
                                WHERE 
								
									B.expire_time>'$now_time'
									AND
                                        A.flag = '1' 
                                AND 
                                        A.type = '1'
                               AND 
                                        B.shengyu_count * B.price!= 0
                          ORDER BY 
                            ");  
        
       $desc = "B.release_time   DESC";
      
       if(@$_GET["orderby"] == "descmoney")
       {
           $desc = "shengyujine DESC";
       }
       elseif (@$_GET["orderby"] == "desctime")
       {
           $desc = "B.release_time DESC";
       }
       
       $mysql .= $desc;
        
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    
    public function update_更新model($question_id,$model)
    {
        //选择表
        $this->Sql->table = 'question';
        //更新表
        $data["model"] = $model;
        //条件语句
        $where =sprintf(" id = '%s'  ",$question_id);
        //更新语句
        $this->Sql->where($where)->save($data);
        //返回数据
        exit("更新成功");        
    }
    
}


//接受请求==================================



IF(@$_POST["type"] == "update")
{
    
    $question_id = $_POST["question_id"];
    
    $model = $_POST["model"];
    
    $_ctrl = new AdminCtrl();
    
    $_ctrl->update_更新model($question_id,$model);
}
?>