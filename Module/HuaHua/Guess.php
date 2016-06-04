<?php 
SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Lib/Class/Log.class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Guess.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库


//公共变量 开始==============================================

$openid = $_SESSION["openid"];                       //openid
$nickname = $_SESSION["nickname"];              //昵称
$headimgurl = $_SESSION["headimgurl"];        //头像
mylog::WriteLog("获取一下头像:".$headimgurl,"Guess.php");
$q = $_GET["q"];                                               //题目编号
$IsDrawer= false;                                              //是否画主本人
$Time = "0";                                                      //冷却时间

//微信类 开始==============================================
$ko=new WX_INT();
$signPackage = $ko->GetSignPackage();   //获取分享接口 相关信

//页面逻辑 开始==============================================
$_GuessCtrl = new GuessCtrl();

//显示图片信息
$arr = $_GuessCtrl->get_根据ID获取画画信息();
$question_pic = $arr["question_pic"];                                  //图片路径
$price = $arr["price"];                                                         //单价
$price_count = $arr["price_count"];                                     //总价
$expire_time_unix = $arr["expire_time"];                            //过期时间的时间戳，js有用
$expire_time = date('Y-m-d H:i:s',$expire_time_unix);         //过期时间
$flag = $arr['flag'];                                                            //是否过期
$hongbao_count = $arr["hongbao_count"];                      //红包个数
$shengyu_count = $arr["shengyu_count"];                        //红包余额
$prop = $arr["prop"];                                                       //道具比例
$daoju =$prop;                                                               //道具价格
//$Title =$arr["wx_name"]." - 成语作品";                                                  //设置页面Title
$Title ="看图猜成语";

//判断openid是否存在用户表，没有的话先插入
if(!$_GuessCtrl->Openid是否存在用户表中())
{
    $_GuessCtrl->Insert_新增用户($openid,$nickname,$headimgurl);   //添加到数据库
    
}
else 
{
    $IsDrawer = $_GuessCtrl->Is_是否是画主($q);
    $IsReal = $_GuessCtrl->Is_是否用户已经回答正确过();
    if(!$IsDrawer)
    {
        $Time = $_GuessCtrl->get_距离下一次答题的时间();
    } 
}


$_GuessCtrl->Add_插入访客($openid,$headimgurl,$nickname);

$Is_Out =  $_GuessCtrl->红包是否无剩余或者过期();      //返回true即是过期

$answerList = $_GuessCtrl->get_answerList();

$piclist = $_GuessCtrl->get_headpic();

//判断tips状态
//$tips_row=$_GuessCtrl->get_tip_rows();
//获取tips
//$tips_arr=$_GuessCtrl->get_tips();




$tips_word = $_GuessCtrl->get_tips_for_word();

$prop = $_GuessCtrl->get_获取道具比例();




?>



<?php 	include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php';?>

<?php 
       CssLoader::Jqm();      //加载jqm.css
 ?>
	
 <style type="text/css">
