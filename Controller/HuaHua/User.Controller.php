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
        //条件语句
        $mysql = sprintf("SELECT sum(price_count) as money FROM `question` where UNIX_TIMESTAMP() > expire_time AND uid = '%s' AND flag = '0' ",$this->Openid);
        //发送语句
        $arr =  $this->Sql->query($mysql);
        //获取过期金额
        $money = $arr[0]["money"];
        
        IF($money > 100)
        {
             //重置
             $this->Sql->reset();
             //数据结构
             $data["flag"] = "1";
             //条件语句
             $where = sprintf(" uid = '%s' ",$this->Openid);
             //发送语句
             $this->Sql->where($where)->save($data);

             //添加用户余额
             $this->Sql->table = 'user';
             //重置
             $this->Sql->reset();
             //条件语句
             $where =sprintf(" openid = '%s' ",$this->Openid) ;
             //发送语句
             $this->Sql->where($where)->sum('balance',$money);

             Lee::alert("你的题目过期了，这是你的退返金：".$money / 100);
        }
        
//         //以前的代码
//         IF($money > 100)
//         {
//              $XML = $this->wx_转账接口($money);
             
//              //解析XML
//              $XMLOBJ = simplexml_load_string($XML, 'SimpleXMLElement', LIBXML_NOCDATA);
              
//              IF($XMLOBJ->result_code == "SUCCESS")
//              {
//                  //重置
//                  $this->Sql->reset();
//                  //数据结构
//                  $data["flag"] = "1";
//                  //条件语句
//                  $where = sprintf(" uid = '%s' ",$this->Openid);
//                  //发送语句
//                  $this->Sql->where($where)->save($data);
                 
//                  //添加用户余额
//                  //选择表
//                  $this->Sql->table = 'user';
//                  //重置
//                  $this->Sql->reset();
//                  //条件语句
//                  $where =sprintf(" openid = '%s' ",$this->Openid) ;
//                  //发送语句
//                  $this->Sql->where($where)->sum('balance',$money);
                 
//                  Lee::alert("你的题目过期了，这是你的退返金：".$money);
//              }
//              else IF($XMLOBJ->result_code == "FAIL")
//              {
//                  //获取微信错误信息
//                  $msg = $XMLOBJ->err_code_des;
//                  //发送失败消息
//                 //Lee::alert("领取退返金失败，失败原因：".$msg);
//              }
//         }
        
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
        $mysql = sprintf("select *,if(bid <> '',3,type) AS realtype  from statements where uid = '%s' OR bid = '%s' AND flag = '1'",$this->Openid,$this->Openid);
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