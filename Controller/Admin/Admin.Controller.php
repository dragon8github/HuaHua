<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class AdminCtrl
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
    

    public function get_根据question_id获取画主的姓名($q)
    {
        //选择表
        $this->Sql->table = 'question';
        //发送语句
        $rett =   $this->Sql->query(sprintf("SELECT B.wx_name AS name FROM question AS A JOIN `user` AS B ON uid = openid WHERE A.id = '%s' LIMIT 1",$q));
        return $rett[0]["name"];
    }
    
    	public	function https_post($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }	
    public function Send_ALLUser($question_id)
    {
        $wx_name =$this->get_根据question_id获取画主的姓名($question_id);
        
        //选择表
        //$this->Sql->table = 'user';
        //重置
       // $this->Sql->reset();
        //发送语句
       // $ret = $this->Sql->field("openid")->select();
        
        $data2 = json_decode(file_get_contents($_SESSION["APP_ROOT"]."/Module/HuaHua/access_token.json"));
        $access_token = $data2->access_token;
        $website="http://huahua.ncywjd.com/home.php?p=guess&q=".$question_id;
        
		  
				  
	 	//$qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": "'.$id.'"}}}';
		
                  $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&next_openid=";
                  $result = $this->https_post($url);
                  $jsoninfo = json_decode($result, true);
                  $ret = $jsoninfo["data"]['openid'];
		
        $message = sprintf("点击本链接立即抢答画家【%s】的灵魂作品",$wx_name);
        $wx = new WX_INT();        
        for($i = 0;$i<count($ret);$i++)
        {
            $openid = $ret[$i];
            $wx->SendMessage("小编向你推荐了一幅相当有诚意的作品\r\n", $website, $message, $access_token, $openid);
        }  
        
     //   $openid = "oYNn6wi2Lg4qvvQDOFFTMXpY6ulY";
     //   $wx->SendMessage("小编向你推荐了一幅相当有诚意的作品\r\n", $website, $message, $access_token, $openid);
        
        exit();
    }
    
    
    public function get_所有信息()
    {
	
		$now_time=time();
        //选择表
        $this->Sql->table = 'question';
        //条件语句
        $mysql = sprintf("
                                SELECT 
                                                mysum,
                                                C.answer,                                                
                                                D.openid,
                                                D.wx_name,
                                                D.wx_litpic,
                                                B.question_pic,
                                                B.release_time,
                                                B.model, 
                                                (B.shengyu_count * B.price) AS shengyujine,
                                                B.id AS AID,
                                                D.balance AS money_balance

                                FROM 
                                                  statements  AS A
                              LEFT  JOIN
                                                 (SELECT uid,sum(price) as mysum FROM statements where type = '4' GROUP BY uid) AS SUM
                                 on
                                        A.uid = SUM.uid                        
                                JOIN 
                                        question AS B 
                                ON 
                                        A.question_id = B.id
                                JOIN 
                                        question_library AS C 
                                ON 
                                        B.answer = C.id
                                JOIN 
                                        `user` AS D 
                                ON 
                                        D.openid = B.uid
                                WHERE 
								
									B.expire_time>'$now_time'
									AND
                                        A.flag = '1' 
                                AND 
                                        A.type = '1'
                               AND 
                                        B.shengyu_count * B.price!= 0
                          ORDER BY 
                            ");  
        
       $desc = "B.release_time   DESC";
      
       if(@$_GET["orderby"] == "descmoney")
       {
           $desc = "shengyujine DESC";
       }
       elseif (@$_GET["orderby"] == "desctime")
       {
           $desc = "B.release_time DESC";
       }
       
       $mysql .= $desc;
        
        //发送语句
        return $this->Sql->query($mysql);
    }
    
    
    public function update_更新model($question_id,$model)
    {
        //选择表
        $this->Sql->table = 'question';
        //更新表
        $data["model"] = $model;
        //条件语句
        $where =sprintf(" id = '%s'  ",$question_id);
        //更新语句
        $this->Sql->where($where)->save($data);
        //返回数据
        exit("更新成功");        
    }
    
}


//接受请求==================================


//接受请求==================================
IF(@$_POST["type"] == "Send_ALLUser")
{
    $admin = new AdminCtrl();
    $q = $_POST["q"];
    $admin->Send_ALLUser($q);
}

IF(@$_POST["type"] == "update")
{
    
    $question_id = $_POST["question_id"];
    
    $model = $_POST["model"];
    
    $_ctrl = new AdminCtrl();
    
    $_ctrl->update_更新model($question_id,$model);
}
?>