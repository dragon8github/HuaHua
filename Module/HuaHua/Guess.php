<?php 
SESSION_START();

//引用区 开始================================================
include $_SESSION["APP_ROOT"].'/Lib/Class/Lee.class.php';                                   //加载辅助类库
include $_SESSION["APP_ROOT"].'/Lib/wang/wx_class.php';                                  //加载微信类
include $_SESSION["APP_ROOT"].'/Controller/HuaHua/Guess.Controller.php';      //加载List页面控制器
include $_SESSION["APP_ROOT"].'/Inc/CssLoader.inc.php';                                  //加载CSS组件库
include $_SESSION["APP_ROOT"].'/Inc/JsLoader.inc.php';                                   //加载JS组件库


//公共变量 开始==============================================

$openid = $_SESSION["openid"];                       //openid
$nickname = $_SESSION["nickname"];              //昵称
$headimgurl = $_SESSION["headimgurl"];        //头像
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
$expire_time = date('Y-m-d H:i:s',$arr["expire_time"]);         //过期时间
$flag = $arr['flag'];                                                            //是否过期
$hongbao_count = $arr["hongbao_count"];                      //红包个数
$shengyu_count = $arr["shengyu_count"];                        //红包余额
$prop = $arr["prop"];                                                       //道具比例
$daoju =$prop;                                                               //道具价格
$Title =$arr["wx_name"]." - 成语作品";                                                  //设置页面Title


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

//**********判断tips状态
$tips_row=$_GuessCtrl->get_tip_rows();


//获取tips
$tips_arr=$_GuessCtrl->get_tips();

?>



<?php 	include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php';?>

<?php 
       CssLoader::Jqm();      //加载jqm.css
 ?>
	
 <style type="text/css">
.ui-body-a img { border-radius: 25px; -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.5); -moz-box-shadow: inset 0 1px 5px rgba(0,0,0,.5); box-shadow: inset 0 1px 1px rgba(0,0,0,.5); }
.ui_ko span { margin:0 3px; float:left;}
</style>

