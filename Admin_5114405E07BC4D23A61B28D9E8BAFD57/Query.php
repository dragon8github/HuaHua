<?php

SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Lib/Class/Log.class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/Admin/Query.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库

 

//页面逻辑 开始==============================================



//回答明细列表
$answer_details_arr = QueryCtrl::$answer_details_arr;
//出题列表
$question_arr = QueryCtrl::$question_arr;
//流水列表
$statements_arr = QueryCtrl::$statements_arr;
//用户信息列表
$user_arr = QueryCtrl::$user_arr;
//获取用户生成的提示列表
$tips_arr = QueryCtrl::$Tips_arr;




?>



<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title></title>
	<link href="https://cdn.bootcss.com/bootstrap/3.3.6//css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php 
CssLoader::autoComplete();
?>

<form class="form-inline"  method = "post" action="?action=query" style=" margin: 10px; text-align: center;">
      <div class="form-group">
        <label class="sr-only" for="exampleInputAmount">Amount (in dollars)</label>
        <div class="input-group">
          <input type="text" class="form-control" style="width:270px;" name="openid" id="exampleInputAmount"  placeholder="<?php echo trim(@$_POST["openid"]); ?>">
        </div>
      </div>
      <button type="submit" class="btn btn-primary" data-role = 'none'>Query</button>
</form>


<ul id="myTab" class="nav nav-tabs">
   <li class="active"> <a href="#statements" data-toggle="tab"> 流水表statements </a> </li>
   <li><a href="#user" data-toggle="tab">用户信息user</a></li>
   <li><a href="#question" data-toggle="tab">画画表question</a></li>
   <li><a href="#answer" data-toggle="tab">答题表answer</a></li>
    <li><a href="#tips" data-toggle="tab">道具提示表daojuinfo</a></li>
