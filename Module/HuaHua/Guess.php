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
//是否关注了公众号
//$subscribe=$_SESSION["subscribe"];

$q = $_GET["q"];                                               //题目编号
$IsDrawer= false;                                              //是否画主本人
$Time = "0";                                                      //冷却时间
 

//微信类 开始==============================================
$ko=new WX_INT();
$signPackage = $ko->GetSignPackage();   //获取分享接口 相关信



//页面逻辑 开始==============================================
$_GuessCtrl = new GuessCtrl();


$arr = $_GuessCtrl->get_根据ID获取画画信息();                   //显示图片信息
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
//$Title =$arr["wx_name"]." - 成语作品";                           //设置页面Title
$Title ="看图猜成语";                                                        //设置页面Title
$model = $arr["model"];                                                  //该题目的模式


//判断openid是否存在用户表，没有的话先插入
// if(!$_GuessCtrl->Openid是否存在用户表中())
// {
//     //添加新用户到数据库
//     $_GuessCtrl->Insert_新增用户($openid,$nickname,$headimgurl);      
// }
// else 
// {
//     //获取画主标识
//     $IsDrawer = $_GuessCtrl->Is_是否是画主($q);
//     //获取是否答对的标识
//     $IsReal = $_GuessCtrl->Is_是否用户已经回答正确过();
//     //获取非奖励但答对的标识
//     $IsRealButNotMoney = $_GuessCtrl->Is_是否为回答正确但没有获取红包的用户();
//     //获取时间间隔
//     if(!$IsDrawer)
//     {
//         $Time = $_GuessCtrl->get_距离下一次答题的时间();
//     }
// }
 
 
$_GuessCtrl->SET_用户($openid,$nickname,$headimgurl);


//获取画主标识
$IsDrawer = $_GuessCtrl->Is_是否是画主($q);
//获取是否答对的标识
$IsReal = $_GuessCtrl->Is_是否用户已经回答正确过();
//获取非奖励但答对的标识
$IsRealButNotMoney = $_GuessCtrl->Is_是否为回答正确但没有获取红包的用户();
//获取时间间隔
if(!$IsDrawer)
{
    $Time = $_GuessCtrl->get_距离下一次答题的时间();
}




//插入访客
$_GuessCtrl->Add_插入访客($openid,$headimgurl,$nickname);

//返回true即是过期
$Is_Out =  $_GuessCtrl->红包是否无剩余或者过期();     

//获取访客答案列表
$answerList = $_GuessCtrl->get_answerList();

//获取访客图像列表
$piclist = $_GuessCtrl->get_headpic();

//获取道具提示列表
$tips_word = $_GuessCtrl->get_tips_for_word();

//获取道具比例
$prop = $_GuessCtrl->get_获取道具比例();

//获取答题消费比例
$model_prop = $_GuessCtrl->get_获取答题花销比例();

?>


 <?php include $_SESSION["APP_ROOT"].'/Inc/Header.inc.php'; ?>
 
<?php 
       CssLoader::Jqm();                                                //加载jqm.css
       CssLoader::LoadCss("HuaHua", "Guess.css");       //加载本页样式
 ?>
	
<?php 
	JsLoader::Jquery();    //加载jquery
	JsLoader::Jqm();       //加载jqm
    JsLoader::Layermobile();    //加载layermobile
	JsLoader::weixin();   //加载微信官方JS
	JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
	JsLoader::LoadDirective('HuaHua', 'WeiXin.Directive.js');   //加载个人封装的微信JS指令
	JsLoader::LoadDirective('HuaHua', 'Guess.Directive.js');
?>	
	