<html>
    	<body>
            	<div data-role="page">
                	    <div role="main" class="ui-content">
                	    
                	    				<!-- 图片 -->
                            			<div class="ui-grid-solo">
                        						<img src="<?php echo $question_pic ?>" alt="" width="100%" style="background:#fff;height:300px" height="30%" />
                            			</div>
                            			
                            			
                            			<!-- 输入框 -->
                            			<div class="ui-grid-solo" style="margin:15px auto 0px;">
											<label for="search" style="float:left;line-height:35px"></label>
                                    		<input data-role="none" id="search"  name="answerInput"   maxlength="4" placeholder="<?php   if($IsDrawer) {echo "您是画主无法作答";}else{echo "请填写四字成语";} ?>"   style="float:left;max-width:160px;padding-left: 18px;text-shadow: 0 1px 0 #f3f3f3;border:solid 1px #ddd;height:43px;margin:9px 1px" type="text" />
											<?php   if($IsDrawer) {   //如果是画主自己，无法参与答题 ?>
                        		          			
                                			 					<a href="#" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a  ui-state-disabled"  >分享给好友</a>
                                					
                                								
                                				
                                		<?php } else if ($IsReal) {    //如果猜主已经答对了，无法参与答题 ?>
                                				<a href="#" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a  ui-state-disabled"  >你已经答对这道题了</a>
                                		<?php } else { ?>
                	    				 		<a href="#" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a <?php if($Time != '' && $Time < 0)  echo "ui-state-disabled"; ?>"  id="submit">提交</a>
                	    				<?php } ?>
                            			</div>
										
										 <div  id="chengyutishi" class="ui-corner-all custom-corners"  style="margin:15px auto; <?php if($tips_row == 0){ echo "display:none";} ?>">
                                                      <div class="ui-bar ui-bar-a"> <h3>成语提示</h3> </div>
                                                      <div class="ui-body ui-body-a"  id="panelbody">                                                            
															<?php 
																if($tips_row==1)
																{
																	echo "答案提示1：".$tips_arr[0]['tips'];
																}else if($tips_row>1)
																{
																	echo "答案提示1：".$tips_arr[0]['tips'];
																	echo "</br>答案提示2：".$tips_arr[0]['tips2'];
																}
															?>
                                                      </div>
                                            </div>	
													
									
										<!--  温馨提示 -->
                                    	<?php  if(!$Is_Out) {  //该题目没过期/红包没发完的情况下才显示‘温馨提示' ?>
                                    			 <div class="ui-corner-all custom-corners"  style="margin:15px auto">
                                                          <div class="ui-bar ui-bar-a"> <h3>温馨提示</h3> </div>
                                                          <div class="ui-body ui-body-a"  id="panelbody2">
                                                            	<p>1、题目剩余红包 <span style="font-size:20px;color:red"><?php echo $hongbao_count; ?></span> 个</p>
                                                            	<p>2、答对题目可获取红包 <span style="font-size:20px;color:red">￥ <?php echo $price/100;?> </span>元</p>
                                                         		<p>3、购买提示的价格为 <span style="font-size:20px;color:red">￥ <?php echo $daoju/100; ?> </span>元</p>
                                                          		<p>4、红包奖励截止到 <span style="font-size:16px;color:red"><?php echo $expire_time; ?></span></p>
                                                          </div>
                                                </div>												
                                       <?php } ?>
                                       
                                       	<!--  谁玩过  -->
    									<?php  if(count($piclist) > 0) {   //有数据才显示 ?>
    									 		<div class="ui-corner-all custom-corners"  style="margin:15px auto">
                                                          <div class="ui-body ui-body-a">
                                                          		<?php for($k = 0;$k<count($piclist);$k++) { ?>
                                                            			<span><img width="30" src="<?php echo $piclist[$k]["wx_litpic"] ?>" /></span>
    															<?php } ?>
																<h5>等<?php echo count($piclist); ?>人正在玩</h5>
                                                          </div>
                                                </div>
    									<?php } ?>
    									
    									
    									
    									<!-- 他们的答案 -->
    									<?php  if(count($answerList) > 0  && $IsDrawer) {    //有数据且是画主自己才显示 ?>
            									 <div class="ui-corner-all custom-corners"  style="margin:15px auto">
                                                          <div class="ui-body ui-body-a ui_ko">
                                                            	<p>他们的答案：</p>
																 <table width="100%" id="pop_table" style="margin:10px auto;" >
																
                                                            	<?php for($j = 0;$j<count($answerList);$j++) { ?>
																		
																		 <tr>
																		  <td align="right" width="15" ><img width="30" style="border-radius:25px;" src="<?php echo $answerList[$j]["wx_litpic"]; ?>" /></td>
																			 <td align="left" width="35%"><?php echo $answerList[$j]["wx_name"]; ?></td>
                                                                            <td align="center" width="50%"><?php echo $answerList[$j]["content"]; ?></td>
                 
                                                                        </tr>
												
																<?php } ?>
																 		
                                                                </table>
                                                          </div>
                                                </div>
    									<?php } else if($IsDrawer){?>
										 <div class="ui-corner-all custom-corners"  style="margin:15px auto">
                                                          <div class="ui-body ui-body-a ui_ko">
                                                            	<p>暂时还没有人回答</p>
                                                            	
                                                          </div>
                                                </div>
										<?php }?>
    									
                                    	           
                                    	 <?php  if(!$Is_Out) {  //该题目没过期/红包没发完的情况下才显示‘温馨提示' ?>        
										          <input name="radio-choice"  data-daoju = '1' data-tipstype = '<?php echo $tips_row; ?>'   id="radio-choice-0a"  type="submit" value="购买提示">
                                         <?php } ?>          
                                         
                                                        
                                    	           
                                    	 <!-- 各种场景下的按钮 -->     
                                		<?php   if($IsDrawer&&$Is_Out) {   //如果是画主自己，可继续添加红包 ?>
                                								<a href="#" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a"  id="reputHongBao" onClick="reputHongBao()">发布奖金</a>
                                					<?php } ?>
													
										<?php   if($IsDrawer) {   //如果是画主自己，无法参与答题 ?>
                                								<a href="<?php echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=list" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a">再去画一题</a>
                                		<?php } ?>
                                		<?php   if(!$IsDrawer)  {    //如果猜主已经答对了，无法参与答题 ?>
                                				<a href="<?php echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=list" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a"  >我也是画一题</a>
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
                               		 
                               		 
                	    			<div data-role="popup" id="cy-tp-dialog" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="min-width:300px;" data-transition="pop"  >
                                        <a href="#" id="Cy-tp-CloseDialogBtn" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
                        				    <div data-role="header" data-theme="b">
                        				    <h1 id="Cy-tp-PopupTitle"  style="margin:0px; padding: 0.7em 0;">温馨提示</h1>
                        				    </div>
                        				    <div role="main" class="ui-content">
                                                    <div id="Cy-tp-DialogInfo" style="margin:0px 15px;font-size:16px;">
                                                       			<p>该金额将由微信方代管并且转付到出题者账户中</p>
                                                        		 <table width="100%" id="pop_table" style="color:#fff;margin:10px auto;"  >
                                                                            <td align="right" width="55%">支付金额 ：</td>
                                                                            <td align="left" width="45%" id="cy-tp-money">￥ <?php echo $daoju ?></td>
                                                                        </tr>
                                                                </table>
                                                    </div>					          
                                                <p style="margin:0px auto;text-align:center;">
                        					        <a href="#" id="Cy-tp-DialogYes" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-icon-check ui-btn-icon-left">确认购买</a> 
                        					        <a href="#" id="Cy-tp-DialogNo"  class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-icon-delete ui-btn-icon-left" data-rel="back" data-transition="flow">取消交易</a>
                                                </p>
                        				    </div>
                        			</div>  
                        			
                        			<div data-role="popup" id="cy-tp-dialog2" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="min-width:300px;" data-transition="pop"  >
                                        <a href="#" id="Cy-tp-CloseDialogBtn" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
                        				    <div data-role="header" data-theme="b">
                        				    <h1 id="Cy-tp-PopupTitle"  style="margin:0px; padding: 0.7em 0;">请确认交易信息</h1>
                        				    </div>
                        				    <div role="main" class="ui-content">
                                                    <div id="Cy-tp-DialogInfo" style="margin:0px 15px;font-size:16px;">
                                                       
                                                        			<div class="ui-field-contain">
                                                        			    <label for="textinput-1">红包金额:（单位：￥）</label>
                                                        			    <input name="HongBaoJinE" id="HongBaoJinE" placeholder="请输入红包金额" value="1"  type="text">
                                                        			</div>
                                                        			
                                                        			<div class="ui-field-contain">
                                                        			    <label for="textinput-1">红包个数:（单位：个）</label>
                                                        			    <input name="HongBaoCount" id="HongBaoCount" placeholder="请输入红包个数" value="1"  type="text">
                                                        			</div>
                                                        			<div class="ui-field-contain">
                                                        			    <label for="textinput-1">购买提示金额:（单位：￥）</label>
                                                        			    <input name="DaoJuJinE" id="DaoJuJinE"  class="ui-state-disabled"  placeholder="请输入道具金额" value="0.5" type="text" readonly>
                                                        			</div>
                                                    </div>					          
                                                <p style="margin:0px auto;text-align:center;">
                        					        <a href="#" id="Cy-tp-DialogYes2" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-icon-check ui-btn-icon-left">确认</a> 
                        					        <a href="#" id="Cy-tp-DialogNo2"  class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-icon-delete ui-btn-icon-left" data-rel="back" data-transition="flow">取消</a>
                                                </p>
                        				    </div>
                        			</div>    
                        			
                	    </div><!-- /content -->
            	</div><!-- /page -->	
	 	</body>
</html>

		
<?php 
	JsLoader::Jquery();    //加载jquery
	JsLoader::Jqm();       //加载jqm
	JsLoader::Layer();     //加载layer
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

</script>
<script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>
<script>



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

wx.ready(function () 
{
	var mylink = "<?php echo $_SESSION["STATIC_ROOT"].'/Home.php?p=guess&q='.$_GET["q"];?>";
	
	var wxshare_title = "猜画有奖，<?php echo $nickname ?> 画了一副成语画给你，大家一起来玩吧！";	

	var wxshare_imgUrl = "<?php echo $question_pic ?>";
	
	share(mylink,wxshare_title,wxshare_imgUrl);
});
  

</script>