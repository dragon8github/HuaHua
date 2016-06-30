<?php 


function https_post($url, $data = null){
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
				  
 function downloadImageFromWeiXin($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        return array_merge(array('body' => $package), array('header' => $httpinfo));
    }
	
		$id='4';
		     $data2 = json_decode(file_get_contents("../Module/HuaHua/access_token.json"));
            $access_token = $data2->access_token;
	 	$qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": "'.$id.'"}}}';
                  $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$access_token";
                  $result = https_post($url,$qrcode);
                  $jsoninfo = json_decode($result, true);
                  $ticket = $jsoninfo["ticket"];

                  $url2="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
                  $imageInfo = downloadImageFromWeiXin($url2);
	
	 $filename = "ewm/".$id.".jpg";
					  $local_file = fopen($filename, 'w');
					  if (false !== $local_file){
						  if (false !== fwrite($local_file, $imageInfo["body"])) {
	
							  fclose($local_file);
							  //原始图像
	
						  }
					  }

?>