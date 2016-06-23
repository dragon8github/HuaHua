<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class UserCtrl extends Lee
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
    

    public function get_获取真实正确的需要提现的数据()
    {
        //用户openid
        $openid = $this->Openid;        
        //选择表
        $this->Sql->table = 'statements';
        //重置sql
        $this->Sql->reset();
        //条件语句
        $mysql = sprintf("
                                    SELECT
                            					IFNULL(sum(price) / 100,0)  as user_balance
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

    
    public function get_获取setting的提现规则()
    {
        //选择表
        $this->Sql->table = 'setting';
        //重置
        $this->Sql->reset();
        //发送语句
        $arr =  $this->Sql->limit(1)->find();
        //返回余额
        return $arr["pending_balance"];
    }
    
    public function Is_是否存在提现申请列表中()
    {
        //选择表
        $this->Sql->table = 'statements';
        //重置
        $this->Sql->reset();        
        //条件语句
        $where = sprintf("uid = '%s' AND flag = '0' AND type = '4' ",$this->Openid);        
          //获取总数
        $count = $this->Sql->where($where)->getCount();
        //如果大于0返回true
        if($count > 0)
        {
            return true;
        }
        //否则返回false
        return false;
    }
    

    public function Ajax_提现()
    {
        $balance = $this->get_根据用户id获取余额($this->Openid);             
        
        if($balance < 100)
        {
            //AJAX接受的信息
            $arr = array('Msg' => '提现失败，您的余额不足以满足微信提现条件（金额 >=1）！' , 'Result' => '' , 'Status' => '失败' );
            //返回为json 
            exit(json_encode($arr));
        }      
       
       $pending_balance = $this->get_获取setting的提现规则();       
       
       IF($balance >= $pending_balance)
       {
           if(!$this->Is_是否存在提现申请列表中())
           {              
               //清空用户余额，并且插入一条flag 为 0 的数据
              $this->清空用户余额和插入流水表($balance,0);              
              //AJAX接受的信息
              $arr = array('Msg' => '提现申请成功,请等待工作人员审核。（工作时间9:30~18:00）' , 'Result' => '' , 'Status' => '成功' );
              //返回为json
              exit(json_encode($arr));
           }
           else 
           {
               //AJAX接受的信息
               $arr = array('Msg' => '你上一次的申请在队列中。请等待审核完毕' , 'Result' => '' , 'Status' => '失败' );
               //返回为json
               exit(json_encode($arr));
           }
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
    }
    
    
    public function Ajax_获取用户余额()
    {        
        $balance = $this->get_根据用户id获取余额($this->Openid);    
        
        if($balance < 100)
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
    
    
    public function 清空用户余额和插入流水表($balance,$flag = 1)
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
        $data2["flag"] = $flag;
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
                $Reuslt = "画主充值 <span style='color:red'>-</span>";
                break;
            case 2:
                $Reuslt = "使用道具 <span style='color:red'>-</span>";
                break;
            case 3:
                $Reuslt = "道具收益 <span style='color:red'>+</span>";
                break;
            case 4:
                $Reuslt = "提现 <span style='color:red'>-</span>";
                break;
            case 4.1:
                $Reuslt = "提现 <span style='color:red'>-</span>";
                break;
            case 5:
                $Reuslt = "猜中谜题 <span style='color:red'>+</span>";
                break;
            case 6:
                $Reuslt = "充值奖金退回 <span style='color:red'>+</span>";
                break;
            case 7:
                $Reuslt = "别人答题支付 <span style='color:red'>+</span>";
				break;
			case 8:
                $Reuslt = "猜题费用退回 <span style='color:red'>+</span>";
                break;
        }
        return  $Reuslt;
    }
    
    public function get_获取流水列表()
    {
        $openid = $this->Openid;
        
        //选择表
        $this->Sql->table = 'statements';
        
        //查询语句
        $mysql = sprintf("
                                    SELECT 
                                					A. * ,
                                					B.wx_name,
                                					B.wx_litpic, 
                                					IF( bid =  '%s' AND TYPE <>7, 3,TYPE ) AS realtype
                                     FROM 
                                					statements AS A
                                     JOIN 
                                					user AS B 
                            			 ON 
                                					uid = openid
                                    WHERE 
                                                    A.type 
                                            IN 
                                                    (4,5,6,7,8) 
                                		 AND 
                                					(
                                								uid =  '%s'
                                						AND 
                                								A.flag =  '1'  
                                						AND 
                                								A.type<>7 
                                					)
                                            OR 
                                					( 
                                									bid = '%s' 
                                						AND 
                                									A.flag =  '1' 
                                					)
                                ORDER BY 
                                					happen_time 
                                		DESC                                 
                                                    LIMIT 30
                                 ",$openid,$openid,$openid);
        
        /*
        $mysql2 = sprintf("           
                                    SELECT 
                                    				A.price,
                                    				A.happen_time,
                                    				A.uid,
                                    				A.bid,
                                    				A.type,
                                    				B.wx_litpic,
                                    				B.wx_name,
                                    				CASE WHEN type = '4' AND flag  = '0' THEN '4.1' 
                                    						 WHEN type = 7 AND bid = '%s' THEN '7' 
                                    						 WHEN type IN (4,5,6,8) AND uid = '%s' THEN type 
                                    				END AS realtype			
                                    FROM 
                                    				statements  AS A
                                     JOIN
                                    				`user` AS B
                                    	ON
                                    				 A.uid = B.openid
                                    where 		
                                    				(uid = '%s' 
                                    or  
                                    				bid = '%s')
                                    AND			
                                    				type IN (4,5,6,7,8)
                                    ORDER BY
                                    				happen_time
                                    DESC
                                    				LIMIT 30
                                    ",$openid,$openid,$openid,$openid);
        */
        
        if($this->Openid == "oYNn6wg0qYDkqNVomc78AUctYfRM")
        {
            //发送语句
           // return $this->Sql->query($mysql2);
        }
        
        
        //发送语句
        return $this->Sql->query($mysql); 
    }
    
}


//接受请求==================================

if(@$_POST["type"] == "UserYuE")
{
    $_UserCtrl = new UserCtrl();
    
    $_UserCtrl->Ajax_提现();
    
    /*
    IF($_SESSION["openid"] == "oYNn6wg0qYDkqNVomc78AUctYfRM" || $_SESSION["openid"] == "oYNn6wi2Lg4qvvQDOFFTMXpY6ulY")
    {
        $_UserCtrl->Ajax_提现(); 
    }  
    else
    {
       $_UserCtrl->Ajax_获取用户余额();
    }*/
}

?>