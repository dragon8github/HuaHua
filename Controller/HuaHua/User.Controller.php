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
        
        //调用支付接口
        IF($money > 1)
        {
             $XML = $this->wx_转账接口($money);
             
             //解析XML
             $XMLOBJ = simplexml_load_string($XML, 'SimpleXMLElement', LIBXML_NOCDATA);
              
             IF($XMLOBJ->result_code == "SUCCESS")
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
                 //选择表
                 $this->Sql->table = 'user';
                 //重置
                 $this->Sql->reset();
                 //条件语句
                 $where =sprintf(" openid = '%s' ",$this->Openid) ;
                 //发送语句
                 $this->Sql->where($where)->sum('balance',$money);
                 
                 Lee::alert("你的题目过期了，这是你的退返金：".$money);
             }
             else IF($XMLOBJ->result_code == "FAIL")
             {
                 //获取微信错误信息
                 $msg = $XMLOBJ->err_code_des;
                 //发送失败消息
                //Lee::alert("领取退返金失败，失败原因：".$msg);
             }
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
        $this->Sql->save($data);
        
        //选择表
        $this->Sql->table = 'statements';
        //重置
        $this->Sql->reset();
        //数据结构
        $data["id"] = uniqid();
        $data["type"] = "4";
        $data["price"] = $balance;
        $data["happen_time"] = time();
        $data["uid"] = $this->Openid;
        $data["flag"] = "1";
        //发送语句
        $this->Sql->add($data);
        
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
    
}


//接受请求==================================

if(@$_POST["type"] == "UserYuE")
{
    $_UserCtrl = new UserCtrl();
    $_UserCtrl->Ajax_获取用户余额();
}

?>