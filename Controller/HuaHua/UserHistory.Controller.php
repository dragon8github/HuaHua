<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class UserHistoryCtrl
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
        else if($openid != "" && $wx_litpic != "")
        {
            if(@!file_get_contents($wx_litpic,0,null,0,1))
            {
                $this-> Update_更新用户($myopenid,$name,$pic);
            }
        }
    }
    
    
    
    public function get_获取用户余额()
    {
        //选择表
        $this->Sql->table = 'user';
         //条件语句
        $where = sprintf("openid = '%s' ",$this->Openid);
        //发送语句
        $arr =  $this->Sql->field("balance")->where($where)->find();
        //返回余额
        return $arr["balance"];
        
    }
    
    public function get_获取分享列表()
    {
        //选择表
        $this->Sql->table = 'question';
        //条件语句
        $mysql = sprintf("SELECT a.id,question_pic,b.answer,a.release_time FROM question AS a JOIN question_library AS b ON a.answer = b.id WHERE a.uid =  '%s' ORDER BY release_time DESC",$this->Openid) ;
         
        //发送语句
        return $this->Sql->query($mysql);
    }
    
}


//接受请求==================================

?>