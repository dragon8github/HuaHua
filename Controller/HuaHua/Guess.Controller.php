<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class GuessCtrl
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
        $arr = $this->Sql->where($where)->field('wx_litpic,flag')->select();
        //返回结果
        return $arr;
    }
    
    
    public function get_answerList()
    {
        //选择表
        $this->Sql->table = "answer_details";
        //重置
        $this->Sql->reset();
        //条件语句
        $mysql = sprintf("SELECT A.content,B.wx_litpic,B.wx_name FROM `answer_details` AS A JOIN user AS B ON  A.user_id = B.openid WHERE A.question_id = '%s' order by A.content desc limit 10",$_GET["q"]);
        //发送语句
        $arr = $this->Sql->query($mysql);
        //返回结果
        return $arr;
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
	   $this->Sql->reset();
	   $this->Sql->table = "setting";
	   $dd=$this->Sql->where("id=1")->select();
	   $prop =  @$dd[0]["prop"]; 
	   return $prop;
	}
    
     public function  C_金额转换($money)
     {
           return $money*100;
     }
	
    
    public function Ajax_流水记录和微信支付json($stype,$money,$openid,$cot)
    {
        $orderid = WxPayConfig::MCHID.uniqid();             //订单号   	
		$prict_count=$this->C_金额转换($money)*$cot;     //价格
		
        //选择表
        $this->Sql->table = 'statements';
        //数据结构
        $data["id"] = $orderid;
        $data["type"] = $stype;
        $data["price"] =   $prict_count;
		//$data["prop"] = $money*0.5;
        $data["happen_time"] = time();
        $data["uid"] = $openid;
        $data["question_id"] = $_GET['q'];    
        //插入语句
        $this->Sql->add($data);
        
        //获取道具比例 
        $DaoJuBiLi = $this->get_获取道具比例();
        
         
        //选择question表
		$this->Sql->table = "question";
		//重置
		$this->Sql->reset();
        //条件语句
        $where = sprintf("id = '%s' ",$_GET['q']);
        //生成数据       
        $data2["prop"] =$this->C_金额转换($money)*floatval($DaoJuBiLi);
		$data2["price"] =$this->C_金额转换($money);
		$data2["price_count"] =$prict_count;
		$data2["hongbao_count"]=$cot;
		$data2["shengyu_count"]=$cot;
        //sql语句发送
        $this->Sql->where($where)->save($data2);
		//************************************ko博 新增加道具金额问题**************************//
		
        $ko=new WX_INT();
        $jsApiParameters=$ko->Jspay("添加红包","添加红包",$prict_count,"http://huahua.ncywjd.com/Module/HuaHua/Notify.php",$openid,$orderid);
        //exit($jsApiParameters);
        //AJAX接受的信息
        $arr = array('Msg' => '请求成功！' , 'Result' => array('order' => $orderid, 'wxjson' => $jsApiParameters) , 'Status' => '成功' );
        //返回为json
        exit(json_encode($arr));
    
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
	    $sql = sprintf(" SELECT TIMESTAMPDIFF( SECOND , FROM_UNIXTIME( `answer_time`) , NOW( )) - (SELECT answer_limit_time as S from setting) AS ShengYuTime FROM answer_details WHERE `user_id` = '%s' order by answer_time  desc limit 1 ",$this->Openid);
	    //发送语句
	    $ShengYuTime = $this->Sql->query($sql);
	    //返回结果(有可能返回空，所以会报错，手动避免)
	    $ShengYuTime =  @$ShengYuTime[0]["ShengYuTime"];   
	    
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
       $arr =  $this->Sql->where($where)->field('price,shengyu_count,expire_time')->find();
       $price = $arr['price'];
       $shengyu_count =$arr["shengyu_count"];
       $expire_time = $arr['expire_time'];
       
       IF($price == 0 || $shengyu_count <= 0 || time() > $expire_time)
       {
          return true;
       }
       
       return false;
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
           
           //猜主添加收益流水
           //选择表
           $this->Sql->table = 'statements';
           //数据结构
           $data["id"] = uniqid();
           $data["type"] = '5';
           $data["price"] = $price;
           $data["happen_time"] = time();
           $data["uid"] = $this->Openid;
           $data["flag"] = "1";
           $data["question_id"] = $_GET["q"];
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
   
    
    
    public function Ajax_提交答案($id,$content)
    {
        //是否回答正确？
        $flag = 0;  
        //返回的金额
        $price = 0;
        
        //获取正确答案
        $this->Sql->table = 'question_library';
        //条件语句
        $myql = sprintf("
                                    SELECT 
                                                    a.answer 
                                      FROM 
                                                    question_library AS a 
                                        JOIN 
                                                    question AS b 
                                          ON 
                                                    a.id = b.answer 
                                    WHERE 
                                                    b.id = '%s' 
                               ",$id); 
        //发送语句
        $readContent = $this->Sql->field("answer")->query($myql);
        $readContent = $readContent[0]["answer"];
        
        

        if($readContent == $content)
        {
            $this->Update_访客是否答对($this->Openid, $_GET['q']);
            //猜主回答正确
            $flag = 1;
            $price = $this->Add_猜主回答正确添加流水并且减掉红包数量();
        } 
        
        //还原配置,如where,order,limit,field
        $this->Sql->reset();
         
          //选择表
        $this->Sql->table = 'answer_details';
        //获取POST数据
        $data["question_id"] = $id;
        $data["user_id"] = $this->Openid;
        $data["flag"] = $flag;
        $data["answer_time"] = time();
        $data["content"] = $content;
         
        
        if(!Lee::Is_遍历数组中所有的值判断是否有空值($data))
        {
            $arr = array('Msg' => "数据非法，请联系管理员，错误码：00030","Result" => '','Status' => '失败');
            exit(json_encode($arr));
        }
        
       //发送语句,返回id
        $id =  $this->Sql->add($data); 
        //AJAX接受的信息
        $arr = array('Msg' => '请求成功！' , 'Result' => array('id' => $id,'flag' => $flag,'price'=>$price), 'Status' => '成功' );
        //返回为json
        exit(json_encode($arr));  
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
    
    
    public function Ajax_购买道具($order,$money,$uid,$tips_index,$tips)
    { 
        //选择流水表
        $this->Sql->table = 'statements';
        //条件语句
        $where = sprintf(" id = '%s' ",$order);
        //数据结构
        $data["flag"] = '1';
        //发送语句
        $this->Sql->where($where)->save($data);
        
        //插入道具流水
        if($tips_index != "" && $tips != "")
        {
              $this->Daoju_添加道具购买标识($tips_index,$tips); 
        }
         
        //添加用户余额
        //选择流水表
        $this->Sql->table = 'user';
        //重置
        $this->Sql->reset();
         //条件语句
        $where = sprintf(" openid = '%s' ",$uid);
        //添加金额
        $this->Sql->where($where)->sum("balance",$money);
        //拼接json
        $arr = array('Msg' => '购买成功！' , 'Result' =>"", 'Status' => '成功' );
        //返回结果
        exit(json_encode($arr));
    }
    
    
    public function Ajax_重新添加红包($order,$HongBaoJinE,$HongBaoCount)
    {
        //选择流水表
        $this->Sql->table = 'statements';
        //条件语句
        $where = sprintf(" id = '%s' ",$order);
        //数据结构
        $data["flag"] = '1';
        //发送语句
        $this->Sql->where($where)->save($data);
        //重置
        $this->Sql->reset();
        //选择流水表
        $this->Sql->table = 'question';
        //数据结构
       // $price = round($HongBaoJinE / $HongBaoCount,1);  //获取每个红包的金额
        $data2["price"] = $this->C_金额转换($HongBaoJinE);
        $data2["price_count"] = $this->C_金额转换($HongBaoJinE)*$HongBaoCount;
        $data2["hongbao_count"] = $HongBaoCount;
        $data2["shengyu_count"] = $HongBaoCount;
        $data2["expire_time"] = strtotime("+24 hours");
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
    
    public function Update_访客是否答对($openid,$question_id)
    {
        $this->Sql->table = 'visitor';
        $this->Sql->reset();
        $data["flag"] = 1; //修改状态值为1
        $where = sprintf(" openid = '%s' and $question_id='%s' ",$openid,$question_id);
        $this->Sql->where($where)->save($data);
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
        $uid =  $daoju[0]["uid"];                      //用户uid也是受益者的id
        $uid = $daoju[0]["uid"];                     //用户ID
        $answer = $daoju[0]['answer'];         //正确答案
        
        
        //-----------------------------------------------
        
        //重置
        $this->Sql->reset();
        //选择表
        $this->Sql->table = 'daojuinfo';
        //条件语句
        $where = sprintf("question_id = %s",$q) ;
        //查询语句
        $word_index =  $this->Sql->where($where)->find();
        //获取结果
        $word_index = $word_index['word_index'];
        //先假设用户没有购买的记录，随机从答案中抽取一个出来返回
        $re = chunk_split($answer,3,",");
        //再利用explode将字符串分割为数组
        $re = explode(",",$re);
        
                
        //获取tips的索引
        $tips_index = rand(0, 3);
        //赋值
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
                       $tips .= $word . ",";
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
                $tips .= $word . ",";
            }
        }
        
        
        $tips = Lee::shuffle_打散并且洗牌字符串($tips);
     
        
        //重置
        $this->Sql->reset();
        //选择流水表，先插入流水，等微信确定用户支付成功后，再从另外一个地方更改该表的‘flag’字段为1
        $this->Sql->table = 'statements';
        //数据结构
        $data["id"] = $orderid;               //订单号，订单号，订单号，会传递给微信并且传递给前端然后在另外一个地方作为更新的标记
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
   
}


//接受请求==================================

IF(@$_POST["type"] == 'TiJiaoDaAn')
{
    //实例化
    $_GuessCtrl = new GuessCtrl();
    //获取参数
    $id = $_GET["q"];                            //id
    $content = $_POST["content"];      //内容
    //调用方法
    $_GuessCtrl->Ajax_提交答案($id,$content);
}


//调用微信支付接口，返回核心json
IF(@$_POST['type'] == 'weixinzhifu')
{
    $_GuessCtrl = new GuessCtrl();
    $_GuessCtrl->Ajax_微信支付json();
}

//调用微信支付接口，返回核心json
IF(@$_POST['type'] == 'weixinzhifu2')
{
    $price = $_POST["price"];
    $openid = $_SESSION["openid"]; 
    $stype = $_POST["stype"];
	 $cot = $_POST["cot"];
    $_GuessCtrl = new GuessCtrl();
    $_GuessCtrl->Ajax_流水记录和微信支付json($stype, $price, $openid,$cot);
}



IF(@$_POST["type"] == 'ChongXinTianJiaHongBao')
{
    //实例化
    $_GuessCtrl = new GuessCtrl();
    $order = $_POST["order"];
    $HongBaoJinE = $_POST["HongBaoJinE"];
    $HongBaoCount = $_POST["HongBaoCount"];
    $_GuessCtrl->Ajax_重新添加红包($order,$HongBaoJinE,$HongBaoCount);
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