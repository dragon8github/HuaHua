<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class UserCtrl
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
    
    //根据传入的id，找到用户的余额，然后根据
    public function get_根据用户id获取余额($uid)
    {
        //选择表
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf("openid = '%s' ",$uid);
        //发送语句
        $arr =  $this->Sql->field("balance")->where($where)->find();
        //返回余额
        return $arr["balance"];
    }
    
    public function Openid是否存在用户表中()
    {
        //用户表
        $this->Sql-> table = 'user';
        //条件语句
        $where = sprintf("openid = '%s' ",$this->Openid);
        //获取总数
        $count = $this->Sql->where($where)->getCount();
    
        if($count > 0)
        {
            return true;
        }
    
        return false;
    }

    public function Insert_新增用户($openid,$name,$pic)
    {
        //选择数据库
        $this->Sql->table = 'user';
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
    
    
    public function Is_如果用户存在过期的信息就归还金钱()
    {
        if($this->Openid != "oYNn6wg0qYDkqNVomc78AUctYfRM")
        {
            return false;      
        }
        
        //选择表
        $this->Sql->table = 'question';
        //sql语句
        $mysql = sprintf("
                                            SELECT 
                                            A.uid,B.type,A.shengyu_count,A.price,A.price_count,A.id
                                            ###sum(A.shengyu_count * A.price) as money
                                            FROM
                                            `question` AS A
                                            JOIN
                                            statements AS B
                                            ON 
                                            A.id = B.question_id
                                            where 
                                            UNIX_TIMESTAMP() > expire_time    #####必须是过期题目
                                            AND 
                                            B.type = '1'					 ########首先必须是“画主充值”类型
                                            AND
                                            B.flag = '1'					 ######## 必须是充值成功
                                            AND 
                                            A.flag = '0' 					  ### 必须还没有过期
                                            AND
                                            A.uid = '%s'
        ",$this->Openid);
        //发送语句
        $arr =  $this->Sql->query($mysql);    
        //拼接insery语句
         $Insertsql = 'INSERT INTO statements (`type`,flag,id,uid,happen_time,price,balance,question_id) values ';        
         //总金额
         $money = 0;       
         //获取用户余额
         $statements_balance =   $this-> get_根据用户id获取余额($this->Openid);                 
         //循环结果集
         for ($i=0;$i<count($arr);$i++)
         {           
                //题目id
                $question_id = $arr[$i]["id"];
               //单价
               $price = $arr[$i]["price"];
               //剩余红包数量
               $shengyu_count = $arr[$i]["shengyu_count"];
               //累加总金额
               $money = $money  + $price * $shengyu_count; 
               //余额流水
               $statements_balance = $statements_balance +  $price * $shengyu_count; 
               //拼接结果集
               $Insertsql .= sprintf("( '6', '1' ,'%s' ,'%s' ,'%s' ,'%s','%s',%s),",uniqid().rand(0,10000),$this->Openid,time(), $price * $shengyu_count, $statements_balance,$question_id);
         }        
       //去除最后一个逗号
       $Insertsql = substr($Insertsql, 0,-1);    
       //判断
        IF($money > 0)
        {
            //添加流水statements
            $this->Sql->table = 'statements';
            //重置
            $this->Sql->reset();
            //发送语句
            $this->Sql->send($Insertsql);
            
            
             //选择表question
             $this->Sql->table = 'question';
             //重置
             $this->Sql->reset();
             //数据结构
             $data["flag"] = "2";
             $data["price"] = '0';
             $data["prop"] = '0';
             $data["price_count"] = '0';
             $data["hongbao_count"] = '0';
             $data["shengyu_count"] = '0';
             //条件语句
             $where = sprintf(" uid = '%s' AND  UNIX_TIMESTAMP() > expire_time AND flag != '2' ",$this->Openid);
             //发送语句
             $this->Sql->where($where)->save($data);
                         
 
             
             

             //添加用户余额user
             $this->Sql->table = 'user';
             //重置
             $this->Sql->reset();
             //条件语句
             $where =sprintf(" openid = '%s' ",$this->Openid) ;
             //发送语句
             $this->Sql->where($where)->sum('balance',$money);

             
             //提示一下
             Lee::alert("你的题目过期了，这是你的退返金：￥".$money / 100);
        }
    }
    
    
    public function wx_转账接口($money)
    {
        $ko=new WX_INT();
        $XML =  $ko->Zhifu($money,$this->Openid);
        return $XML;
    }
     

    public function Ajax_获取用户余额()
    {
        //选择表
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf("openid = '%s' ",$this->Openid);
        //发送语句
        $arr =  $this->Sql->field("balance")->where($where)->find();
        //返回余额
        $balance = $arr["balance"];
        
        if($balance <= 0)
        {
            //AJAX接受的信息
            $arr = array('Msg' => '提现失败，您的余额不足以满足微信提现条件（金额 >=0）！' , 'Result' => '' , 'Status' => '失败' );
            //返回为json
            exit(json_encode($arr));
        }
        else
        {
            //...调用转账接口
            $XML = $this->wx_转账接口($balance);
            
            //解析XML
            $XMLOBJ = simplexml_load_string($XML, 'SimpleXMLElement', LIBXML_NOCDATA);
           
            IF($XMLOBJ->result_code == "SUCCESS")
            {
                $this->清空用户余额和插入流水表($balance);
            }
            else IF($XMLOBJ->result_code == "FAIL")
            {
                //获取微信错误信息
                $msg = $XMLOBJ->err_code_des; 
                //拼接数组
                $arr = array('Msg' => $msg , 'Result' => '' , 'Status' => '失败' );
                //返回为json
                exit(json_encode($arr));
            }
        }
        //返回成功json
        $arr = array('Msg' => '提现成功，请查看微信消息！' , 'Result' => '' , 'Status' => '成功' );
        //返回为json
        exit(json_encode($arr));
    }
    
    
    public function 清空用户余额和插入流水表($balance)
    {
        //选择表
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf("openid = '%s' ",$this->Openid);
        //数据结构
        $data["balance"] = "0";
        //发送语句
        $this->Sql->where($where)->save($data); 
        
        //选择表
        $this->Sql->table = 'statements';
        //重置
        $this->Sql->reset();
        //数据结构
        $data2["id"] = uniqid();
        $data2["type"] = "4";
        $data2["price"] = $balance;
        $data2["happen_time"] = time();
        $data2["uid"] = $this->Openid;
        $data2["flag"] = "1";
        $data2["balance"] = "0";
        //发送语句
        $this->Sql->add($data2);
    }
    
    
    public function get_获取用户资料()
    {
        //选择表
        $this->Sql->table = 'user';
         //条件语句
        $where = sprintf("openid = '%s' ",$this->Openid);
        //发送语句
        $arr =  $this->Sql->where($where)->find();
        //返回余额
        return $arr;
    }
	 public function get_根据不同的type获取不同的图片($type)
    {
        $Reuslt = $_SESSION["STATIC_ROOT"].'/Upload/HuaHua/20160518/573bc0fb84b6a.jpeg';
         
        switch ($type)
        {
            case 1:     //画主充值 
                $Reuslt = $_SESSION["STATIC_ROOT"].'/Upload/HuaHua/20160518/573bc0fb84b6a.jpeg';
                break;
            case 2:    //使用道具
                $Reuslt = $_SESSION["STATIC_ROOT"].'/Upload/HuaHua/20160518/573bc0fb84b6a.jpeg';
                break;
            case 3:   //道具收益
                $Reuslt = $_SESSION["STATIC_ROOT"].'/Upload/HuaHua/20160518/573bc0fb84b6a.jpeg';
                break;
            case 4:   //提现
                $Reuslt = $_SESSION["STATIC_ROOT"].'/Upload/HuaHua/20160518/573bc0fb84b6a.jpeg';
                break;
            case 5:   //猜中谜题
                $Reuslt = $_SESSION["STATIC_ROOT"].'/Upload/HuaHua/20160518/573bc0fb84b6a.jpeg';
                break;
            case 6:  //红包退回
                $Reuslt = $_SESSION["STATIC_ROOT"].'/Upload/HuaHua/20160518/573bc0fb84b6a.jpeg';
                break;
        }
        return  $Reuslt;
    }
    
    public function get_根据不同的type获取正负($type)
    {
        $Reuslt = "-";
        switch ($type)
        {
            case 1:
                $Reuslt = "画主充值 -";
                break;
            case 2:
                $Reuslt = "使用道具 -";
                break;
            case 3:
                $Reuslt = "道具收益 +";
                break;
            case 4:
                $Reuslt = "提现 -";
                break;
            case 5:
                $Reuslt = "猜中谜题 +";
                break;
            case 6:
                $Reuslt = "红包退回 +";
                break;
        }
        return  $Reuslt;
    }
    
    public function get_获取流水列表()
    {
        //选择表
        $this->Sql->table = 'statements';
        //查询语句
        $mysql = sprintf("
                                    SELECT A. * , B.wx_name, B.wx_litpic, IF( bid =  '%s', 3, 
                                    TYPE ) AS realtype
                                    FROM statements AS A
                                    JOIN user AS B ON uid = openid
                                    WHERE 
                                    A.type IN ( 2, 3, 4, 5, 6 ) 
                                    AND uid =  '%s' 
                                    AND A.flag =  '1'
                                    OR 
                                    bid = '%s' 
                                    AND A.flag =  '1'
                                    ORDER BY happen_time DESC 
                                 ",$this->Openid,$this->Openid,$this->Openid);
        
        
        //发送语句
        return $this->Sql->query($mysql); 
    }
    
}


//接受请求==================================

if(@$_POST["type"] == "UserYuE")
{
    $_UserCtrl = new UserCtrl();
    $_UserCtrl->Ajax_获取用户余额();
}

?>