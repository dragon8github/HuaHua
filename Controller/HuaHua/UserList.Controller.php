<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class UserListCtrl
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
                $Reuslt = "提现 +";
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

?>