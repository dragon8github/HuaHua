<?php
/**
 * 公共函数库
 * 作者:咖啡兽兽   2015/05/11
 */

	/**
	 * 取文件配置项
     * @param String $name 要取的配置文件名前缀
     * @param String $key 要取的值
     * @return mixed
     */
	function C($name,$key = null){
		//尝试获取配置数组
		$conf = @include('./conf/'.$name.'.config.php');
		//判断是否取键值
		if(is_null($key)){
				return $conf;
		}
		//判断键是否存在
		if(is_null(@$conf[$key])){
			return false;
		}
		return $conf[$key];
	}
	
	/**
	 * 格式化输出数组
	 * @param Array $array 数组或对象
	 * @return void
	 */
	function dump($array){
		echo '<pre>';
		var_dump($array);
		echo '</pre>';
	}
	include('./class/Mysql.class.php');
	/**
	 * 创建数据库对象
	 * @param String $table 要操作的表名
	 * @return Object
	 */
	function M($table){
		$conn = C('sql');
		$conn['DB_TABLE'] = $table;
		return Mysql::start($conn);
	}
	
	/**
	 * 发起GET请求
	 * @param String $url 目标网填，带http://
	 * @return bool
	 */
	function httpGet($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Accept-Encoding: gzip, deflate'));
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 3);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	
	/**
     * Ajax方式返回数据到客户端
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
	function ajax_return($data,$type='JSON') {
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET['callback']) ? $_GET['callback'] : 'jsonpReturn';
                exit($handler.'('.json_encode($data).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);      
        }
    }
	
	/**
	 * 获取客户端IP地址
	 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @return mixed
	 */
	function get_client_ip($type = 0) {
	    $type       =  $type ? 1 : 0;
	    static $ip  =   NULL;
	    if ($ip !== NULL) return $ip[$type];
	    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	        $pos    =   array_search('unknown',$arr);
	        if(false !== $pos) unset($arr[$pos]);
	        $ip     =   trim($arr[0]);
	    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
	    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
	        $ip     =   $_SERVER['REMOTE_ADDR'];
	    }
	    // IP地址合法验证
	    $long = sprintf("%u",ip2long($ip));
	    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	    return $ip[$type];
	}
	
	/**
	 * XML编码
	 * @param mixed $data 数据
	 * @param string $root 根节点名
	 * @param string $item 数字索引的子节点名
	 * @param string $attr 根节点属性
	 * @param string $id   数字索引子节点key转换的属性名
	 * @param string $encoding 数据编码
	 * @return string
	 */
	function xml_encode($data, $root='data', $item='item', $attr='', $id='id', $encoding='utf-8') {
	    if(is_array($attr)){
	        $_attr = array();
	        foreach ($attr as $key => $value) {
	            $_attr[] = "{$key}=\"{$value}\"";
	        }
	        $attr = implode(' ', $_attr);
	    }
	    $attr   = trim($attr);
	    $attr   = empty($attr) ? '' : " {$attr}";
	    $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
	    $xml   .= "<{$root}{$attr}>";
	    $xml   .= data_to_xml($data, $item, $id);
	    $xml   .= "</{$root}>";
	    return $xml;
	}

	/**
	 * 数据XML编码
	 * @param mixed  $data 数据
	 * @param string $item 数字索引时的节点名称
	 * @param string $id   数字索引key转换为的属性名
	 * @return string
	 */
	function data_to_xml($data, $item='item', $id='id') {
	    $xml = $attr = '';
	    foreach ($data as $key => $val) {
	        if(is_numeric($key)){
	            $id && $attr = " {$id}=\"{$key}\"";
	            $key  = $item;
	        }
	        $xml    .=  "<{$key}{$attr}>";
	        $xml    .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
	        $xml    .=  "</{$key}>";
	    }
	    return $xml;
	}
?>