<html>
    	<body id="bdbd">
            	<div data-role="page">
                	    <div role="main" class="ui-content">
                	    
                	    				<!-- 图片 -->
                            			<div id="tupian_wrap" class="ui-grid-solo">
                        						<img src="<?php echo $question_pic ?>" alt="名作加载中" width="100%" style="background:#fff;"  />
                            			</div>
                            			
                            			<!-- 已过期 -->
                            			<?php if($Is_Out) { ?>
                    							<div id='hb_k'>
                    								    <span class="jjjss">奖金已领完</span>
                    							        <img width="150px" src="http://huahua.ncywjd.com/Img/hf.png" />
                    							</div>  
							             <?php } ?>
									
									   <!-- 未过期 -->
            							<?php if(!$Is_Out) { ?>
                    							<div id='hb_k'>
                    								    <span class="jjjss">猜对奖<?php echo $price/100; ?>元</span>
                    							         <img width="150px" src="http://huahua.ncywjd.com/Img/hf.png" />
                    							</div>  
            							<?php } ?>
                            			
                            			<!-- 输入框 -->
                            			<div class="ui-grid-solo"  style="margin:15px 0px 7px;">
                            			     <div style="margin:0 auto;width:169px">
    											<label for="search" style="float:left;line-height:35px"></label>
                                        		<input data-role="none" id="search"  name="answerInput"  maxlength="4" <?php if(@$IsReal) { echo "disabled='disabled'";} ?> <?php if($IsDrawer) { echo "disabled='disabled'";} ?> placeholder="<?php   if($IsDrawer) {echo "您是画主无法作答";}else{echo "请填写四字成语";} ?>"   style="max-width:160px;text-align:left;text-shadow: 0 1px 0 #f3f3f3;border:solid 1px #777;height:43px;margin:9px 10px 9px 1px" type="text" />
                            			     </div>
                            			</div>
                            			
                            			 <!-- 各种场景下的按钮 -->     
                                		<?php   if($IsDrawer&&$Is_Out) {   //如果是画主自己，可继续添加红包 ?>
                                								<a href="#" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a" data-role="none"  id="reputHongBao" onClick="reputHongBao()" style="max-width:90%;">充值奖金</a>
                                							    <p  class='ziti' style='text-align: center;color:#747485'>(充值奖金后别人答题需向您支付奖金的30%)</p>
                    					<?php } ?>
										<?php   if($IsDrawer) {   //如果是画主自己，无法参与答题 ?>
                                    		<?php } else if (@$IsReal) {    //如果猜主已经答对了，无法参与答题 ?>
                                    				<?php if(@$IsRealButNotMoney) { ?>
                                    				        <a style="margin-top:8px;margin: auto;" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a" id="hhhh"  >答对了</a>
                                    				 <?php } else { //如果你已经答对这道题，那么无法再次回答，只能去个人中心兑换奖励  ?>
                                				            <a style="margin-top:8px;margin: auto;" href="http://huahua.ncywjd.com/home.php?p=user" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a" id="hhhh"  >答对了，去提现</a>  
                                    		          <?php } ?>
                                    		<?php } else { //你终于可以正常回答了  ?>
                    	    				 		<a href="#" data-role="none" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a <?php if($Time != '' && $Time < 0 || $Is_Out )  echo "ui-state-disabled"; ?>"  style="margin: auto;font-size:18px;" id="submit">提交答案 <span style="font-size:13px; color:#ffff00;display:block;margin-top:5px "><?php if(!$Is_Out) {echo "须支付".$price/100 * $model_prop."元，猜中奖".($price/100)."元";}else{echo " 奖金已领完";} ?></span></a>                	    				
                    	    				<?php } ?>
                    	    				
                    	    				
                    	    				<?php if(!$Is_Out){ //如果这道题没有过期，才会显示“提示” ?>                    	    				          
										               <p  class='ziti' style='text-align: center;color:red'>猜对奖<?php echo $price/100 ; ?>元，猜错会送4字选1字的提示</p>
										     <?php } ?>
										     
										
										<div style="clear:both;margin:15px auto;height:45px;max-width:96%;">
    								       	<?php   if($IsDrawer) {   //如果是画主自己，无法参与答题 ?>
                    								<a href="<?php echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=list" id="huahua1" class="ui-btn  ui-corner-all ui-shadow  ui-btn-a" style="float: left; width: 19%;margin-right:2%;">去画题</a>
                                                    <a id="huahua1" class="ui-btn ui-corner-all ui-shadow ui-btn-a" href="http://huahua.ncywjd.com/home.php?p=user" style="float: left; width: 19%;margin-right:2%;">去提现</a>
													<a id="huahua1" class="ui-btn ui-corner-all ui-shadow ui-btn-a" href="<?php echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=Maidan" style="float: left; width: 19%;">更多题</a>
                                    		<?php } ?>
                                    		<?php   if(!$IsDrawer)  {    //如果猜主已经答对了，无法参与答题 ?>
                                    				<!-- <a href="<?php //echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=list" id='huahua2' class="ui-btn  ui-corner-all ui-shadow  ui-btn-a"  >我也要画一题</a> -->
                                    				<a href="<?php echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=list" id='huahua2' class="ui-btn  ui-corner-all ui-shadow  ui-btn-a"  style="float: left; width: 19%;margin-right:2%;" >去画题</a>
                                    				<a id="huahua1" class="ui-btn ui-corner-all ui-shadow ui-btn-a" href="http://huahua.ncywjd.com/home.php?p=user" style="float: left; width: 19%;margin-right:2%">去提现</a>
													<a id="huahua1" class="ui-btn ui-corner-all ui-shadow ui-btn-a" href="<?php echo $_SESSION["STATIC_ROOT"]?>/Home.php?p=Maidan" style="float: left; width: 19%;">更多题</a>
                                    		<?php } ?>          
                                    	  </div>
										  
										  <a href="#" data-role="none" id='share_hy' class="ui-btn  ui-corner-all ui-shadow  ui-btn-a"  style="margin:auto;width:60%;" >分享给好友一起玩</a>
										  
										
										
										<!-- 成语提示 -->
										 <div  id="chengyutishi" class="ui-corner-all custom-corners"  style="margin:15px auto; <?php if($tips_word == null){ echo "display:none";} ?>">
                                                      <div id="cyts_k" class="ui-bar ui-bar-a"> <h3>答案提示 </h3>  <span id="chengyutishilate" style="font-size:12px;color:#666;">亲，好好利用您的提示</span> </div>
                                                      <div class="ui-body ui-body-a"  id="panelbody">         
													  <p style="text-align:center;color:#FF0000;margin:0 0 0 0;padding:0;font-size:12px">★根据提示来答题成功率提升500%★</p>                                                   
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
    														              echo "<a style='color:#666;font-size:12px'>←←(四字选一字)</a></div>";
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
                                                  		<p>4、所得奖金可去<a data-role="none" href="http://mp.weixin.qq.com/s?__biz=MzI3MTIxOTU1Mg==&mid=100000002&idx=2&sn=6e5b8b35f2d2724fab8b5f42a8d53bed#rd">个人中心</a>提现</p>
                                                  <?php } ?>
                                                    </div>
                                          </div>		
                                          
                                          <!-- 未过期  -->										
                                          <?php  if(!$Is_Out) {  //该题目没过期/红包没发完的情况下才显示‘温馨提示' ?>     
                                                     <!-- 
    										         <div style='margin:0 auto'> <a name="radio-choice" data-role="none"  data-daoju = '1'     id="radio-choice-0a"    >购买提示<span style='color:#ffff00'><?php // echo ($daoju / 100)."元"; ?></span></a></div>
    										         <p class='ziti' style='text-align: center;color:#747485'>(买后可立即再猜一次，并显示4个字含1个成语字)</p>
													 <p class='ziti' style='text-align: center;color:#ff0000'>推荐购买，有提示猜对机率提升200%</p>
													  -->    
                                          <?php } ?>          
                                         
                                                        
                                    	           
                                    	
													
									
                                       
                                       	<!--  谁玩过  -->
    									<?php  if(count($piclist) > 0) {   //有数据才显示 ?>
    									 		<div class="ui-corner-all custom-corners"  style="margin:15px auto">
    									 			 <div class="ui-bar ui-bar-a" id="szw_k"> <h3>谁在玩 <span style="font-size:12px;color:#666666">有<img width="17" src="<?php echo  $_SESSION["STATIC_ROOT"]."/Img/hgg.png"; ?>" />为猜对了</span><span style="font-size:12px;color:#666">，<span id='dddssa'></span>为猜的次数</span></h3> </div>
                                                          <div id="pann_k" class="ui-body ui-body-a">
                                                          		<?php for($k = 0;$k<count($piclist);$k++) { ?>
                                                            			<span><img width="30" height="30" src="<?php echo $piclist[$k]["wx_litpic"] ?>" />
                                                            			             <?php if($piclist[$k]["flag"]) { ?>
                                                            			                         <a class="fuck_y"> <img width="30"  src="<?php echo  $_SESSION["STATIC_ROOT"]."/Img/hg.png"; ?>"  /> </a>
                                                        			                 <?php } ?>
																					 <div id="ddd_kk">
																					            <?php $nn=$piclist[$k]["daojuflag"];  for($i=0;$i<$nn;$i++) { echo "<span class='dian'></span>"; if($i>=4) { break; } } ?>
																					 </div>
                                                            			</span>
    															<?php } ?>
																<h5 style='color:#747485'>共<?php echo $_GuessCtrl->get_headpic_count(); ?>人正在玩</h5>
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
																		  <td align="right" width="15" ><img width="30" height="30" style="border-radius:25px;" src="<?php echo $answerList[$j]["wx_litpic"]; ?>" /></td>
																			 <td align="left" width="35%"><?php echo Lee::Sub_截取字符串如果超出某位就省略号($answerList[$j]["wx_name"], 15); ?></td>
                                                                            <td align="center" width="50%"><?php echo $answerList[$j]["content"]; ?></td>
                                                                     </tr>
																<?php } ?>
                                                                </table>
                                                                <h5 style='color:#747485'>共有<?php echo $_GuessCtrl->get_answerList_count(); ?>个回答</h5>
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
                                                    		<p style="text-align:center;font-size:20px;font-weight:bold">奖金金额</p>
																<p style="margin:2pxpx auto;padding:0;font-size:12px;text-align:center;color:#ff0000">24小时内奖金未领完退回到你的余额</p>
                                                    					<input style="display:none" data-role="none" class='wbkk' name="HongBaoJinE" disabled = "disabled" id="HongBaoJinE"  placeholder="请输入红包金额" value="3"  type="text">
                                                    						 <p>
																			    <span class='jsj' style="float:left;width:17%" val='0.3'>0.3元</span> 	
                                                    						
                                                    							<span class='jsj crrtt'  style="float:left;width:17%" val='3'>3元</span>
																			
                                                    						
																				<span class='jsj' style="float:left;width:17%" val='20'>20元</span> 
																				
																				<span class='jsj' style="float:left;width:17%" val='100'>100元</span> 
                                                    						</p> 
                                                    				<div style="clear:both"></div>
                                                    			    <div style="display:none">
                                                    			
                                                    			    	<input maxlength="2" onKeyUp="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" data-role="none" class='wbkk' name="HongBaoCount" id="HongBaoCount" placeholder="请输入红包个数" value="1"  type="text">
                                                    			</div>
                                                    			
																<p style="margin:0 auto"><input style="width:14px;height:14px;margin-top:1px;margin-left:15%" id="tuijiansf" type="checkbox" checked="checked"  /><span style="font-size:11px;display:inline;margin-left:24%">(有诚意的题有几率推荐至首页)</span></p>
                                                    			<!--  <p class='ziti'>(提示售价为单份奖金的30%，收入你可百分百提现)</p>-->
                                                    			<p style="margin:8px auto 2px auto;padding:0;font-size:12px;text-align:center">别人每猜一次，须向您支付<span id="model_price">0.9</span>元</p>
															
																<p style="margin:2px auto;padding:0;font-size:12px;text-align:center">您的收益可提现</p>
                                                    </div>					          
                                                    <p style="margin:0px auto;text-align:center;">
                            					               <a href="#" id="Cy-tp-DialogYes2" data-role="none" >充值奖金<span style='color:#FEFF00'><font id='jinddd'>3</font>元</span></a> 
                                                    </p>
                        				    </div>
                    			    </div> 
										<div class="ui-corner-all custom-corners"  style="margin:15px auto;">
    									 			 <div class="ui-bar ui-bar-a" style="border-top-left-radius: 0.3125em;
    border-top-right-radius: 0.3125em;" > <h3>亲，关注微信不迷路</h3> </div>
													   <div class="ui-body ui-body-a" style="border-bottom-left-radius: 0.3125em;
    border-bottom-right-radius: 0.3125em;" >
														<div style="text-align:center"><img width="200"  style="border-radius: 0;
   " src="<?php echo $_SESSION["STATIC_ROOT"]?>/Img/qrcode_for_gh_0399f3f16327_430.jpg" />
														</br>
														<span style="color:#FF0000">长按二维码2秒，识别关注</span>
														</div>
														</div>  
													
									</div> 
                	    </div><!-- /content -->
            	</div><!-- /page -->
				
				<div id="zhezhaocheng" style="background-color:#191919;display:none;position:absolute;left:0;top:0"><img width="100%" src="<?php echo $_SESSION["STATIC_ROOT"]?>/Img/83358PICrqB_1024.jpg" /></div>	
	 	</body>
		<style type="text/css">
			#huahua1, #huahua2
			{
			padding: 0.7rem 6%}
		</style>
</html>

		



<script>
 
var IsReal = "<?php echo @$IsReal; ?>"; 
var time = <?php echo $Time; ?>;
if(time < 0 && IsReal != "1")
{
	//冷却时间
	var abstime = Math.abs(time);
	//开始倒计时
	showtime(abstime);
}


//通过js动态修改成语提示 
var textlength = $("#panelbody").text().replace(/\s/g, "").length;
//$("#chengyunum").text(textlength);
//$("#chengyunum2").text(textlength/4);


 
//距离时间的时间戳
var suoshengshijian = <?php echo $expire_time_unix ; ?>;
//当前时间的时间戳
var timestamp = Date.parse(new Date()) / 1000; 
//倒计时函数
var getTime = function() 
{
	var daojishi_sysj =  document.getElementById("suoshengshijian");
	if(daojishi_sysj == null) return false;  //说明题目已过期;
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
    	daojishi_sysj.innerHTML = hour + "小时 " + minute + "分 " + second + " 秒";
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