<?php 

/**
* 
*/
class Page
{
	private $_总页码;		//总页码
	private $_总条数;		//总条数
	private $_每页显示条数;	//每页显示条数		
	private $_当前页码;		//当前页码	

	private $url;			//当前URL
	private $bothnum;		//位于当前页码两边的页码数量
	private $limit;			//分页查询条件

	function __construct($_total,$_pagesize)
	{
		$this->_总条数 = $_total?$_total:1;
		$this->_每页显示条数 =   $_pagesize;
		$this->_总页码 = ceil($this->_总条数/$this->_每页显示条数);
		$this->_当前页码 =  $this->setPage(); 

		$this->url = $this->setUrl();
		$this->bothnum = 2;
		$this->limit = 'LIMIT '.($this->_当前页码) * $this->_每页显示条数.','.$this->_每页显示条数;
	}
	


	private function setPage()
	{
		if(!empty($_GET['page']))
		{
			if($_GET['page'] > 0)
			{
				if($_GET['page'] > $this->_总页码)
				{
					return $this->_总页码;
				}
				else
				{
					return 1;					
				}
			}
			else
			{
				return 1;
			}
		}
	}

	private function setUrl()
	{
		$_url = $_SERVER["REQUEST_URI"];
		$_para = parse_url($_url);
		if (isset($_para['query'])) {
			unset($_query['page']);
			$_url = $_para['path']."?".http_build_query($_para['query']);
		}
		return $_url;
	}

	private function pageList()
	{
		$_pagehtml = '';

		for ($i = $this->bothnum;$i>=1;$i--){
			$_page = $this->_当前页码 - $i;
			if ($_page < 1) break;
			$_pagehtml .= '<a href="'.$this->_url.'&page='.$_page.'">'.$_page.'</a>';	
		}

		$_pagehtml .= '<span class="me">'.$this->_当前页码.'</span>';

		for ($i = 1;$i<= $this->bothnum;$i++){ 
			$_page = $this->_当前页码 + $i;
			if($_page > $this->_总页码) break;
			$_pagehtml .= '<a href="'.$this->_url.'&page='.$_page.'">'.$_page.'</a>';	
		}

		return $_pagehtml;
	}

	private function first()
	{
		if ($this->_当前页码 > $this->bothnum + 1) {
			return '<a href = "'.$this->url.'">1</a>...';
		}
	}

	private function prev()
	{
		if ($this->_当前页码 == 1) {
			return '<span class="disabled">上一页</span>';
		}
		else
		{
			return '<a href="'.$this->url.'&page='.($this->_当前页码 - 1).'">上一页</a>';
		}
	}

	private function next()
	{
		if ($this->_当前页码 == $this->_总页码) {
			return '<span class="disabled">下一页</span>';
		}
		else
		{
			return '<a href="'.$this->url.'&page='.($this->_当前页码 + 1).'">下一页</a>';
		}
	}

	private function last()
	{
		if($this->_总页码 - $this->_当前页码 > $this->bothnum)
		{
			return '...<a href="'.$this->url.'&page='.$this->_总页码.'">'.$this->_总页码.'</a>';
		}
	}

	public function showpage()
	{
		$_page .= $this->first();
		$_page .= $this->pageList();
		$_page .= $this->last();
		$_page .= $this->prev();
		$_page .= $this->next();
		return $_page;
	}
}

?>