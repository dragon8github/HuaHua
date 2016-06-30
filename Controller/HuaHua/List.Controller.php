<?php

ini_set('date.timezone','Asia/Shanghai');




class ListCtrl
{
	 private  $Sql;	          //Mysql类的全局对象
	 
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
    		$where = sprintf("openid = '%s'  ",$this->Openid);
    		
    		//发送语句
    		$ret = $this->Sql->field("openid,wx_litpic")->where($where)->find();
    		//获取结果
    		$wx_litpic = $ret["wx_litpic"];  //微信头像（推广用户默认为空）
    		$openid = $ret["openid"];       //openid(推广用户不为空)
    		
    		IF($wx_litpic != "" && $openid != "")
    		{
    		    return true;
    		}
    		
    		return false;
    		
//     		//获取总数
//     		$count = $this->Sql->where($where)->getCount();
            
//     		if($count > 0)
//     		{
//     			return true;
//     		}
    		
//     		return false;
	}
	
	
	
	
	
	public function SET_用户($question,$myopenid,$name,$pic)
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
	        $this-> Insert_新增用户($question,$myopenid,$name,$pic);
	    }	  
	    //如果为推广用户
	    else if($openid != "" && $wx_litpic == "")
	    { 
	        $this-> Update_更新用户($question,$myopenid,$name,$pic);
	    }
	    else if($openid != "" && $wx_litpic != "")
	    {
	        if(@!file_get_contents($wx_litpic,0,null,0,1))
	        {
	          $this-> Update_更新用户($question,$myopenid,$name,$pic);
	        }
	    }
	}
	
	
	
	
	public function Update_更新用户($question,$openid,$name,$pic)
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
	    $data['update_time'] = time();                         //刷新冷却时间
	    $data['question'] = $question;                       //历史题库
	    //插入数据库
	    $this->Sql->where($where)->save($data);
	}
	
   
		
	public function get_获取随机的十条历史记录和这十条记录的id()
	{
    	     //题库表
    	     $this->Sql-> table = 'question_library';
    	     //随机抽取十条
    	     $arr =  $this->Sql->query("SELECT *,(select change_next_question_time from setting) as LengQueTime FROM question_library where id > 1000 ORDER BY RAND() LIMIT 10");
    	     //返回结果
    	    return $arr;
	}
	
	public function get_根据id返回指定的题库并且不包含发布过的历史($question)
	{
	    //还原配置,如where,order,limit,field
	    $this->Sql->reset();
	    //题库表
	    $this->Sql->table = 'question_library';
	    //条件语句
	    $where = sprintf(" id IN (%s) AND id NOT IN (SELECT distinct(answer) FROM question where  uid = '%s')",$question,$this->Openid);
	    //发送语句
	    $arr = $this->Sql->where($where)->select();
	    //返回结果
	    return $arr;
	}
	
	public function Insert_新增用户($question,$openid,$nickname,$headimgurl)
	{
    	    //选择数据库
    	    $this->Sql->table = 'user';
    	    //重置
    	    $this->Sql->reset();
    	    //数组对象
    	    $data['openid'] = $this->Openid;                      //微信号
    	    $data['wx_name'] = $nickname;                       //微信名称
    	    $data['wx_litpic'] = $headimgurl;                      //微信头像
    	    $data['balance'] = '';                                        //账户余额
    	    $data['register_time'] = time();                        //注册时间
    	    $data['update_time'] = time();                         //刷新冷却时间
    	    $data['question'] = $question;                       //历史题库
    	    //插入数据库
    	     $this->Sql->add($data);        
	}
	    
	public function Update_更新用户的历史题库和冷却时间($question)
	{
    	    //选择数据库
    	    $this->Sql->table = 'user';
    	    //还原配置,如where,order,limit,field
    	    $this->Sql->reset();
    	    //数组对象
	        $data["question"] = $question;                      //历史题库
	        $data["update_time"] = time();                      //冷却时间
    	    //条件语句
    	    $where = sprintf("openid = '%s' ",$this->Openid);
    	    //发送语句
    	    $this->Sql->where($where)->save($data);
	}
	
	//待优化sql
	private  function Get_剩余冷却的时间()
	{
	    //选择数据库
	    $this->Sql->table = 'user';
	    //获取与当前相减的差
	    $sql = sprintf(" SELECT TIMESTAMPDIFF( SECOND , FROM_UNIXTIME( update_time ) , NOW( )) - (SELECT change_next_question_time as S from setting) AS ShengYuTime FROM user WHERE openid = '%s' ",$this->Openid);
	    //发送语句
	    $ShengYuTime = $this->Sql->query($sql);
	    //返回结果
	    return $ShengYuTime[0]["ShengYuTime"];
	}
	
    
	
	
	public function Get_返回历史数据如果历史数据为空则返回随机数据并且更新到用户资料中()
	{
	    //获取剩余的冷却时间
	    $Time = $this->Get_剩余冷却的时间();
	    
	    //返回的题库列表
	    $arr = array();
	    
	    //还原配置,如where,order,limit,field
	    $this->Sql->reset();

	    //选择数据库
	    $this->Sql->table = 'user';
	    //条件语句
	    $where = sprintf("openid = '%s' ",$this->Openid);
	    //发送语句
	    $Result = $this->Sql->field('question')->where($where)->find();
	    //获取问题id集
	    $question =  $Result["question"];  //如：3,12,5,1,2,7,6,10,8,9
	    
	    if($question != '')
	    {
	        $arr =  $this->get_根据id返回指定的题库并且不包含发布过的历史($question);
	    }
	    else
	    {
	        //...历史数据为空，则生成新的数据并且更新到用户表中去
	        $arr =  $this->get_获取随机的十条历史记录和这十条记录的id();
	        //取id字符串集
	        $ids = Lee::get_获取数组中指定键的值按照逗号隔开返回($arr,"id");
	        //更新用户信息
	        $this->Update_更新用户的历史题库和冷却时间($ids);
	    }
	    
	    //将剩余冷却时间放入到数组中去
	    $arr["LengQueTime"] = $Time;
	    
	    //返回结果
	    return $arr;
	}
	
	
	
	public  function Get_根据冷却时间返回数据或者更新用户信息()
	{
	    //获取剩余的冷却时间
	    $Time = $this->Get_剩余冷却的时间();
	    
	    //还原配置,如where,order,limit,field
	    $this->Sql->reset();
        //返回的题库列表
	    $arr = array();
	    //选择数据库
	    $this->Sql->table = 'user';
	    //条件语句
	    $where = sprintf("openid = '%s' ",$this->Openid);
	    //发送语句
	    $Result = $this->Sql->field('question')->where($where)->find();
	    //获取问题id集
	    $question =  $Result["question"];  //如：3,12,5,1,2,7,6,10,8,9
	    
	    
	    
	    //$Time < 0 说明他冷却时间未完成，直接取上一次的历史展示即可（不包含已发布）
	    //$question == ''说明他可能是通过其他渠道注册然而没有获取过题目，所以应该重新获取
	    if($Time < 0 && $question != '')
	    {
	        //...冷却未完成，返回历史数据
	        $arr =  $this->get_根据id返回指定的题库并且不包含发布过的历史($question);
	    }
	    else 
	    {
	        //...冷却结束，取新的数据并且要不包含这一次数据和已发布的数据. 然后更新用户信息
	        //切换数据库
	        $this->Sql->table = 'question_library';
	        //还原配置,如where,order,limit,field
	        $this->Sql->reset();
	        //如果用户的上一次次历史记录为空的情况，就从数据库中随机取10条出来 
	        IF($question == '')
	        {
	           $arr =  $this->get_获取随机的十条历史记录和这十条记录的id();
	        }
	        else
	        {
	            //条件语句
	            $asql = sprintf(" SELECT 
										* 
									FROM 
										  question_library						
									WHERE	
										  id 
								   Not IN 
										  (%s) 
									  AND 
									      id 
								   NOT IN 
										  (SELECT distinct(answer) FROM question where uid = '%s')
								 order by 
										   rand() 
									limit 
										   10	  
								 ",$question,$this->Openid);
							    
	             
	            //根据id，获取随机并且不包含上一次历史题库的十条题并且未发布过的题目、
	            $arr  = $this->Sql->query($asql);
	        }
	        
	        //取id字符串集
	        $ids = Lee::get_获取数组中指定键的值按照逗号隔开返回($arr,"id");
	      
	        //更新用户信息
	        $this->Update_更新用户的历史题库和冷却时间($ids);
	    } 
	      
	    //将剩余冷却时间放入到数组中去
	    $arr["LengQueTime"] = $Time;  
	    //返回结果
	    return $arr;
	}
}


//接受请求==================================

 IF(@$_REQUEST["type"] == "refresh")
 {
     $_ListCtrl = new ListCtrl();
     $_ListCtrl->Get_根据冷却时间返回数据或者更新用户信息();
     exit();
 }
 ?>