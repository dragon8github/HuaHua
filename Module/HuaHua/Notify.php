<?php
ini_set('date.timezone','Asia/Shanghai');


require_once     "../../Lib/wang/wxpay/lib/WxPay.Api.php";
require_once    "../../Lib/wang/wxpay/lib/WxPay.Notify.php";
require_once    "../../Lib/wang/wxpay/example/log.php";
class Price_Ctrl{
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
						
						//return $arr["balance"];
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
