

var Lee = {};


// <summary>
// Get_获取浏览器宽度
// </summary>
Lee.Get_获取浏览器宽度 = function()
{
	return $(window).width();	
}

	
// <summary>
// xs_超小屏幕如所有手机小于768px
// </summary>
Lee.xs_超小屏幕如所有手机小于768px = function()
{
	if(Lee.Get_获取浏览器宽度 () < 768)	
	return true;
	
}

// <summary>
// sm_小屏幕如ipad平板大于768px
// </summary>
Lee.sm_小屏幕如ipad平板大于等于768px = function()
{
	if(Lee.Get_获取浏览器宽度 () >= 768)	
	return true;
}

// <summary>
//md_中等屏幕如桌面大于992px
// </summary>
Lee.md_中等屏幕如桌面大于等于992px = function()
{
	if(Lee.Get_获取浏览器宽度 () >= 992)	
	return true;
}

// <summary>
// lg_大屏幕如现代显示器大于1200px
// </summary>
Lee.lg_大屏幕如现代显示器大于等于1200px = function()
{
	if(Lee.Get_获取浏览器宽度 () >= 1200)	
	return true;
}



