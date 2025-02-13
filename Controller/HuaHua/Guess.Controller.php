<?php 

ini_set('date.timezone','Asia/Shanghai');


//控制器专用累
class GuessCtrl extends Lee
{
    private  $Sql;	          //该sql类的全局对象
    
    private $Openid;      //用户微信号
    
    function __construct()
    {
        if(!class_exists("Mysql"))
        {
                //引入核心sql类库
                include $_SESSION["APP_ROOT"].'/Lib/Class/Mysql.class.php';              
        }
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
    
    public function get_headpic()
    {
        //选择表
        $this->Sql->table = "visitor";
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf(" question_id = '%s' ",$_GET["q"]); 
        //发送语句
        $arr = $this->Sql->where($where)->field('wx_litpic,flag,daojuflag')->order("visitime desc")->limit(50)->select();
        //返回结果
        return $arr;
    }
    
    public function get_headpic_count()
    {
        //选择表
        $this->Sql->table = "visitor";
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf(" question_id = '%s' ",$_GET["q"]);
        //发送语句
        $count = $this->Sql->where($where)->getCount();
        //返回结果
        return $count;
    }
    
    
    public function get_answerList()
    {
        //选择表
        $this->Sql->table = "answer_details";
        //重置
        $this->Sql->reset();
        //条件语句
        $mysql = sprintf("SELECT A.content,B.wx_litpic,B.wx_name FROM `answer_details` AS A JOIN user AS B ON  A.user_id = B.openid WHERE A.question_id = '%s' AND  A.Is_Hongbao = '1' order by A.answer_time desc limit 15",$_GET["q"]);
        //发送语句
        $arr = $this->Sql->query($mysql);
        //返回结果
        return $arr;
    }
    
   
    public function get_answerList_count()
    {
        //选择表
        $this->Sql->table = "answer_details";
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf(" question_id = '%s' ",$_GET["q"]);
        //发送语句
        $count = $this->Sql->where($where)->getCount();
        //返回结果
        return $count;
    }
    
    public function Update_更新流水状态($order,$type)
    {
        $this->Sql->reset();
        //选择表
        $this->Sql->table = "statements";
        //条件语句
        $where = sprintf("id = '%s' ",$order);
        //数据结构
        $data["flag"] = $type;
        //发送语句
        $this->Sql->where($where)->save($data);
       
    }
	
	public function get_获取道具比例()
    {
	   $this->Sql->table = "setting";
	   $this->Sql->reset();
	   $dd=$this->Sql->where("id=1")->select();
	   $prop =  @$dd[0]["prop"]; 
	   return $prop;
	}
	
	public function get_获取答题花销比例()
	{
	    $this->Sql->table = "setting";
	    $this->Sql->reset();
	    $dd=$this->Sql->where("id=1")->select();
	    $prop =  @$dd[0]["model_prop"];
	    return $prop;
	}
	
	public function get_获取抽水比例()
	{
	    $this->Sql->table = "setting";
	    $this->Sql->reset();
	    $dd=$this->Sql->where("id=1")->select();
	    $prop =  @$dd[0]["water"];
	    return $prop;
	}
    
     public function  C_金额转换($money)
     {
           return $money*100;
     }
	
    
    public function Ajax_流水记录和微信支付json($money,$openid,$cot)
    {
		
		if(is_numeric($money) && is_numeric($cot) && $cot >= 1 && $money >= 0)
		{
		      $orderid = WxPayConfig::MCHID.uniqid();             //订单号
		      $prict_count=$this->C_金额转换($money)*$cot;     //价格
		    
		    
                //选择表
                $this->Sql->table = 'statements';
                //数据结构
                $data["id"] = $orderid;
                $data["type"] = "1";
                $data["price"] =   $prict_count;
                $data["happen_time"] = time();
                $data["happen_time"] = time();
                $data["uid"] = $openid;
                $data["question_id"] = $_GET['q'];    
                //======经过bug加上的字段 20160615
                $data["hongbao_count"] = $cot;
                $data["hongbao_price"] = $this->C_金额转换($money);
                //插入语句
                $this->Sql->add($data);
                        
        		
                $ko=new WX_INT();
                $jsApiParameters=$ko->Jspay("添加红包","添加红包",$prict_count,"http://huahua.ncywjd.com/Module/HuaHua/Notify.php",$openid,$orderid);
                $arr = array('Msg' => '请求成功！' , 'Result' => array('order' => $orderid, 'wxjson' => $jsApiParameters) , 'Status' => '成功' );
                //返回为json
                exit(json_encode($arr));
		}
		
    
    }
    

    public  function Ajax_更新画画的数据($id,$price_count,$hongbao_count,$prop,$order)
    {
        //选择表
        $this->Sql->table = "question";
        //条件语句
        $where = sprintf("id = '%s' ",$id);
        //生成数据
        //$price = round($price_count / $hongbao_count,1);
        $data["price"] = $this->C_金额转换($price_count);
        $data["price_count"] = $this->C_金额转换($price_count)*$hongbao_count;
        $data["hongbao_count"] = $hongbao_count;
        $data["shengyu_count"] = $hongbao_count;
        $data["prop"] =$this->C_金额转换($prop);
        //sql语句发送
        $this->Sql->where($where)->save($data);
    
        //更新流水记录
        $this->Update_更新流水状态($order, '1');
    
        //AJAX接受的信息
        $arr = array('Msg' => '请求成功！' , 'Result' => '' , 'Status' => '成功' );
        //返回为json
        exit(json_encode($arr));
    }
    
    public function SET_用户($openid,$name,$pic)
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
           $this-> Insert_新增用户($openid,$name,$pic);
        }
        //如果为推广用户
        else if($openid != "" && $wx_litpic == "")
        {
            $this-> Update_更新用户($openid,$name,$pic);
        }
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
    
    
    public  function Add_插入访客($openid,$pic,$name)
    {
        //用户表
        $this->Sql-> table = 'visitor';
        //条件语句
        $where = sprintf(" openid = '%s' and question_id='%u'",$this->Openid,$_GET["q"]);
        //发送语句
        $count = $this->Sql->where($where)->getCount();
        
        IF($count == 0)
        {
            //重置
            $this->Sql->reset();
            //数据结构
            $data["openid"] = $openid;
             $data["wx_litpic"] = $pic;
             $data["wx_name"] =$name;
             $data["visitime"] = time();
             $data["question_id"] = $_GET["q"];
             
             $this->Sql->add($data);
        }
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
    
    public function get_距离下一次答题的时间()
    {
        //选择数据库
	    $this->Sql->table = 'answer_details';
	    //获取与当前相减的差
	    $sql = sprintf(" SELECT TIMESTAMPDIFF( SECOND , FROM_UNIXTIME( `answer_time`) , NOW( )) - (SELECT answer_limit_time as S from setting) AS ShengYuTime FROM answer_details WHERE `user_id` = '%s' AND question_id = '%s' order by answer_time  desc limit 1 ",$this->Openid,$_GET['q']);
	    //发送语句
	    $ShengYuTime = $this->Sql->query($sql);
	    //返回结果(有可能返回空，所以会报错，手动避免)
	    $ShengYuTime =  @$ShengYuTime[0]["ShengYuTime"];   
	    //有可能为空
	    if($ShengYuTime == "")
	    {
	        return "0";
	    }
	     
	    return $ShengYuTime;
    }
    
    
    public function get_根据ID获取画画信息()
    {
        //选择数据库
        $this->Sql->table = 'question';
        
        $mysql = sprintf("
                          SELECT 
                                        A.*,B.wx_name
                            FROM 
                                        question AS A
                            JOIN
                                        user AS B
                            ON
                                        uid = openid
                            WHERE
                                        A.id = '%s'
                            LIMIT 
                                         1
                    ",$_GET["q"]);
        
        
        //发送语句，获取资料
        $arr = $this->Sql->query($mysql);
        //判断
        IF($arr == null)
        {
            exit("<style type=\"text/css\">#face{margin:0px auto;background: #9ee675;/* for Webkit */background: -webkit-gradient(linear, left top, left bottom, from(#9ee675), to(#78cb4c));/* for Firefox */background: -moz-linear-gradient(top,  #9ee675,  #78cb4c);/* for IE */filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#9ee675',endColorstr='#78cb4c'); color:#fff;border:1px solid #fff;border-radius:200px;text-align:center;width:200px;height:200px;font-size:126px;letter-spacing: 5px;padding-top: 5px;}  *{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: \"微软雅黑\"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 3.8em;text-align: center; font-size: 36px }</style><div style=\"padding: 24px 48px;\"> <h1 id=\"face\">:(</h1><p style='font-size:66px'>未找到题目</p></div>");
        }
        //返回结果 
        return $arr[0];
    }
    
    public function Get_将字符串转化为span($str)
    {
        $re = chunk_split($str,3,",");
        $re = explode(",",$re);  
        $str2 = "";
        for($i = 0;$i<count($re) - 1;$i++)
        {
              $str2.= sprintf("<span class='tipsFont'>%s</span>",$re[$i]); 
        }
        return $str2;
    }
    
    public function Is_是否为回答正确但没有获取红包的用户()
    {     
        //选择表
        $this->Sql-> table = 'statements';
        //条件语句
        $where = sprintf(" uid = '%s' AND question_id = '%s' AND type = '5' ",$this->Openid,$_GET["q"]);
        //获取总数
        $count = $this->Sql->where($where)->getCount();
        
        if($count == 0)
        {
            return true;
        }
        
        return false;
    }
    
    public function Is_是否用户已经回答正确过()
    {
        //选择表
        $this->Sql-> table = 'answer_details';
        //条件语句
        $where = sprintf("user_id = '%s' AND  question_id = '%s' AND flag = '1' ",$this->Openid,$_GET["q"]);
        
        //发送语句
        $count  = $this->Sql->where($where)->getCount();
        
        if($count > 0)
        {
            return true;
        }
       
        return false;
        
    }
    
   public function Is_是否是画主($q)
   {
       //选择表
       $this->Sql-> table = 'question';
       //条件语句
       $where = sprintf("id = '%s' ",$q);
       //获取画主
       $arr  = $this->Sql->where($where)->field('uid')->find();
       $uid = $arr["uid"];
       //判断
       if($uid == $this->Openid)
       {
            return true;
       }
     return false;
   }
   
  
   public function 红包是否无剩余或者过期()
   {
        //重置
       $this->Sql->reset();
       //选择表
       $this->Sql->table = 'question';
       //条件语句
       $where = sprintf(" id = '%s' ",$_GET['q']);
       //发送语句
       $arr =  $this->Sql->where($where)->field('price,shengyu_count,expire_time,price_count,uid,flag')->find();
       $price = $arr["price"];
       $price_count = $arr['price_count'];
       $shengyu_count =$arr["shengyu_count"];
       $expire_time = $arr['expire_time'];
       $uid = $arr["uid"];  //画主ID
       $flag = $arr["flag"];  
       
       if($price != 0 && time() > $expire_time && $flag != "2")
       {
           //六个参数，红包剩余数量，红包总数，金额总数，单价，道具金额，flag = '2'
           //更新状态为2，退还金额(增加用户余额) ，添加流水
           $this->Sql->reset();
           //数据结构
           $data["flag"] = '2';
           $data["price"] = '0';
           $data["prop"] = '0';
           $data["price_count"] = '0';
           $data["hongbao_count"] = '0';
           $data["shengyu_count"] = '0';
           //条件语句
           $where = sprintf(" id = '%s' ",$_GET['q']);
           //发送语句
           $this->Sql->where($where)->save($data);
           
           //退款金额大于0才触发
           if($price * $shengyu_count > 0)
           {            
                       //获取用户余额
                       $statements_balance =   $this-> get_根据用户id获取余额($uid);
                        
                       //添加流水
                       $this->Sql->table = 'statements';
                       //重置
                       $this->Sql->reset();
                       //数据结构
                       $data2["question_id"] = $_GET['q']; 
                       $data2["type"] = '6';
                       $data2["uid"] = $uid;
                       $data2["flag"] = '1';
                       $data2["id"] = uniqid();
                       $data2["happen_time"] = time();
                       $data2["price"] = $price * $shengyu_count;
                       $data2["balance"] = $statements_balance + $price * $shengyu_count;
                       //添加语句
                       $this->Sql->add($data2);
                        
                       
                       
                       
                       //选择表
                       $this->Sql->table = 'user';
                       //重置
                       $this->Sql->reset();
                       //条件语句
                       $where = sprintf("openid = '%s' ",$uid);
                       //发送语句
                       $this->Sql->where($where)->sum('balance',$price * $shengyu_count);
                       
                    
                       Lee::alert("温馨提示：本题已过期，答对不触发奖励机制");
           }
       }
       
       
       IF($price == 0 || $shengyu_count <= 0 || time() > $expire_time)
       {
          return true;
       }
       
       return false;
   }
   
   public function Cut_根据question_id减少红包数量()
   {
       //选择表
       $this->Sql->table = 'question';
       //重置
       $this->Sql->reset();
       //条件语句
       $where = sprintf(" id = '%s' ",$_GET['q']);
       //减少红包
       $this->Sql->where($where)->sum('shengyu_count',-1);
   }
   
   public function Add_根据指定orderid添加statements表的流水($type,$price,$statements_balance,$bid,$water = 0)
   {
       //猜主添加收益流水
       $this->Sql->table = 'statements';
       //重置
       $this->Sql->reset();
       //数据结构
       $data["id"] = uniqid();
       $data["type"] = $type;
       $data["price"] = $price;
       $data["happen_time"] = time();
       $data["uid"] = $this->Openid;
       $data["flag"] = "1";
       $data["question_id"] = $_GET["q"];
       $data["bid"] = $bid;
       $data["balance"] = $statements_balance + $price;
       $data["water"] = $water;
       //发送语句
       $this->Sql->add($data);
   }
   
   public function Add_猜主回答正确添加流水并且减掉红包数量()
   {
       //选择表
       $this->Sql->table = 'question';
       //重置
       $this->Sql->reset();
       //条件语句
       $where = sprintf(" id = '%s' ",$_GET['q']);
       //发送语句
       $arr =  $this->Sql->where($where)->field('price,shengyu_count,expire_time')->find();
       //获取数据
       $price = $arr['price'];                                       //每个红包多少钱
       $shengyu_count =$arr["shengyu_count"];       //剩余多少红包
       $expire_time = $arr["expire_time"];                 //过期时间    
     
       
       
       //回答正确并且满足红包发放
       IF($price >0 && $shengyu_count > 0 && time() < $expire_time)
       {
           //减少红包
           $this->Sql->where($where)->sum('shengyu_count',-1); 
                      
           
           //获取用户余额
           $statements_balance =   $this-> get_根据用户id获取余额($this->Openid);
           
           
           //猜主添加收益流水
           $this->Sql->table = 'statements';
           //重置
           $this->Sql->reset();
           //数据结构
           $data["id"] = uniqid();
           $data["type"] = '5';
           $data["price"] = $price;
           $data["happen_time"] = time();
           $data["uid"] = $this->Openid;
           $data["flag"] = "1";
           $data["question_id"] = $_GET["q"];
           $data["balance"] = $statements_balance + $price; 
           //发送语句
           $this->Sql->add($data);
           
            
           //添加用户余额
           //选择表
           $this->Sql->table = 'user';
           //重置
           $this->Sql->reset();
           //条件语句
           $where =sprintf(" openid = '%s' ",$this->Openid) ;
           //发送语句
           $this->Sql->where($where)->sum('balance',$price);
       }
       else 
       {
            //红包数量为空或者已经过期
            return 0;
       }
       
       return $price;
   }
   

 public function get_tips()
   {
       //获取正确答案
       $this->Sql->table = 'statements';
       //条件语句
       $myql = sprintf("
                                  SELECT 
                                    	       C.tips,C.tips2  
                                    FROM 
                                    	       statements AS A 
                                    JOIN 	
                                    	       question AS B 
                                    ON 
                                    	       A.question_id = B.id 
                                    JOIN 
                                    	       question_library AS C 
                                    ON 
                                    	       B.answer = C.id 
                                    WHERE 
                                    	       A.flag = '1' 
                                    AND 
                                    	       A.type = '2' 
                                    AND 
                                    	       A.question_id = '%s'
                                    AND 
                                    	       A.uid = '%s' 
                                     ",$_GET["q"],$this->Openid);
        //返回数据
         return  $this->Sql->query($myql);
   }
 
   
   public function get_tips_for_word()
   {
       //选择表
       $this->Sql->table = 'daojuinfo';
       //重置
       $this->Sql->reset(); 
       //条件语句
       $where = sprintf(" question_id = '%s' AND openid = '%s' ",$_GET['q'],$this->Openid);
       //查询语句
       $Result =   $this->Sql->where($where)->field('tips')->find(); 
       //返回结果
       return $Result["tips"];
   }
   
   
   //已废弃
   public function get_tips_word()
   { 
       $myarr = array();
       //选择表
       $this->Sql->table = 'daojuinfo';
       //重置
       $this->Sql->reset();
       //查询语句
       $mysql = sprintf(" SELECT 
                                        		* 
                                      FROM 
                                        		daojuinfo  AS A
                                      JOIN 
                                          		question AS B
                                    	ON 
                                        		A.question_id = B.id
                                      JOIN
                                          		question_library AS C
                                    	ON
                                        		B.answer = C.id
                                  WHERE
                                          		B.id = '%s'
                             ",$_GET['q']);
       //发送语句
       $arr =  $this->Sql->query($mysql);
       
       if($arr)
       {
               //答案
               $answer = $arr[0]["answer"];
               //将答案划分为数组
               $re = chunk_split($answer,3,",");
               //再利用explode将字符串分割为数组
               $re = explode(",",$re);
               //word_index
               $word_index = $arr[0]["word_index"];
               //已有索引
               $indexarr = explode(",", $word_index);
               //遍历数组
               for($k = 0;$k < count($indexarr);$k++)
               {
                   $myindex = $indexarr[$k];
                   array_push($myarr, $re[$myindex]);
               } 
       }
       return $myarr;
   }
   
   public function get_tip_rows()
   {
       //获取正确答案
       $this->Sql->table = 'statements';
       //重置
       $this->Sql->reset();
       //条件语句
       $where  = sprintf("
                                    	      flag = '1' 
                                    AND 
                                    	       type = '2' 
                                    AND 
                                    	      question_id = '%s'
                                    AND 
                                    	     uid = '%s' 
                                 ",$_GET["q"],$this->Openid);
        //返回数据
         return  $this->Sql->where($where)->getCount();
   }
   
   function get_根据openid获取用户名($openid)
   {
       //选择表
       $this->Sql->table = "user";
       //条件语句
       $where = sprintf( " openid = '%s' ",$openid);
       //发送查找
       $ret = $this->Sql->where($where)->field("wx_name")->find();
       //记录一下
       //Lee::WriteLog("get_根据openid获取用户名:".$ret["wx_name"]);
       //返回结果
       return $ret["wx_name"];
   }
    
    public function Ajax_提交答案($id,$content,$orderid,$money)
    {       
        $wx_ko= new WX_INT();        
        $wx_msg = $wx_ko->CheckOrder($orderid);        
        if($wx_msg != "SUCCESS")
        {
            //AJAX接受的信息
            $arr = array('Msg' => '订单不存在,请联系客服' , 'Result' => "", 'Status' => '失败' );
            //返回为json
            exit(json_encode($arr));
        }        
                
        //选择流水表
        $this->Sql->table = 'statements';
        //我的sql
        $mysql = sprintf("SELECT 
                                        		A.flag,
                                        		A.price AS statements_price,
                                                A.Is_Use,
                                        		B.uid,
                                                B.shengyu_count,
                                                B.price AS question_price,
                                                B.expire_time,
                                                C.answer
                                    FROM 
                                	 	        statements AS A
                                    JOIN
                                    	 	    question AS B
                                      ON
                                         	    A.question_id = B.id
                                    JOIN
                                    	 	    question_library AS C
                                      ON
                                      	 	    C.id = B.answer
                                WHERE	 
		                                        A.id = '%s'
                                   LIMIT 
                                                1
                                 ",$orderid);
       
        
    
        $_result = $this->Sql->query($mysql);
        
        
        //获取结果集
        $flag = $_result[0]["flag"];
        $statements_price = $_result[0]["statements_price"];
        $uid = $_result[0]["uid"];
        $shengyu_count = $_result[0]["shengyu_count"];
        $question_price = $_result[0]["question_price"];
        $expire_time = $_result[0]["expire_time"];
        $answer = $_result[0]["answer"];
        $Is_Use = $_result[0]["Is_Use"];
        //获取答题花销比例
        $datihuaxiaobili = $this->get_获取答题花销比例();
        //获取抽水比例
        $water_bili = $this->get_获取抽水比例();
        
        
        //如果用户未支付或者提交的金额不等于需要花销的金额、或者该订单已经使用过了，那么终止程序。并且考虑记录日志 $flag != 1 
        if(($datihuaxiaobili * $question_price) != $money || $Is_Use == "1")
        {
            exit();
        }

        $tips = "";
        $Is_Real = false;        
        $Is_ok = false;
        $Is_Real_flag = 0;
        $Is_HongBao = 0;
        
        //如果题目还有钱并且没有过期
        if($question_price >0 && $shengyu_count > 0 && time() < $expire_time)
        {
            $Is_ok = true;
            $Is_HongBao = 1;
        }
        
        //如果回答正确了
        if($content == $answer) 
        {
            $Is_Real = true;
            $Is_Real_flag = 1;
        }

        
        //选择表
        $this->Sql->table = 'answer_details';
        //还原配置,如where,order,limit,field
        $this->Sql->reset();
        //获取POST数据
        $data["question_id"] = $id;
        $data["user_id"] = $this->Openid;
        $data["flag"] = $Is_Real_flag;
        $data["answer_time"] = time();
        $data["content"] = $content;
        $data["Is_Hongbao"] = $Is_HongBao;
        //发送语句,返回id
        $Result_id =  $this->Sql->add($data);
        
        
        
        //抽水
        $water_question_price =  $question_price - ($question_price * $water_bili);
        $water_money = $money - ($money * $water_bili);
 
        
        /*如果有红包并且答对*/
        if($Is_ok == true && $Is_Real == true)
        {       
			//减掉红包数量
            $this->Cut_根据question_id减少红包数量();
            //访客答对了
            $this->Update_访客是否答对($this->Openid,$_GET["q"]); 
				
	
			//获取用户余额
            $statements_balance = $this->get_根据用户id获取余额($this->Openid); 	
            //给猜主添加流水
            $this->Add_根据指定orderid添加statements表的流水("5", $water_question_price, $statements_balance, "",$question_price * $water_bili);			
            //给猜主添加用户余额
            $this->Add_给指定id的用户从user表中添加余额($this->Openid,$water_question_price);	
			
			
			
            //获取用户余额
            $statements_balance = $this->get_根据用户id获取余额($uid);
            //更新流水
            $this->Update_根据指定的orderid更新statements表中的流水($orderid, $water_money, $statements_balance, "7", $uid,$money * $water_bili);
            //添加画主余额
            $this->Add_给指定id的用户从user表中添加余额($uid,$water_money);
            
            //uid 通知画主
            $data2 = json_decode(file_get_contents("../../Module/HuaHua/access_token.json"));
            $access_token = $data2->access_token;
            $cz_ming=$this->get_根据openid获取用户名($this->Openid);
            $mes_ko=$cz_ming."支付了给您【".($money/100)."元】，并答对了您的【".$answer."】,Ta获得了奖金【".($question_price/100)."元】";  
            $website="http://huahua.ncywjd.com/home.php?p=guess&q=".$_GET["q"];
            $wx_ko->SendMessage("快点击查看，有人答对了你的题目：",$website,$mes_ko,$access_token,$uid);
        } 
        /*如果有红包但是打错了*/
        else if($Is_ok == true && $Is_Real == false)
        {
            //获取用户余额(画主)
            $statements_balance = $this->get_根据用户id获取余额($uid);			       
            //更新流水余额和其他信息（画主）
            $this->Update_根据指定的orderid更新statements表中的流水($orderid, $water_money, $statements_balance, "7", $uid,$money * $water_bili);
			//给画主添加用户余额(画主)
            $this->Add_给指定id的用户从user表中添加余额($uid,$water_money); 
			
            //获取tips的索引
            $tips_index = rand(0, 3);
            //获取生成的提示
            $tips =$this->get_生成提示($answer,$tips_index);                
            if($tips != "" && $tips_index !== "")
            {                     
                //插入数据库
                $this->Daoju_添加道具购买标识($tips_index,$tips); 
                $this->Daoju_添加访客道具购买标识(); 
            }            
            //uid 通知画主
            $data2 = json_decode(file_get_contents("../../Module/HuaHua/access_token.json"));
            $access_token = $data2->access_token;
            $cz_ming=$this->get_根据openid获取用户名($this->Openid);
            $mes_ko= $cz_ming."支付了给您【".($money/100)."元】，并答错了您的【".$answer."】，Ta提交的答案是【".$content."】";
            $website="http://huahua.ncywjd.com/home.php?p=guess&q=".$_GET["q"];
            $wx_ko->SendMessage("快点击查看，有人答错了你的题目：",$website,$mes_ko,$access_token,$uid);
        }        
       /*没有红包，但是答对了*/
        else if($Is_ok == false && $Is_Real == true)
        {            
            //获取用户余额
            $statements_balance = $this->get_根据用户id获取余额($this->Openid); 
            //退还用户流水
            $this->Update_根据指定的orderid更新statements表中的流水($orderid, $money, $statements_balance, "8", "");
            //给猜主添加用户余额
            $this->Add_给指定id的用户从user表中添加余额($this->Openid,$money);
            //修改标识
            $question_price = 0;
        }        
        /*没有红包，又答错*/
        else if($Is_ok == false && $Is_Real == false)
        {
            //获取用户余额
            $statements_balance = $this->get_根据用户id获取余额($this->Openid);
            //纯盈利
            $this->Update_根据指定的orderid更新statements表中的流水($orderid, $money, $statements_balance, "9", ""); 
        } 
         
        //AJAX接受的信息
        $arr = array('Msg' => '请求成功！' , 'Result' => array('id' => $Result_id,'flag' => $Is_Real_flag,'price'=>$question_price,'tips'=>$tips), 'Status' => '成功' );
        //返回为json
        exit(json_encode($arr));
    }
    
    public function Add_给指定id的用户从user表中添加余额($openid,$money)
    {
        //添加用户余额
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf(" openid = '%s' ",$openid);
        //添加金额
        $this->Sql->where($where)->sum("balance",$money);
    }
    
    public function Update_根据指定的orderid更新statements表中的流水($orderid,$money,$statements_balance,$type,$bid,$water = 0)
    {
        //选择流水表
        $this->Sql->table = 'statements';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf(" id = '%s' ",$orderid);
        //数据结构
        $data2["happen_time"] = time();
        $data2["flag"] = '1';
        $data2["type"] =$type;
        $data2["bid"] = $bid;
        $data2["price"] = $money;
        $data2["balance"] = $statements_balance + $money;
        $data2["Is_Use"] = '1';
        $data2["water"] = $water;
        //发送语句
        $this->Sql->where($where)->save($data2);
    }
    
    public function Daoju_添加访客道具购买标识()
    {
        //选择流水表
        $this->Sql->table = 'visitor';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf(" openid = '%s' AND question_id = '%s' ",$this->Openid,$_GET['q']);
        //更新语句
        $this->Sql->where($where)->sum("daojuflag",1);
    }
    
    public function Daoju_添加道具购买标识($index,$tips)
    {
        //选择流水表
        $this->Sql->table = 'daojuinfo';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf(" openid = '%s' AND question_id = '%s' ",$this->Openid,$_GET['q']);
        //获取总数
        $count = $this->Sql->where($where)->getCount();
        
        //预定义语句
        $mysql = "";
        
        //判断
        if($count > 0) 
        {
            //更新
            $mysql = sprintf("update daojuinfo set word_index = concat(word_index,',%s'),tips = concat(tips,',%s') where openid = '%s' AND question_id = '%s'  ",$index,$tips,$this->Openid,$_GET['q']);
        } 
        else
        {
            //插入
            $mysql = sprintf("insert into  daojuinfo  (word_index,openid,question_id,tips) values ('%s','%s','%s','%s')  ",$index,$this->Openid,$_GET['q'],$tips);
        } 
      
        //发送语句
        $this->Sql->send($mysql); 
    } 
	
	
	//写死300哦
	public function Update_更新用户冷却时间()
	{
		//选择流水表
        $this->Sql->table = 'answer_details';
		//重置
        $this->Sql->reset();
		//条件语句
		$where = sprintf(" question_id = '%s' AND user_id = '%s' ",$_GET['q'],$this->Openid);
		//数据结构
		$data["answer_time"] = time() - 300;
		//更新语句
		$this->Sql->where($where)->save($data);
	}
    
    //抵达这一步，说明真的购买成功道具了
    public function Ajax_购买道具($order,$money,$bid,$tips_index,$tips)
    { 
        //获取用户余额
        $statements_balance =   $this-> get_根据用户id获取余额($bid);
        
        //选择流水表
        $this->Sql->table = 'statements';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf(" id = '%s' ",$order);
        //数据结构
        $data["happen_time"] = time();
        $data["flag"] = '1';
        $data["balance"] = $statements_balance + $money;
        //发送语句
        $this->Sql->where($where)->save($data);
        
        
        
        
        //插入道具流水
        if($tips_index != "" && $tips != "")
        {
              $this->Daoju_添加道具购买标识($tips_index,$tips); 
              $this->Daoju_添加访客道具购买标识();
        }
        
		//更新用户答题的冷却时间
		$this->Update_更新用户冷却时间();
		
         
        //添加用户余额
        //选择流水表
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
         //条件语句
        $where = sprintf(" openid = '%s' ",$bid);
        //添加金额
        $this->Sql->where($where)->sum("balance",$money);
        //拼接json
        $arr = array('Msg' => '购买成功！' , 'Result' =>"", 'Status' => '成功' );
        //返回结果
        exit(json_encode($arr));
    }
    
    
    
    
    public function Ajax_重新添加红包($order,$HongBaoJinE,$HongBaoCount,$model)
    {
        //============================ 在经历大量订单异常之后，加入的机制======================================
        $wx_ko= new WX_INT();
        $wx_msg = $wx_ko->CheckOrder($order); 
        if($wx_msg != "SUCCESS")
        {
            //AJAX接受的信息
            $arr = array('Msg' => '订单不存在,请联系客服' , 'Result' => "", 'Status' => '失败' );
            //返回为json
            exit(json_encode($arr));
        }
        
        
        //============================ 在经历bug攻击后，加入判断======================================
        //选择流水表
        $this->Sql->table = 'statements';
        //条件语句
        $where = sprintf(" id = '%s' AND type = '1' ",$order);
                
        $rett = $this->Sql->field("price,hongbao_price,hongbao_count,flag,Is_Use")->where($where)->find();
                
        
        //获取用户充值完成的金额
        $rett_price = $rett["price"];                                      //用户充值的流水金额
        $rett_hongbao_price =  $rett["hongbao_price"];      //红包单价
        $rett_hongbao_count =  $rett["hongbao_count"];   //红包数量
        $rett_flag = $rett["flag"];                                         //是否充值完成
        $rett_Is_Use = $rett["Is_Use"];                                 //订单是否已经使用
        //用户提交的金额
        $rett_count= $HongBaoJinE * $HongBaoCount;
        
        //$rett_Is_Use如果为0，说明订单未使用  $rett_flag == 1 
        if($rett_Is_Use == "0"  && $rett_price > 0  && ($rett_hongbao_price / 100) == $HongBaoJinE && $rett_hongbao_count  ==$HongBaoCount)
        {                           
                //修改更新标识
                $data["Is_Use"] = "1"; 
                $data["flag"] = '1';
                //发送语句
                $this->Sql->where($where)->save($data);                
                
                //获取道具比例
                //$DaoJuBiLi = $this->get_获取道具比例();                //$this->C_金额转换($HongBaoJinE) * floatval($DaoJuBiLi);  
              
                //选择流水表
                $this->Sql->table = 'question';
                //重置
                $this->Sql->reset();        
                //数据结构
                $data2["price"] = $this->C_金额转换($HongBaoJinE);
                $data2["price_count"] = $this->C_金额转换($HongBaoJinE)*$HongBaoCount;
                $data2["hongbao_count"] = $HongBaoCount;
                $data2["shengyu_count"] = $HongBaoCount;
                $data2["prop"] = "0";   
                $data2["expire_time"] = strtotime("+24 hours");
                $data2["release_time"] = time();
                $data2["model"] = $model;
                $data2["flag"] = '0';
                //条件语句
                $where = sprintf(" id = '%s' ",$_GET["q"]);
                //发送语句
                $this->Sql->where($where)->save($data2);
                
                
                //拼接json
                $arr = array('Msg' => '添加成功！' , 'Result' =>"", 'Status' => '成功' );
                //返回结果
                exit(json_encode($arr));
         }
         else
         {
             //AJAX接受的信息
             $arr = array('Msg' => '订单不存在,请联系客服！！！' , 'Result' => "", 'Status' => '失败' );
             //返回为json
             exit(json_encode($arr));
         }
         
    }
    
    public function Update_访客是否答对($openid,$question_id)
    {
        $this->Sql->table = 'visitor';
        $this->Sql->reset();
        $data["flag"] = 1; //修改状态值为1
        $where = sprintf(" openid = '%s' and question_id='%s' ",$openid,$question_id);
        $this->Sql->where($where)->save($data);
    } 
    
    public function get_生成提示($answer,&$tips_index)
    {        
        //选择表
        $this->Sql->table = 'daojuinfo';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf("question_id = '%s' AND openid = '%s' ",$_GET["q"],$this->Openid) ;
        //查询语句
        $word_index =  $this->Sql->where($where)->find();
        //获取结果
        $word_index = $word_index['word_index'];
        //先假设用户没有购买的记录，随机从答案中抽取一个出来返回
        $re = chunk_split($answer,3,",");
        //再利用explode将字符串分割为数组
        $re = explode(",",$re);
        
        
      
        //赋值,并且加上逗号
        $tips = $re[$tips_index];
        
        
         
        //判断用户有购买记录
        IF(strlen($word_index) > 0)
        {
            //数组
            $word_index_arr = explode(",",$word_index);
            
            if(count($word_index_arr) >= 4)
            {
                //答案已经全部展示给用户了。没什么好展示的了。除非以后扩展
                $tips = "";
                //索引也设置为空
                $tips_index = "";
            }
            else
            {
                //生成一个数组
                $gsarr = array("0","1","2","3");
                //差集
                $array_diff = array_diff($gsarr,$word_index_arr);
                //打乱数组
                shuffle($array_diff);
                //直接返回第一个,将这个数据作为索引
                $tips_index = $array_diff[0];
                //获取随机提示,获取随机提示,获取随机提示
                $tips = $re[$tips_index];
                
                 
                //加上三个字
                $txt = file_get_contents(dirname(__FILE__)."/a.txt");
                $len = mb_strlen($txt,'utf-8');
                for($i = 0;$i< 3;$i++)
                {
                    $rand =  rand(0, $len - 1);
                    $word = mb_substr($txt, $rand,1,"utf-8");
                    if(in_array($word, $re) || mb_strlen($word) < 3)
                    {
                        //..如果随机获取的值中居然有答案的词，那么返回
                        $i--;
                        continue;
                    }                       
                    $tips .= ",".$word;
                }
            }
        }
        else
        {
            //加上三个字
            $txt = file_get_contents(dirname(__FILE__)."/a.txt");
            $len = mb_strlen($txt,'utf-8');
            for($i = 0;$i< 3;$i++)
            {
                $rand =  rand(0, $len - 1);
                $word = mb_substr($txt, $rand,1,"utf-8");
                if(in_array($word, $re) || mb_strlen($word) < 3)
                {
                    //..如果随机获取的值中居然有答案的词，那么返回
                    $i--;
                    continue;
                }              
                $tips .= ",".$word;
            }
        }
        
        
        return Lee::shuffle_打散并且洗牌字符串($tips);
        
         
    }
    
    //购买道具,购买道具,购买道具
    public function Ajax_微信支付json()
    {
        //订单号
        $orderid = WxPayConfig::MCHID.uniqid();   
        //选择表
        $this->Sql->table = 'question';
        //获取q参数
        $q = $_GET["q"];
        //自定义sql     
        $mysql = sprintf("SELECT uid,(prop) AS daoju,B.answer FROM question AS A JOIN question_library AS B ON A.answer = B.id WHERE A.id =  '%s'",$q);
        //发送语句，获得道具金额
        $daoju =  $this->Sql->query($mysql);    
        //获取道具金额
        $daojujiage =  $daoju[0]["daoju"];
        //uid
        $uid =  $daoju[0]["uid"];              //用户uid也是受益者的id
        $answer = $daoju[0]['answer'];         //正确答案
        
        
     
        
        
        //获取tips的索引
        $tips_index = rand(0, 3);
        //获取生成的提示
        $tips =$this->get_生成提示($answer,$tips_index);
     
        
        
        //重置
        $this->Sql->reset();
        //选择流水表，先插入流水，等微信确定用户支付成功后，再从另外一个地方更改该表的‘flag’字段为1
        $this->Sql->table = 'statements';
        //数据结构
        $data["id"] = $orderid;               //订单号，订单号，订单号
        $data["type"] = "2";                   //2:道具购买
        $data["price"] =  $daojujiage;    //道具的金额
        $data["happen_time"] = time();  //时间
        $data["uid"] = $this->Openid;   //道具购买者的id
        $data["bid"] = $uid;                  //受益者id
        $data["question_id"] = $_GET["q"];  //问题的id
        //新增语句
        $this->Sql->add($data);  
        
        
        $ko=new WX_INT();
        $jsApiParameters=$ko->Jspay("道具购买","道具购买",$daojujiage,"http://huahua.ncywjd.com/Module/HuaHua/Notify.php",$this->Openid,$orderid);
        $arr = array('Msg' => '请求成功！' , 'Result' => array('order' => $orderid,'tips' =>$tips,'tips_index'=>$tips_index,'money' => $daojujiage, 'uid' =>$uid ,'wxjson' => $jsApiParameters) , 'Status' => '成功' );
        //返回为json
        exit(json_encode($arr));
    }
    
    public function Ajax_答题花销($id)
    {
        //生成订单号
        $orderid = uniqid();
        
        //微信json
        $jsApiParameters = "";
        
        //----------------------------------------
        
      $datihuaxiaobili =   $this->get_获取答题花销比例();
        
        //选择表
        $this->Sql->table = "question";
        //条件语句
        $where = sprintf( " id = '%s' ",$id);
        //发送查找
        $ret = $this->Sql->where($where)->find();
        //获取消费
        $model_price = $ret["price"] * $datihuaxiaobili;
        //获取画主
        $uid  = $ret["uid"];
        
        //----------------------------------------
        
        //选择表
        $this->Sql->table = "statements";
        //重置
        $this->Sql->reset();
        //插入流水
        $data["id"] = $orderid;
        $data["type"] = "7";
        $data["price"] = $model_price;
        $data["happen_time"] = time();
        $data["uid"] = $this->Openid;
        $data["flag"] = "0";
        $data["bid"] = $uid;
        $data["question_id"] = $id;
        
        //------------------------------------
        
        if($model_price != 0)
        {
             $ko=new WX_INT();
             $jsApiParameters=$ko->Jspay("添加红包","添加红包",$model_price,"http://huahua.ncywjd.com/Module/HuaHua/Notify.php",$this->Openid,$orderid);
        }
        
       //发送请求
       $this->Sql->add($data);
       //拼接json
       $arr = array('Msg' => '请求成功！' , 'Result' => array('order' => $orderid,'money' => $model_price,"wxjson"=>$jsApiParameters) , 'Status' => '成功' );
       //返回为json
       exit(json_encode($arr));
    }    
}


//接受请求==================================

IF(@$_POST["type"] == 'TiJiaoDaAn')
{
    //实例化
    $_GuessCtrl = new GuessCtrl();
    //获取参数
    $id = $_GET["q"];                            //id
    $content = $_POST["content"];      //内容
    $orderid = @$_POST["order"];       //订单号，只有答题消费模式才会有这个参数
    $money = @$_POST["money"];     //金额
    
    

    if(in_array($_SESSION["openid"], array("oYNn6wlH0ISNU6La_sVB5aD_V7HE","oYNn6wg0qYDkqNVomc78AUctYfRM","oYNn6wo-I9w4ZoFwWHVgYa5LGIco","oYNn6wnbqFJFCSrVZ_POsKlQDQPQ","oYNn6wuIPaeN5zYuMbuu6B2p4xs4","oYNn6wr9wYVnLSpczb2TQFjqHu-Y")))
    {
        $str = sprintf("答题——用户id：%s,参数：question_id:%s,内容：%s,  订单号:%s    金额： %s",$_SESSION["openid"],$id,$content,$orderid,$money);
        Lee::WriteLog($str);
    }
    
    
    //调用方法 
    $_GuessCtrl->Ajax_提交答案($id,$content,$orderid,$money);
}


//答题消费模式下面的提交答案，这里实质上是插入流水表，然后返回"orderid"和"消费金额"，在微信回调函数中更新
IF(@$_POST["type"] == 'DaTiHuaXiao')
{
    //实例化
    $_GuessCtrl = new GuessCtrl();
    //获取参数
    $id = $_GET["q"];    
    //调用方法
    $_GuessCtrl->Ajax_答题花销($id);
}




//调用微信支付接口，返回核心json
IF(@$_POST['type'] == 'weixinzhifu2')
{
    $price = $_POST["price"];
    $openid = $_SESSION["openid"]; 
	$cot = $_POST["cot"];
    $_GuessCtrl = new GuessCtrl();
	
	  if(in_array($_SESSION["openid"], array("oYNn6wlH0ISNU6La_sVB5aD_V7HE","oYNn6wg0qYDkqNVomc78AUctYfRM","oYNn6wo-I9w4ZoFwWHVgYa5LGIco","oYNn6wnbqFJFCSrVZ_POsKlQDQPQ","oYNn6wuIPaeN5zYuMbuu6B2p4xs4","oYNn6wr9wYVnLSpczb2TQFjqHu-Y")))
    {       
        $str = sprintf("模拟传值id：%s,金额：%s,  数量:%s",$_SESSION["openid"],$price,$cot);
        Lee::WriteLog($str);
    } 
    $_GuessCtrl->Ajax_流水记录和微信支付json($price, $openid,$cot);
}



IF(@$_POST["type"] == 'ChongXinTianJiaHongBao')
{
    //实例化
    $_GuessCtrl = new GuessCtrl();
    $order = $_POST["order"];
    $HongBaoJinE = $_POST["HongBaoJinE"];       //金额
    $HongBaoCount = $_POST["HongBaoCount"]; //数量
    $model = $_POST["model"];  
    
    
    if(in_array($_SESSION["openid"], array("oYNn6wlH0ISNU6La_sVB5aD_V7HE","oYNn6wg0qYDkqNVomc78AUctYfRM","oYNn6wo-I9w4ZoFwWHVgYa5LGIco","oYNn6wnbqFJFCSrVZ_POsKlQDQPQ","oYNn6wuIPaeN5zYuMbuu6B2p4xs4","oYNn6wr9wYVnLSpczb2TQFjqHu-Y")))
    {       
        $str = sprintf("充值——用户id：%s,参数：order:%s,金额：%s,  数量:%s",$_SESSION["openid"],$order,$HongBaoJinE,$HongBaoCount);
        Lee::WriteLog($str);
    } 
    
   if(is_numeric($HongBaoCount) && is_numeric($HongBaoJinE) && $HongBaoCount >= 1 && $HongBaoJinE >= 0)
    {
          $_GuessCtrl->Ajax_重新添加红包($order,$HongBaoJinE,$HongBaoCount,$model); 
   }    
}

IF(@$_POST["type"] == 'GouMaiDaoJu')
{
    //实例化
    $_GuessCtrl = new GuessCtrl();
    $order = $_POST["order"];
    $money = $_POST["money"];
    $uid = $_POST["uid"];
    $tips_index = $_POST["tips_index"];
    $tips = $_POST["tips"];
    
    $_GuessCtrl->Ajax_购买道具($order,$money,$uid,$tips_index,$tips);
}

?>