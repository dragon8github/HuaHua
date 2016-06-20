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
    
    public function If_判断是否画主重新打开这个页面($q,$openid)
    {
        //选择表
        $this->Sql->table = 'question';
        //条件语句
        $query = sprintf("SELECT *,b.answer FROM `question` AS a JOIN question_library  AS b ON b.id = a.answer WHERE uid = '%s' AND a.answer = '%s'",$openid,$q);
        //发送语句
        $arr = $this->Sql->query($query);
        //返回结果
        return @$arr[0];
    }
    
    public function Ajax_流水记录和微信支付json($stype,$name1,$money,$openid)
    {
        $orderid = WxPayConfig::MCHID.uniqid();   //订单号
        
        //选择表
        $this->Sql->table = 'statements';
        //数据结构
        $data["id"] = $orderid;
        $data["type"] = $stype;
        $data["price"] = $money;
        $data["happen_time"] = time();
        $data["uid"] = $openid;
        $data["question_id"] = $_GET['q'];
        
        //发送语句
        $this->Sql->add($data);
        $ko=new WX_INT(); 
        $jsApiParameters=$ko->Jspay($name1,$name1,$money,"http://huahua.ncywjd.com/Module/HuaHua/Notify.php",$openid,$orderid);
        //exit($jsApiParameters);
        //AJAX接受的信息
        $arr = array('Msg' => '请求成功！' , 'Result' => array('order' => $orderid, 'wxjson' => $jsApiParameters) , 'Status' => '成功' );
        //返回为json
        exit(json_encode($arr));
        
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
    
    
    
    
    public function Get_生成tips答案提示()
    {
        //选择表
        $this->Sql->table = 'question_library';
        //重置
        $this->Sql->reset();
        //条件语句
        $where = sprintf("id = '%s' ",$_GET["q"]);
        //发送语句
        $arr = $this->Sql->where($where)->find();
                      
        if($arr["tips"] == null || $arr["tips2"] == null)
        {
               $txt = file_get_contents(dirname(__FILE__)."/a.txt");
               $len = mb_strlen($txt,'utf-8');
               $word = $_GET["word"];
               $tips1  = mb_substr($word, 0,1,"utf-8") . "," .mb_substr($word, 1,1,"utf-8").","; 
               $tips2  = mb_substr($word, 2,1,"utf-8") . "," .mb_substr($word, 3,1,"utf-8").","; 
               for($i = 0;$i< 40;$i++)
               {            
                   $rand =  rand(0, $len - 1);   
                   $word = mb_substr($txt, $rand,1,"utf-8");
                   if($i<=20)
                   {
                       $tips1 .= $word . ",";
                   }
                   else
                   {
                       $tips2.=$word . ",";
                   }
               }       
               
                $tips1 = Lee::shuffle_打散并且洗牌字符串($tips1); 
                $tips2 =Lee::shuffle_打散并且洗牌字符串($tips2);
                
        
                //选择表
                $this->Sql->table = 'question_library';
                //重置
                 $this->Sql->reset();
                //条件语句
                $where = sprintf("id = '%s' ",$_GET["q"]);
                //数据结构
                $data["tips"] = $tips1;
                $data["tips2"] = $tips2;
                $data["release_time"] = time();
                //发送语句
                $this->Sql->where($where)->save($data);
        }
    }
    
    
    //应该加入机制防止重复提交。比如一天内，相同的openid和相同的题目id 就无法重复提交
    //还得判断$_GET["q"];是否存在，如果不存在说明非法操作。
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
    
    
    
    public  function Ajax_更新画画的数据($id,$price_count,$hongbao_count,$prop,$order)
    {
	
	//******************************************ko调整用户填写的红包金额为 单个红包金额
        //选择表
        $this->Sql->table = "question";
        //条件语句
        $where = sprintf("id = '%s' ",$id);
        //生成数据
       // $price = round($price_count / $hongbao_count,1);
        $data["price"] = $price_count;
        $data["price_count"] = $price_count*$hongbao_count;
        $data["hongbao_count"] = $hongbao_count;
        $data["shengyu_count"] = $hongbao_count;
        $data["prop"] =$prop;
        //sql语句发送
        $this->Sql->save($data);
        
       //更新流水记录
       $this->Update_更新流水状态($order, '1');
        
        //AJAX接受的信息
        $arr = array('Msg' => '请求成功！' , 'Result' => '' , 'Status' => '成功' );
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


//更新question
IF(@$_POST['type'] == 'UpdateQuestionWithId')
{
    $id = $_POST["id"];                                                              //id
    $price_count = $_POST["price_count"];                                //总金额
    $hongbao_count = $_POST["hongbao_count"];                   //红包总数
    $prop = $_POST["prop"];                                                     //道具汇率
    $order = $_POST["order"];                                                   //订单号
    $_DrawCtrl = new DrawCtrl();                                                
    $_DrawCtrl->Ajax_更新画画的数据($id, $price_count, $hongbao_count,$prop,$order);
}


//调用微信支付接口，返回核心json
IF(@$_POST['type'] == 'weixinzhifu')
{
    $name = $_GET["word"];
    $price = $_POST["price"];
    $openid = $_SESSION["openid"];
    $stype = $_POST["stype"];
    $_DrawCtrl = new DrawCtrl();
    $_DrawCtrl->Ajax_流水记录和微信支付json($stype,$name, $price, $openid);
}
?>