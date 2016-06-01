//使用指南
//JsLoader::wilddog();   //加载野狗
//JsLoader::LoadDirective('HuaHua', 'Ajax.Directive.js');
//WildddogDir.log("test");

var WildddogDir = {};

var dogobj = new Wilddog("https://cyhuahua.wilddogio.com/");


WildddogDir.log =  function(content)
{
	var Mydate = new Date();    //Date对象
	var day = Mydate.format("yyyy年MM月dd日"); //日期
	var guid = newGuid();
	var time = Mydate.toLocaleTimeString(); //当前时间
	
	//...野狗日志系统
	dogobj.child(day).child(guid).set
	({
		"time":time,
	    "content":content
	}); 
}



//<summary>
//时间类
//</summary>
Date.prototype.format = function(format){ 
 var o = { 
     "M+" : this.getMonth()+1, //month 
     "d+" : this.getDate(), //day 
     "h+" : this.getHours(), //hour 
     "m+" : this.getMinutes(), //minute 
     "s+" : this.getSeconds(), //second 
     "q+" : Math.floor((this.getMonth()+3)/3), //quarter 
     "S" : this.getMilliseconds() //millisecond 
 }
 if(/(y+)/i.test(format)) { 
     format = format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
 }
 for(var k in o) { 
     if(new RegExp("("+ k +")").test(format)) { 
         format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length));
     } 
 } 
 return format; 
}

//<summary>
//Guid
//</summary>
function newGuid()
{
 var guid = "";
 for (var i = 1; i <= 32; i++){
     var n = Math.floor(Math.random()*16.0).toString(16);
     guid +=   n;
   
 }
 return guid;     
}