</ul>







    
<div id="myTabContent" class="tab-content">

   <div class="tab-pane fade in active" id="statements">
                                  
                            <table class="table table-hover table-bordered" style="vertical-align: middle">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>订单号</th>  
                                      <th>题目id</th>
                                      <th>类型</th>
                                      <th>价格</th>
                                      <th>发生时间</th>        
                                      <th>支付标识flag</th>    
                                      <th>用户Openid</th> 
                                      <th>受益者Openid</th>
                                      <th>发生时的余额</th> 
                                      <th>该订单是否已使用</th>      
                                    </tr>
                                  </thead>
                                  <tbody>
                                   <?php 
                                        for($i = 0;$i<count($statements_arr);$i++)
                                        {
                                            $id = $statements_arr[$i]["id"];
                                            $question_id = $statements_arr[$i]["question_id"];
                                            $type = $statements_arr[$i]["type"];
                                            $price = $statements_arr[$i]["price"];
                                            $happen_time = date('Y-m-d H:i:s',$statements_arr[$i]["happen_time"]);
                                            $flag = $statements_arr[$i]["flag"];
                                            $uid = $statements_arr[$i]["uid"]; 
                                            $bid = $statements_arr[$i]["bid"];
                                            $balance = $statements_arr[$i]["balance"];
                                            $Is_Use = $statements_arr[$i]["Is_Use"];
                                    ?>
                                    <tr <?php if(($type == "7" || $type == "1") && $flag == "1" && $Is_Use == "0") { echo "class='warning'"; } ?>>                 
                                          <td><?php echo $i; ?></td> 
                                          <td><?php echo $id; ?></td> 
                                           <td><?php echo $question_id; ?></td>  
                                            <td><?php echo $type; ?></td>  
                                             <td><?php echo $price; ?></td>  
                                              <td><?php echo $happen_time; ?></td>  
                                               <td><?php echo $flag; ?></td>  
                                                <td><?php echo $uid; ?></td>  
                                                 <td><?php echo $bid; ?></td>  
                                                  <td><?php echo $balance; ?></td>  
                                                   <td><?php echo $Is_Use; ?></td>                
                                    </tr>
                                  <?php } ?>
                                  </tbody>
                                </table>
                                
   </div>
   
   <div class="tab-pane fade" id="user">
                	<table class="table table-hover table-bordered" style="vertical-align: middle">
                  <thead>
                    <tr>
                      <th>Openid</th>  
                      <th>微信名</th>
                      <th>头像</th>
                      <th>余额</th>  
                    </tr>
                  </thead>
                  <tbody>
                   <?php 
                        for($i = 0;$i<count($user_arr);$i++)
                        {
                            $openid = $user_arr[$i]["openid"];
                            $wx_name = $user_arr[$i]["wx_name"];
                            $wx_litpic = $user_arr[$i]["wx_litpic"];
                            $balance = $user_arr[$i]["balance"];
                           
                    ?>
                    <tr>                 
                           <td><?php echo $openid; ?></td>  
                           <td><?php echo $wx_name; ?></td>  
                           <td><?php echo $wx_litpic; ?></td>  
                           <td> <input type="text"  style="border:0px;background:transparent;" id="user_balance"  data-username = "<?php echo $wx_name ?>"  data-openid = "<?php echo $openid; ?>"  value="<?php echo $balance; ?>"></td>                            
                    </tr>
                  <?php } ?>
                  </tbody>
                </table>
   </div>
   
   <div class="tab-pane fade" id="question">
                            		<table class="table table-hover table-bordered" style="vertical-align: middle">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>question_id</th>  
                                      <th>openid</th>
                                      <th>答案</th>
                                      <th>题目图片</th>
                                      <th>单价</th>        
                                      <th>总价</th>    
                                      <th>发布时间</th> 
                                      <th>过期时间</th>
                                      <th>flag</th> 
                                      <th>红包数量</th>     
                                      <th>剩余红包</th>      
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                        for($i = 0;$i<count($question_arr);$i++)
                                        {
                                            $id = $question_arr[$i]["id"];
                                            $uid = $question_arr[$i]["uid"];
                                            $answer1 = $question_arr[$i]["answer1"];
                                            $question_pic = $question_arr[$i]["question_pic"];
                                            $price = $question_arr[$i]["price"];
                                            $price_count = $question_arr[$i]["price_count"];
                                            $release_time = date('Y-m-d H:i:s',$question_arr[$i]["release_time"]); 
                                            $expire_time = date('Y-m-d H:i:s',$question_arr[$i]["expire_time"]);
                                            $flag = $question_arr[$i]["flag"];
                                            $hongbao_count = $question_arr[$i]["hongbao_count"];
                                            $shengyu_count = $question_arr[$i]["shengyu_count"];
                                    ?>
                                    <tr>                 
                                          <td><?php echo $i; ?></td> 
                                          <td><?php echo $id; ?></td> 
                                          <td><?php echo $uid; ?></td> 
                                          <td><?php echo $answer1; ?></td> 
                                          <td><?php echo $question_pic; ?></td> 
                                          <td><?php echo $price; ?></td> 
                                          <td><?php echo $price_count; ?></td> 
                                          <td><?php echo $release_time; ?></td> 
                                          <td><?php echo $expire_time; ?></td> 
                                          <td><?php echo $flag; ?></td> 
                                         <td><?php echo $hongbao_count; ?></td> 
                                         <td><?php echo $shengyu_count; ?></td> 
                                    </tr>
                                  <?php } ?>
                                  </tbody>
                                </table>
	
   </div>
   
     <div class="tab-pane fade" id="answer">
		<table class="table table-hover table-bordered" style="vertical-align: middle">
      <thead>
        <tr>
          <th>#</th>
          <th>题目id</th>
          <th>用户名</th>
          <th>是否答对</th>
          <th>发生时间</th>        
          <th>提交内容</th>    
          <th>是否有红包</th>  
        </tr>
      </thead>
      <tbody>
          <?php 
            for($i = 0;$i<count($answer_details_arr);$i++)
            {
                $question_id = $answer_details_arr[$i]["question_id"];
                $user_id = $answer_details_arr[$i]["user_id"];
                $flag = $answer_details_arr[$i]["flag"];
                $answer_time = date('Y-m-d H:i:s',$answer_details_arr[$i]["answer_time"]);
                $content = $answer_details_arr[$i]["content"];
                $Is_Hongbao = $answer_details_arr[$i]["Is_Hongbao"]; 
        ?>
        <tr>                 
              <td><?php echo $i; ?></td> 
               <td><?php echo $question_id; ?></td>  
                <td><?php echo $user_id; ?></td>  
                 <td><?php echo $flag; ?></td>  
                  <td><?php echo $answer_time; ?></td>  
                <td><?php echo $content; ?></td>  
                 <td><?php echo $Is_Hongbao; ?></td>           
        </tr>
      <?php } ?>
      </tbody>
    </table>
   </div>
   
   
   <div class="tab-pane fade" id="tips">
   		<table class="table table-hover table-bordered" style="vertical-align: middle">
      <thead>
        <tr>
          <th>#</th>
          <th>openid</th>
          <th>question_id</th>
          <th>提示词语</th>
          <th>词语索引</th>        
        </tr>
      </thead>
      <tbody>
          <?php 
            for($i = 0;$i<count($tips_arr);$i++)
            {
                $question_id = $tips_arr[$i]["openid"];
                $user_id = $tips_arr[$i]["question_id"];
                $content = $tips_arr[$i]["tips"];
                $flag = $tips_arr[$i]["word_index"];               
        ?>
        <tr>                 
              <td><?php echo $i; ?></td> 
               <td><?php echo $question_id; ?></td>  
               <td><?php echo $user_id; ?></td>  
               <td><?php echo $content; ?></td>  
               <td><?php echo $flag; ?></td>  
        </tr>
      <?php } ?>
      </tbody>
    </table>
   </div>