.ui-body-a img { border-radius: 25px; -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.5); -moz-box-shadow: inset 0 1px 5px rgba(0,0,0,.5); box-shadow: inset 0 1px 1px rgba(0,0,0,.5); }
.ui_ko span { margin:0 3px; float:left;}
#submit{background-color:#2ED146;color:#FFF;max-width:45%;}
#share_hy{background-color:#3aa7ff;color:#FFF;max-width:45%}
#search{border-radius:.3125em}
#clsm_k,#cyts_k,#szw_k,#zhuyi,#clsm_k2{border-top-left-radius:.3125em;border-top-right-radius:.3125em}
#panelbody,#panelbody2,#pann_k,#pnnn_k,#pnnn_k2{border-bottom-left-radius:.3125em;border-bottom-right-radius:.3125em}
#radio-choice-0a,#reputHongBao,#Cy-tp-DialogYes2{background-color:#2ED146;color:#fff;max-width:45%;border-color:#ddd;border-radius:.3125em;font-weight:700;-moz-user-select:none;cursor:pointer;display:block;font-size:16px;margin:.5em 0;overflow:hidden;padding:.7em 1em;position:relative;text-align:center;text-overflow:ellipsis;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,.15);background-clip:padding-box;border-style:solid;border-width:1px;margin:0 auto}
#huahua1,#huahua2,#hhhh{background-color:#3aa7ff;color:#fff;max-width:45%;border-color:#ddd;text-shadow:0 1px 0 #f3f3f3;border-radius:.3125em;font-weight:700;-moz-user-select:none;cursor:pointer;display:block;font-size:16px;margin:.5em 0;overflow:hidden;padding:.7em 1em;position:relative;text-align:center;text-overflow:ellipsis;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,.15);background-clip:padding-box;border-style:solid;border-width:1px;margin:0 auto;text-shadow:0 0 0 #000;}
#panelbody2 p{color:#747485}
#tupian_wrap{box-shadow:0px 0px 8px #ccc;-moz-box-shadow:0px 0px 8px #ccc;-webkit-box-shadow:0px 0px 8px #ccc;border-radius:6px}
#Cy-tp-DialogYes2{text-decoration:none}
.tipsFont{line-height:1.3rem;margin:6px 3px;border-radius:.2rem;border:solid 1px #ccc;display:inline-block;padding:1px 3px;background-color:#F6F5F7;color:#747485}
$panelbody{white-space:normal;word-break:break-all}
#bdbd a,#bdbd div,#bdbd h3,#bdbd h4,#bdbd h5,#bdbd input,#bdbd p{font-family:"黑体";}
#Cy-tp-PopupTitle{background-color:#2ED146}
#cy-tp-dialog2{border-color:#2ED146}
#boderdd{border:none}
.wbkk{border:1px solid #ddd;height:43px;margin:9px 10px 9px 1px;width:100%;padding-left:18px;text-shadow:0 1px 0 #f3f3f3}
#Cy-tp-DialogInfo p{margin:5px 0;color:#747485}
.jsj{border:1px solid #ccc;padding:4px;border-radius:.2rem;margin:4px 5px}
.crrtt{border:1px solid #2ed146;color:#2ed146}
.ui-body-a, .ui-page-theme-a .ui-body-inherit, html .ui-bar-a .ui-body-inherit, html .ui-body-a .ui-body-inherit, html body .ui-group-theme-a .ui-body-inherit, html .ui-panel-page-container-a
,.ui-page-theme-a .ui-btn, html .ui-bar-a .ui-btn, html .ui-body-a .ui-btn, html body .ui-group-theme-a .ui-btn, html head + body .ui-btn.ui-btn-a, .ui-page-theme-a .ui-btn:visited, html .ui-bar-a .ui-btn:visited, html .ui-body-a .ui-btn:visited, html body .ui-group-theme-a .ui-btn:visited, html head + body .ui-btn.ui-btn-a:visited
,#huahua1, #huahua2,.ui-overlay-a, .ui-page-theme-a, .ui-page-theme-a .ui-panel-wrapper
{text-shadow: 0 0 0 #000;}
.ziti{font-size:13px;}
.layermanim h3{margin:0px;}
.fuck_y
{
  left: 0;
    opacity: 0.76;
    position: absolute;
    top: -20px;}
.fuck_y img
{
border:none;
box-shadow:0 0 0 rgba(0, 0, 0, 0);
}
#pann_k span
{
position:relative;
display:inline-block;
margin-top:30px;
}
#chengyutishilate{font-size:14px;}

::-webkit-input-placeholder { /* WebKit browsers */ 
color: #ccc; 
} 
:-moz-placeholder { /* Mozilla Firefox 4 to 18 */ 
color: #ccc; 
} 
::-moz-placeholder { /* Mozilla Firefox 19+ */ 
color: #ccc; 
} 
:-ms-input-placeholder { /* Internet Explorer 10+ */ 
color: #ccc; 
} 
</style>

<html>
    	<body id="bdbd">
            	<div data-role="page">
                	    <div role="main" class="ui-content">
                	    
                	    				<!-- 图片 -->
                            			<div id="tupian_wrap" class="ui-grid-solo">
                        						<img src="<?php echo $question_pic ?>" alt="" width="100%" style="background:#fff;"  />
                            			</div>
                            			
                            			
                            			<!-- 输入框 -->
                            			<div class="ui-grid-solo" style="margin:15px auto 0px;">
											<label for="search" style="float:left;line-height:35px"></label>
                                    		<input data-role="none" id="search"  name="answerInput"   maxlength="4" placeholder="<?php   if($IsDrawer) {echo "您是画主无法作答";}else{echo "请填写四字成语";} ?>"   style="float:left;max-width:160px;padding-left: 12px;text-shadow: 0 1px 0 #f3f3f3;border:solid 1px #777;height:43px;margin:9px 10px 9px 1px" type="text" />
											<?php   if($IsDrawer) {   //如果是画主自己，无法参与答题 ?>
        			 					    <a href="#" data-role="none" id='share_hy' class="ui-btn  ui-corner-all ui-shadow  ui-btn-a"  >分享给好友</a>
                                    		<?php } else if (@$IsReal) {    //如果猜主已经答对了，无法参与答题 ?>
                                    				<!--  <a href="<?php //echo $_SESSION["STATIC_ROOT"]."/Home.php?p=user"; ?>" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a" id="hhhh"  >答对了，去提现</a>  -->
                                    				 <a style="margin-top:8px;" href="http://mp.weixin.qq.com/s?__biz=MzI3MTIxOTU1Mg==&mid=100000002&idx=2&sn=6e5b8b35f2d2724fab8b5f42a8d53bed#rd" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a" id="hhhh"  >答对了，去提现</a>  
                                    		<?php } else { ?>
                    	    				 		<a href="#" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a <?php if($Time != '' && $Time < 0)  echo "ui-state-disabled"; ?>"  id="submit">提交答案 <span style="font-size:11px; color:#ffff00; "><?php if(!$Is_Out) {echo "猜中奖".($price/100)."元";} ?></span></a>
                    	    				<?php } ?>
                            			</div>
										
										 <div  id="chengyutishi" class="ui-corner-all custom-corners"  style="margin:15px auto; <?php if($tips_word == null){ echo "display:none";} ?>">
                                                      <div id="cyts_k" class="ui-bar ui-bar-a"> <h3>答案提示 </h3> - <span id="chengyutishilate">下面<span id="chengyunum">4</span>个字有<span id="chengyunum2">1</span>个为成语的字</span> </div>
                                                      <div class="ui-body ui-body-a"  id="panelbody">                                                            
															<?php 
															     if($tips_word != null)
															     {
        															      $tips_word_arr  = explode(",", $tips_word);
        															      for($p = 0;$p<count($tips_word_arr);$p++) 
        															      { 
        														              $re = chunk_split($tips_word_arr[$p],3,",");
        														              $re = explode(",",$re);
        														              echo "<div class='tipsFontPanel'>";
        														              for($k = 0;$k<count($re) - 1;$k++)
        														              {
        														                  echo sprintf("<span class='tipsFont'>%s</span>",$re[$k]);
        														              }
        														              echo "</div>";
        															      } 
															     }
															?>  
                                                      </div>
                                            </div>	
													
									
										  <!--  温馨提示 -->
                            			   <div class="ui-corner-all custom-corners"  style="margin:15px auto;color:#747485">
                                                  <div id="zhuyi" class="ui-bar ui-bar-a"> <h3>注意</h3> </div>
                                                  <div class="ui-body ui-body-a"  id="panelbody2">
                                             			<p>1、根据上面的画猜一个四字成语</p>
                                    				<?php  if(!$Is_Out) { ?>                                                    	
                                                    	<p>2、答对可获得 <span style="font-size:20px;color:red">￥ <?php echo $price/100;?> </span>元现金奖励</p>                                                 	
                                                  		<p>3、奖励剩余时间 <span id="suoshengshijian" style="font-size:16px;color:red"><?php echo $expire_time; ?></span></p>
                                                  		<p>4、所有奖金可去个人中心100%提现</p>
                                                  <?php } ?>
                                                    </div>
                                          </div>												
                                          <?php  if(!$Is_Out) {  //该题目没过期/红包没发完的情况下才显示‘温馨提示' ?>        
    										         <div style='margin:0 auto'> <a name="radio-choice" data-role="none"  data-daoju = '1'     id="radio-choice-0a"    >购买提示<span style='color:#ffff00'><?php  echo ($daoju / 100)."元"; ?></span></a></div>
    										         <p class='ziti' style='text-align: center;color:#747485'>(买后可立即再猜一次，并显示4个字含1个成语字)</p>
                                          <?php } ?>          
                                         
                                                        
                                    	           
                                    	 <!-- 各种场景下的按钮 -->     
                                		<?php   if($IsDrawer&&$Is_Out) {   //如果是画主自己，可继续添加红包 ?>
                                								<a href="#" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a" data-role="none"  id="reputHongBao" onClick="reputHongBao()">充值奖金</a>
                                									<p  class='ziti' style='text-align: center;color:#747485'>(充值奖金后可销售提示，收入你可100%提现)</p>
                    					<?php } ?>
													
									
                                       
                                       	<!--  谁玩过  -->
    									<?php  if(count($piclist) > 0) {   //有数据才显示 ?>
    									 		<div class="ui-corner-all custom-corners"  style="margin:15px auto">
    									 			 <div class="ui-bar ui-bar-a" id="szw_k"> <h3>谁在玩</h3> </div>
                                                          <div id="pann_k" class="ui-body ui-body-a">
                                                          		<?php for($k = 0;$k<count($piclist);$k++) { ?>
                                                            			<span><img width="30" src="<?php echo $piclist[$k]["wx_litpic"] ?>" />
                                                            			             <?php if($piclist[$k]["flag"]) { ?>
                                                            			                         <a class="fuck_y"> <img width="30" src="<?php echo  $_SESSION["STATIC_ROOT"]."/Img/hg.png"; ?>"  /> </a>
                                                        			                 <?php } ?>
                                                            			</span>
    															<?php } ?>
																<h5 style='color:#747485'>等<?php echo count($piclist); ?>人正在玩</h5>
                                                          </div>
                                                </div>
    									<?php } ?>
    									
    									
    									
    									<!-- 他们的答案 -->
    									<?php  if(count($answerList) > 0  && $IsDrawer) {    //有数据且是画主自己才显示 ?>
            									 <div class="ui-corner-all custom-corners"  style="margin:15px auto">
                                                          	 <div class="ui-bar ui-bar-a" id="clsm_k"> <h3>他们猜了什么</h3> </div>
                                                          <div class="ui-body ui-body-a ui_ko" id="pnnn_k">
																 <table width="100%" cellpadding="5" id="pop_table" style="margin:10px auto;color:#747485" >
                                                            	<?php for($j = 0;$j<count($answerList);$j++) { ?>
																	 <tr>
																		  <td align="right" width="15" ><img width="30" style="border-radius:25px;" src="<?php echo $answerList[$j]["wx_litpic"]; ?>" /></td>
																			 <td align="left" width="35%"><?php echo Lee::Sub_截取字符串如果超出某位就省略号($answerList[$j]["wx_name"], 15); ?></td>
                                                                            <td align="center" width="50%"><?php echo $answerList[$j]["content"]; ?></td>
                                                                     </tr>
																<?php } ?>
                                                                </table>
                                                          </div>
                                                </div>
    									<?php } else if($IsDrawer){?>
										 <div class="ui-corner-all custom-corners"  style="margin:15px auto">
										  <div class="ui-bar ui-bar-a" id="clsm_k2"> <h3>他们猜了什么</h3> </div>
                                                          <div id="pnnn_k2"  class="ui-body ui-body-a ui_ko">
                                                            	<p style='color:#747485' >暂时还没有人回答</p>
                                                          </div>
                                                </div>
										<?php }?>
    									
                                	 	<?php   if($IsDrawer) {   //如果是画主自己，无法参与答题 ?>
                								<a href="<?php echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=list" id="huahua1" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a">再去画一题</a>
                                		<?php } ?>
                                		<?php   if(!$IsDrawer)  {    //如果猜主已经答对了，无法参与答题 ?>
                                				<!-- <a href="<?php //echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=list" id='huahua2' class="ui-btn  ui-corner-all ui-shadow  ui-btn-a"  >我也要画一题</a> -->
                                				<a href="http://mp.weixin.qq.com/s?__biz=MzI3MTIxOTU1Mg==&mid=100000002&idx=1&sn=4ccd46aa6b0833bf8b4a485253a6d416#rd" id='huahua2' class="ui-btn  ui-corner-all ui-shadow  ui-btn-a"  >我也要画一题</a>
                                		<?php } ?>          
                                    	  
                	    			
                	    			
                	    			
                	    			<!-- jqm模板，后期可能组件化 -->
            	    			  <div data-role='popup' id='Cy-Tp-Alert' data-overlay-theme='b' data-theme='b' data-dismissible='false' style='min-width:300px;' data-transition="pop" >
                                        <a href='#' id='A1' data-rel='back' class='ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right'>Close</a>
                                	        <div data-role='header' data-theme='b'>
                                	        <h1 id='H1'  style='margin:0px; padding: 0.7em 0;'>温馨提示</h1>
                                	        </div>
                                	        <div role='main' class='ui-content'>
                                                    <div id='Cy-Tp-Alert-Content' style='text-align:center;margin:15px;font-size:16px;'></div>					          
                                                <p style='margin:0px auto;text-align:center;'>
                                		            <a href='#' id='A2' data-rel='back'  class='ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-icon-delete ui-btn-icon-left'  data-transition='flow'>确定</a>  
                                		        </p>
                                	        </div>
                               		 </div>
                               		  
                        			
                        			<div data-role="popup" id="cy-tp-dialog2" data-overlay-theme="a" data-theme="a" data-dismissible="false" style="min-width:300px;" data-transition="pop"  >
                                        <a href="#" id="Cy-tp-CloseDialogBtn2" style=" color: #fff; right: 12px;text-decoration: none;top: 2px;font-size:28px;display:inline-block" data-rel="back" class="ui-btn-right">×</a>
                        				    <div data-role="header" id="boderdd" data-theme="b">
                        				    <h1 id="Cy-tp-PopupTitle"  style="margin:0px; padding: 0.7em 1em;text-align:left;text-shadow: 0 0 0 #111;">充值</h1>
                        				    </div> 
                        				    <div role="main" class="ui-content">
                                                    <div id="Cy-tp-DialogInfo" style="margin:0px;font-size:16px;">
                                                       
                                                        		<table>
                                                        				<tr>
                                                        					<td width='30%'>奖金金额:</td>
                                                        					<td><input data-role="none" class='wbkk' name="HongBaoJinE" disabled = "disabled" id="HongBaoJinE"  placeholder="请输入红包金额" value="1"  type="text">
                                                        						 <p><span class='jsj crrtt' val='1'>1元</span> 	
                                                        							<span class='jsj' val='3'>3元</span>
                                                        						 	<span class='jsj' val='5'>5元</span> 	
                                                        							<span class='jsj' val='10'>10元</span>
                                                        						</p> 
                                                        					</td>
                                                        				</tr>
                                                        			    <tr>
                                                        					<td>奖金份数:</td><td>
                                                        			    	<input onKeyUp="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" data-role="none" class='wbkk' name="HongBaoCount" id="HongBaoCount" placeholder="请输入红包个数" value="1"  type="text">
                                                        					</td>
                                                        				</tr>
                                                        			     <tr>
                                                        					<td>购买售价:</td><td>
                                                        			    	<input data-role="none" class='wbkk' name="DaoJuJinE" id="DaoJuJinE"  disabled = "disabled"  placeholder="请输入道具金额" value="<?php echo $prop;?>" type="text" readonly>
                                                        					</td>
                                                        				</tr>
                                                        			</table>
                                                        			<p class='ziti'>(提示售价为单份奖金的30%，收入你可百分百提现)</p>
                                                    </div>					          
                                                <p style="margin:0px auto;text-align:center;">
                        					        <a href="#" id="Cy-tp-DialogYes2" data-role="none" >充值奖金<span style='color:#FEFF00'><font id='jinddd'>1</font>元</span></a> 
                        					
                                                </p>
                        				    </div>
                        			</div>    
                        			
                	    </div><!-- /content -->
            	</div><!-- /page -->
				<div id="zhezhaocheng" style="background-color:#000000;display:none"><img src="<?php echo $_SESSION["STATIC_ROOT"]?>/Img/83358PICrqB_1024.jpg" /></div>	
	 	</body>
</html>

		
<?php 
	JsLoader::Jquery();    //加载jquery
	JsLoader::Jqm();       //加载jqm
	//JsLoader::Layer();     //加载layer
    JsLoader::Layermobile();    //加载layermobile
	JsLoader::weixin();   //加载微信官方JS
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
	JsLoader::LoadDirective('HuaHua', 'WeiXin.Directive.js');   //加载个人封装的微信JS指令
	JsLoader::LoadDirective('HuaHua', 'Guess.Directive.js');
?>


<script>

var time = <?php echo $Time; ?>;
if(time < 0)
{
	//冷却时间
	var abstime = Math.abs(time);
	//开始倒计时
	showtime(abstime);
}


//通过js动态修改成语提示 
var textlength = $("#panelbody").text().replace(/\s/g, "").length;
$("#chengyunum").text(textlength);
$("#chengyunum2").text(textlength/4);


 
//距离时间的时间戳
var suoshengshijian = <?php echo $expire_time_unix ; ?>;
//当前时间的时间戳
var timestamp = Date.parse(new Date()) / 1000; 
//倒计时函数
var getTime = function() 
{
    var nowTime = new Date();
    var endTime = new Date(suoshengshijian * 1000);
    var ms = endTime.getTime() - nowTime.getTime();
    var day = Math.floor(ms / (1000 * 60 * 60 * 24));
    var hour = Math.floor(ms / (1000 * 60 * 60)) % 24;
    var minute = Math.floor(ms / (1000 * 60)) % 60;
    var second = Math.floor(ms / 1000) % 60;
    ms = Math.floor(ms / 100) % 10;
    if (second >= 0)   
    {
        document.getElementById("suoshengshijian").innerHTML = hour + "小时 " + minute + "分 " + second + " 秒"
    } else {
        window.location.reload();
        return false;
    }
    setTimeout("getTime()", 1000);
}


if(timestamp < suoshengshijian)
{ 
	//如果当前时间小于过期时间，说明可以显示倒计时
	getTime(); 
}

wx.config
({
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp:'<?php echo $signPackage["timestamp"];?>',
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: ['onMenuShareAppMessage','onMenuShareTimeline','startRecord','stopRecord','uploadVoice','downloadVoice'
        // 所有要调用的 API 都要加到这个列表中
    ]
});

//价值（）元，快来猜，手快有手慢无
wx.ready(function ()  
{
	var mylink = "<?php echo $_SESSION["STATIC_ROOT"].'/Home.php?p=guess&q='.$_GET["q"];?>";	
	var wxshare_title = "猜画有奖，<?php echo $nickname ?>画了副<?php if(!$Is_Out){echo ($price/100)."元的";} ?>成语画给大家猜";	
	var wxshare_imgUrl = "<?php echo $question_pic ?>";
	var des="<?php  if(!$Is_Out) {echo "价值".($price/100)."元，快来猜，手快有手慢无";} ?>";	
	share(mylink,wxshare_title,wxshare_imgUrl,des);
});
  

</script>