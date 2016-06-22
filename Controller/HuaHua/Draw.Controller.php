<?php 
header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class DrawCtrl
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
    
    public function Upload_上传图片()
    {
        $path = '/Upload/HuaHua/'.date("Ymd");
        Lee::mkFolder($_SESSION["APP_ROOT"].$path);
        $img = $_REQUEST['question_pic'];
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = $path .'/'.uniqid().'.jpeg'; 
        $success = file_put_contents($_SESSION["APP_ROOT"].$file, $data);
        if($success)
        {
             return  $_SESSION["STATIC_ROOT"].$file;
        }
        else
        {
            return null;
        }
    }
    
    
    
    public function Ajax_将画画的请求插入到数据库中()
    {
         $file =   $this->Upload_上传图片();
         
         if($file == null)
         {
             //图片上传失败
             $arr = array('Msg' => "图片上传失败","Result" => '','Status' => '失败');
             exit(json_encode($arr));
         }         
         
        //选择表
        $this->Sql->table = 'question';
        //重置
        $this->Sql->reset();
        //获取POST数据
        $data["uid"] = $this->Openid;
        $data["answer"] = $_GET["q"];
        $data["question_pic"] = $file;
        $data["price"] = '0';
        $data["price_count"] = '0';
        $data["hongbao_count"] = '0';
        $data["shengyu_count"] = '0';
        $data["release_time"] = time();
        $data["expire_time"] = strtotime("+24 hours");
        
       //发送语句,返回id
        $id =  $this->Sql->add($data); 
        //AJAX接受的信息
        $arr = array('Msg' => '图片生成成功！' , 'Result' => array('id' => $id) , 'Status' => '成功' );
        //返回为json
        exit(json_encode($arr)); 
    }   
}


//接受请求==================================


//提交画画
IF(@$_POST['type'] == "TiJiaoQuestion")
{
    $openid = $_SESSION["openid"];
    $_DrawCtrl = new DrawCtrl();
    $_DrawCtrl->Ajax_将画画的请求插入到数据库中();
}



?>