</div>


	<?php
    	JsLoader::Jquery();    //加载jquery
    	JsLoader::autoComplete(); 
    	JsLoader::Layer();
	?>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</body>
</html>


<script>
$(function() 
{

	$('#exampleInputAmount').autoComplete({
        minChars: 1,
        source: function(term, suggest)
        {
            term = term.toLowerCase();
            $.getJSON('?type=autoComplete', { q:term }, function(data){  
            	//console.log(JSON.stringify(data));
            	console.log(data);  
            });
            /*
            var choices = ['ActionScript', 'AppleScript', 'Asp', 'Assembly', 'BASIC', 'Batch', 'C', 'C++', 'CSS', 'Clojure', 'COBOL', 'ColdFusion', 'Erlang', 'Fortran', 'Groovy', 'Haskell', 'HTML', 'Java', 'JavaScript', 'Lisp', 'Perl', 'PHP', 'PowerShell', 'Python', 'Ruby', 'Scala', 'Scheme', 'SQL', 'TeX', 'XML'];
            var suggestions = [];
            for (i=0;i<choices.length;i++)
                if (~choices[i].toLowerCase().indexOf(term)) suggestions.push(choices[i]);
            suggest(suggestions);
            */
        }
    });


    
	
	$("#user_balance").bind("keydown", function(e) 
	{
		var theEvent = e || window.event;
		var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
		if (code == 13) {
			update_balance();
		}
	})
	
})


update_balance = function()
{
	var v = $("#user_balance").val();
	var id = $("#user_balance").attr("data-openid");
	var username = $("#user_balance").attr("data-username");
	layer.open({
		title: "信息",
		content: "你确定更新openid为 :" + id + " <br /> 用户名为：" + username + " <br /> 更新余额为：" + v,
		btn: ["确定", "取消"],
		yes: function() {
			layer.load(0, {
				shade: [0.8, '#fff'],
				time: 10 * 1000
			});
			$.ajax({
				type: "post",
				data: {
					type: "update_balance",
					openid: id,
					balance: v
				},
				success: function(data) {
					layer.msg("修改完成")
				}
			}) 
			layer.closeAll();
		}
	})
}
	</script>
