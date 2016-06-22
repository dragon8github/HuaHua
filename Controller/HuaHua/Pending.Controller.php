<?php 

ini_set('date.timezone','Asia/Shanghai');

//控制器专用累
class PendingCtrl
{
    private  $Sql;	          //该sql类的全局对象
    
    private $Openid;      //用户微信号
    
    function __construct()
    {
        //引入核心sql类库
        include $_SESSION["APP_ROOT"].'/Lib/Class/Mysql.class.php';
    
        //引入数据库配置
        $dsn = include $_SESSION["APP_ROOT"].'/Lib/Config/Sql.config.php';
        	
        //返回数据库对象
        $this->Sql =  Mysql::start($dsn);
    }
    
    public function get_获取真实正确的需要提现的数据($openid)
    {
        //选择表
        $this->Sql->table = 'statements';
        //条件语句
        $mysql = sprintf("
                                    SELECT
                            					sum(price) / 100 as user_balance
                            		FROM
                            					statements
                            		where
                            					happen_time >(SELECT happen_time from statements where type = '4' and uid = '%s' order by happen_time desc limit 1)
                            			AND
                            					flag = '1'
                            			AND
                            					(
                                                        uid = '%s' AND (type = '5' or type = '6' or type = '8')
                                    			OR
                                    					bid = '%s' AND type = '7'
                                                )
                            ",$openid,$openid,$openid);
    
        //发送语句
        $rett=  $this->Sql->query($mysql);
    
        return $rett[0]["user_balance"];
    }
    
    public function get_所有信息()
    {
        //选择表
        $this->Sql->table = 'statements';
        //条件语句
        $mysql = sprintf(" 
                                  	SELECT 
                                                    A.id AS orderid,
                                					A.price AS shenqing_balance,
                                					A.happen_time,
                                					A.uid,
                                                    B.balance AS user_balance,
                                					B.wx_litpic,
                                					B.wx_name 
                            		FROM 
                                					statements AS A 
                                left JOIN 
                                					`user` AS B 
                            			ON 
                                					A.uid = B.openid 
                        		  WHERE 
                                					type = '4' 
                        			  AND 
                                					flag = '0'
                                 ");
        
        //发送语句
        return $this->Sql->query($mysql);
    }
        
    
    public function clear_balance($openid)
    {
        //选择表
        $this->Sql->table = 'user';        
        //数据结构
        $data["balance"] = "0";        
        //条件语句
        $where =  sprintf(" openid = '%s' ",$openid);       
        //发送语句
        $this->Sql->where($where)->save($data);   
        //中止
        exit();
    } 
    
    public function wx_转账接口($openid,$money)
    {
        $ko=new WX_INT();
        $XML =  $ko->Zhifu($money,$openid);
        return $XML;
    }
    
    
    //这里还需要调用微信支付
    public function Add_balance($orderid,$openid,$balance)
    {   
        $balance = $balance * 100;
        
        //...调用转账接口
        $XML = $this->wx_转账接口($openid,$balance);
         
        //解析XML
        $XMLOBJ = simplexml_load_string($XML, 'SimpleXMLElement', LIBXML_NOCDATA);
         
        //是否XML
        IF($XMLOBJ->result_code == "SUCCESS")
        {
                //选择表
                $this->Sql->table = 'statements';
                //重置 
                $this->Sql->reset();
                //条件语句
                $where =  sprintf(" id = '%s' ",$orderid);
                //更新标识
                $mydata["flag"] = "1"; 
                //更新数据 
                $this->Sql->where($where)->save($mydata); 
                
                
                
                
                
                //获取微信错误信息
                $msg = $XMLOBJ->err_code_des;
                //拼接数组
                $arr = array('Msg' => "审核成功" , 'Result' => '' , 'Status' => '成功' );
                //返回为json
                exit(json_encode($arr));
                
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
        
        exit();
    } 
}
 

//接受请求==================================
IF(@$_POST["type"] == "clear_balance")
{
    $_PendingCtrl = new PendingCtrl();
    $openid = $_POST["openid"];
    $_PendingCtrl->clear_balance($openid);
}


IF(@$_POST["type"] == "Add_balance")
{
    $_PendingCtrl = new PendingCtrl();
    $openid = $_POST["openid"];
    $balance = $_POST["balance"];    
    $orderid = $_POST["orderid"];
    IF($balance <= 0)  exit(); 
    $_PendingCtrl->Add_balance($orderid,$openid,$balance);
}
?>