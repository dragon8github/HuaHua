<?php
header("Content-type: text/html; charset=utf-8");
SESSION_START();

$data2 = json_decode(file_get_contents("Module/HuaHua/access_token.json"));
$access_token = $data2->access_token;
echo $access_token;
//创建菜单
function createMenu($data,$acc){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$acc);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }

    curl_close($ch);
    return $tmpInfo;

}

//获取菜单
function getMenu($acc){
    return file_get_contents("https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$acc);
}

//删除菜单
function deleteMenu($acc){
    return file_get_contents("https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$acc);
}




$data = '{"button":[{ "type": "view",  "name": "我也画", "url": "http://huahua.ncywjd.com/Home.php?p=list"},
                           	   { "type": "view", "name": "猜画有奖", "url": "http://huahua.ncywjd.com/Home.php?p=Maidan"},
                           	   { "name": "用户中心", "sub_button":[{"type":"view","name":"去提现","url": "http://huahua.ncywjd.com/Home.php?p=user"},{"type":"view","name":"我的作品","url": "http://huahua.ncywjd.com/Home.php?p=UserHistory"]}]}';


//$str_json = json_encode($data);



echo createMenu($data,$access_token);
?>