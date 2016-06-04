<?php
/**
 * 	作用：格式化参数，签名过程需要使用
 */
function formatBizQueryParaMap($paraMap, $urlencode)
{
	//var_dump($paraMap);//die;
	$buff = "";
	ksort($paraMap);
	foreach ($paraMap as $k => $v)
	{
		if($urlencode)
		{
			$v = urlencode($v);
		}
		//$buff .= strtolower($k) . "=" . $v . "&";
		$buff .= $k . "=" . $v . "&";
	}
	$reqPar;
	if (strlen($buff) > 0)
	{
		$reqPar = substr($buff, 0, strlen($buff)-1);
	}
	//var_dump($reqPar);//die;
	return $reqPar;
}

/**
 * 	作用：生成签名
 */
function getSign($Obj)
{
	//var_dump($Obj);//die;
	foreach ($Obj as $k => $v)
	{
		$Parameters[$k] = $v;
	}
	//签名步骤一：按字典序排序参数
	ksort($Parameters);
	$String = formatBizQueryParaMap($Parameters, false);
	//echo '【string1】'.$String.'</br>';
	//签名步骤二：在string后加入KEY
	$String = $String."&key=udfsdfjkh36237asuhclo1j28yfuiash";
	//echo "【string2】".$String."</br>";
	//签名步骤三：MD5加密
	$String = md5($String);
	//echo "【string3】 ".$String."</br>";
	//签名步骤四：所有字符转为大写
	$result_ = strtoupper($String);
	//echo "【result】 ".$result_."</br>";
	//echo "**".$result_."**";
	return $result_;
}
