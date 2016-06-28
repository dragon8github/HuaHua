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
				  

		     $data2 = json_decode(file_get_contents("../Module/HuaHua/access_token.json"));
            $access_token = $data2->access_token;
	 	//$qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": "'.$id.'"}}}';
		
                  $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&next_openid=";
                  $result = https_post($url);
                  $jsoninfo = json_decode($result, true);
                  $data = $jsoninfo["data"]['openid'];
				  
				  for($i=0;$i<count($data);$i++)
				  {
				   echo $data[$i]."<br>";
				  }
				 
                 // $url2="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
                 // $imageInfo = downloadImageFromWeiXin($url2);
	
	// $filename = "ewm/".$id.".jpg";
					//  $local_file = fopen($filename, 'w');
					 // if (false !== $local_file){
						 // if (false !== fwrite($local_file, $imageInfo["body"])) {
	
							//  fclose($local_file);
							  //原始图像
	
						 // }
				//	  }

?>