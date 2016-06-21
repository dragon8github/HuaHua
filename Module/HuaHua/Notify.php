<?php
ini_set('date.timezone','Asia/Shanghai');
require_once     "../../Lib/wang/wxpay/lib/WxPay.Api.php";
require_once    "../../Lib/wang/wxpay/lib/WxPay.Notify.php";
require_once    "../../Lib/wang/wxpay/example/log.php";

class Price_Ctrl
{
    private  $Sql;	          
			 function __construct()
			{
				//引入核心sql类库
				include '../../Lib/Class/Mysql.class.php';              
			
				//引入数据库配置
				$dsn = include '../../Lib/Config/Sql.config.php';
					 
				
				//返回数据库对象
				$this->Sql =  Mysql::start($dsn);
			}
			
			
			
			
			
	
	 public function Ajax_重新添加红包($order,$HongBaoJinE,$HongBaoCount,$q)
    {
        
        //============================ 在经历bug攻击后，加入判断======================================
        //选择流水表
        $this->Sql->table = 'statements';
        //条件语句
        $where = sprintf(" id = '%s' AND type = '1' ",$order);
        //待获取结果
  			 $rett = $this->Sql->field("price,hongbao_price,hongbao_count,flag,Is_Use")->where($where)->find();  
        //获取用户充值完成的金额
        $rett_price = $rett["price"];                                      //用户充值的流水金额
        $rett_hongbao_price =  $rett["hongbao_price"];      //红包单价
        $rett_hongbao_count =  $rett["hongbao_count"];   //红包数量
        $rett_flag = $rett["flag"];                                         //是否充值完成
        $rett_Is_Use = $rett["Is_Use"];                                 //订单是否已经使用
        //用户提交的金额
        $rett_count= $HongBaoJinE * $HongBaoCount;
        
         
        //$rett_Is_Use如果为0，说明订单未使用
        if($rett_Is_Use == "0")
        {                           
                //修改更新标识
                //$data["flag"] = '1';   //(已更新版本为异步完成1)
                $data["Is_Use"] = "1"; 
                //发送语句
                $this->Sql->where($where)->save($data);
                
                
                //获取道具比例
                $DaoJuBiLi = $this->get_获取道具比例();
                
              
                //选择流水表
                $this->Sql->table = 'question';
                //重置
                $this->Sql->reset();        
                //数据结构
                $data2["price"] =$HongBaoJinE;
                $data2["price_count"] = $HongBaoJinE*$HongBaoCount;
                $data2["hongbao_count"] = $HongBaoCount;
                $data2["shengyu_count"] = $HongBaoCount;
                $data2["prop"] = $HongBaoJinE * floatval($DaoJuBiLi);  
                $data2["expire_time"] = strtotime("+24 hours");
                $data2["release_time"] = time();
                //$data2["model"] = $model;
                $data2["flag"] = '0';
                //条件语句
                $where = sprintf(" id = '%s' ",$q);
                //发送语句
                $this->Sql->where($where)->save($data2);
                
                
                //拼接json
               // $arr = array('Msg' => '添加成功！' , 'Result' =>"", 'Status' => '成功' );
                //返回结果
                //exit(json_encode($arr));
         }
         
        // exit();
    }
    
			public function get_获取道具比例()
			{
			   $this->Sql->table = "setting";
			   $this->Sql->reset();
			   $dd=$this->Sql->where("id=1")->select();
			   $prop =  @$dd[0]["prop"]; 
			   return $prop;
			}	
			
   
		
	
	
	
			    public function 修改订单号状态_update($out_trade_no)
				{
						//选择表
						$this->Sql->table = 'statements';
						//重置
						$this->Sql->reset();
						//条件语句
						$where = sprintf("id = '%s' ",$out_trade_no);
						 $data["flag"] = 1;
						//更新语句
		 				$this->Sql->where($where)->save($data);
						
						//根据订单号 判断类型进行相应修改
						
							$this->Sql->table = 'statements';
						$this->Sql->reset();
						$arr=$this->Sql->where($where)->find();
						
						//涉及支付的 TYPE 1跟7
						$type_n=$arr['type'];
						//涉及的金额
						$price=$arr['price']; 
						//用户OPENID
						 $openid=$arr['uid']; 
						 //涉及的画布ID
						$question_id=$arr['question_id']; 
						//Lee::WriteLog('222'); 
						if($type_n=='1')
						{	
							//充值
							/*
							
							需要用到的方法 
							
					
							*/
							//Lee::WriteLog('111');  
							$this->Ajax_重新添加红包($out_trade_no,$price,1,$question_id);
							
						
						}else if($type_n=='7')
						{
							//答题
						}
						
						//return $arr["balance"];
				}
	
			public function get_获取答题花销比例()
			{
				$this->Sql->table = "setting";
				$this->Sql->reset();
				$dd=$this->Sql->where("id=1")->select();
				$prop =  @$dd[0]["model_prop"];
				return $prop;
			}
			
			
	}
	


$up = new Price_Ctrl();

//初始化日志
//$logHandler= new CLogFileHandler($_SESSION["APP_ROOT"]."/Log/".date('Y-m-d').'.log');
//$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		//Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			global $up; 
			$up->修改订单号状态_update($result['out_trade_no']);
		//$result['out_trade_no'] 用户订单号   将用户订单号 状态值设置为1
			$fp=fopen('abc.txt', 'a');
			fputs($fp,$result['out_trade_no']);
			fclose($fp);
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		//Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}

//Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
