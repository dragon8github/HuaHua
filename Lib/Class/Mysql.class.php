<?php
/**
 * MySql操作类2015版
 * 作者：咖啡兽兽   287962566@qq.com
 * 使用说明：
 * 		//包含文件
 * 		inclode 'Mysql.class.php';
 * 		//写个方法来实例化数据库对象
 * 		function newMySQL($table){
 * 			//DSN参数，建议从配置文件获取
 * 			$dsn = array(
 * 					'DB_HOST'		=>	'loaclhost',	//主机地址
 *					'DB_NAME'		=>	'mydb',		//数据库名
 *					'DB_PORT'		=>	3306,			//端口
 *					'DB_PREFIX'		=>	'kf',			//表前缀
 *					'DB_CHARSET'	=>	'utf8',			//数据库编码
 *					'DB_USER'		=>	'root',			//用户名
 *					'DB_PWD'		=>	'123456',	//密码
 * 			);
 * 			//表名
 * 			$dsn['DB_TABLE'] = $table;
 * 			//返回数据库对象
 * 			return Mysql::start($dsn);
 * 		}
 * 		//传入表名，获取数据库对象
 * 		$Sql = newMySQL('user');
 * 		//若要切换操作表，可直接给table成员属性赋值
 * 		$Sql->table = 'product';
 * 		//插入数据
 * 		$data['name'] = '小白';
 * 		$data['pwd'] = md5('123456');
 * 		$data['age'] = 25;
 * 		$data['sex'] = '男';
 * 		$Sql->add($data);
 * 		
 * 		//更新数据
 * 		$data['name'] = '小黑';
 * 		$where = "id=1";
 * 		$Sql->where($where)->save($data);
 * 		//指定字段加1
 * 		$Sql->where($where)->sum('age');
 * 		//指定字段加N
 * 		$Sql->where($where)->sum('age',10);
 * 
 * 		//查询数据
 * 		$where = "sex='男' and age>20";
 * 		//取回所有符合条件的数据
 * 		$arr = $Sql->where($where)->select();
 * 		//可用field指定要取回的字段
 * 		$arr = $Sql->field('name,sex')->where($where)->select();
 * 		//取回指定范围  limit  两个参数都是整数型
 * 		$arr = $Sql->where($where)->limit(偏移量,最大行数)->select();
 * 		//指定排序  desc  or  asc  可指定多个字段，以","隔开
 * 		$arr = $Sql->where($where)->order("sex desc")->select();
 * 		//取回第一行
 * 		$find = $Sql->where($where)->find();
 * 		//单独取回指定字段,若符合条件的数据 >1行，则返回数组
 * 		$field = $Sql->where('id=1')->getField('name');
 * 		//取符合条件的总行数
 * 		$count = $Sql->where($where)->getCount();
 * 		
 * 		//删除数据
 * 		$Sql->where($where)->delete();
 */
class Mysql
{
	private $host;   //主机地址
	private $dbname; //数据库名
	private $port;  //数据库端口
	private $username; //用户名
	private $password; //密码
	private $charset; //数据库编码
	static $SQL; //数据库对象
	static $obj = null;
	public $table; //操作表
	private $pre = null;
	private $opt;  //选项
	
	/**
	 * 初始化数据库
	 * @param Array $dsn 数据库连接参数
	 */
	private function __construct($dsn){
		$this->host = $dsn['DB_HOST'];
		$this->dbname = $dsn['DB_NAME'];
		$this->username = $dsn['DB_USER'];
		$this->password = $dsn['DB_PWD'];
		$this->port = $dsn['DB_PORT'];
		$this->charset = $dsn['DB_CHARSET'];
		//$this->table = $dsn['DB_PREFIX'].$dsn['DB_TABLE'];
		$this->opt['where'] = '';
		$this->opt['order'] = '';
		$this->opt['limit'] = '';
		$this->opt['field'] = null;
		$this->connect();
	}
	
	/*
	 * Lee:
	 * 还原默认纯洁的配置环境
	 * */
	public function reset()
	{
	    $this->opt['where'] = '';
	    $this->opt['order'] = '';
	    $this->opt['limit'] = '';
	    $this->opt['field'] = null;
	}
	
	/**
	 * 连接数据库
	 * @return Bool
	 */
	private function connect(){
		try{
			 $dsn  =  'mysql:host='.$this->host.';dbname='.$this->dbname.';port='.$this->port;
			 $options  = array(
			     PDO :: MYSQL_ATTR_INIT_COMMAND  =>  'SET NAMES '.$this->charset,
			); 
 			self::$SQL = new  PDO ($dsn,$this->username,$this->password,$options);
			//设置错误报告
			self::$SQL->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			echo "数据库连接失败：".$e->getMessage();
			exit;
		}
		return true;
	}
	
	/**
	 * @param Array $dsn 数据库连接参数(DB_HOST[主机],DB_NAME[数据库名],DB_PORT[端口],DB_CHARSET[编码],DB_USER[帐户],DB_PWD[密码],DB_TABLE[表名])
	 * @return Object
	 */
	static function start($dsn){
		//判断对象是否存在，若已存在则直接返回该对象
		//if(is_null(self::$obj)){
		self::$obj = null;
		self::$obj = new self($dsn);
		//}
		return self::$obj;
	}
	
	/**
	 * 插入数据(成功返回新插入的 自动增长主键，失败返回0)
	 * @param Array $data 欲插入的数据，对应表中的键与值
	 * @param Int 0 准备语句 ,1 执行语句,2(默认)准备并执行语句 
	 * @return Int 
	 */
	public function add($data,$type = 2){
		try{
			//构造SQL语句
			$count = 0;
			$a = '';
			foreach($data as $k=>$v){
				if($count != 0){
					$a = ',';
				}
				@$key .= $a.$k;
				@$bind .=$a.":$k";
				$count++;
			}
			$s = "insert into {$this->table}({$key}) values({$bind})";
		 
			//发送语句
			if($type != 1){
				$this->pre = self::$SQL->prepare($s);
				if($type == 0 && is_object($this->pre) == true){
					return 1;
				}
			}
			//执行语句
			if($type != 0){
				$this->pre->execute($data);
			}
			//重置各种条件
			$this->reset();
			//返回最新主键
			return self::$SQL->lastinsertid();
		}catch(PDOException $e){
			exit("错误：".$e->getMessage());
		}
		
	}
	/**
	 * 更新数据(返回受影响行)
	 * @param void $data 传入更新数组
	 * @return Int
	 */
	public function save($data){
		try{
			//构造SQL语句
			$count = 0;
			$a = '';
			foreach($data as $k=>$v)
			{
				if($count != 0){
					$a = ',';
				}
				@$bind .=$a."$k='$v'";
				$count++;
			}
			$s = "update {$this->table} set {$bind} {$this->opt['where']}";  
			//发送语句
			$this->pre = self::$SQL->prepare($s);
			//执行语句
			$this->pre->execute($data);
			//重置各种条件
			$this->reset();
			//返回受影响行
			return $this->pre->rowCount();
		}catch(PDOException $e){
			exit("错误：".$e->getMessage());
		}
	}
	
	/**
	 * 指定字段加N，默认加1 
	 * @param String $key 字段
	 * @param Int $num [可选]裕增加的数值，默认1
	 * @return Int
	 */
	public function sum($key,$num){
		try{
			$s = "update {$this->table} set {$key}={$key}+{$num} {$this->opt['where']}";	
			
			//发送语句
			$this->pre = self::$SQL->prepare($s);
			//执行语句
			$this->pre->execute();
			//返回受影响行
			return $this->pre->rowCount();
		}catch(PDOException $e){
			exit("错误：".$e->getMessage());
		}
	}
	
	
	
	
	
	
	
	/**
	 * 删除数据(返回受影响行)
	 * @return Int
	 */
	public function delete(){
		try{
			$s = "delete from {$this->table} {$this->opt['where']}";
			//发送语句
			$this->pre = self::$SQL->prepare($s);
			//执行语句
			$this->pre->execute();
			//返回受影响行
			return $this->pre->rowCount();
		}catch(PDOException $e){
			exit("错误：".$e->getMessage());
		}
	}
	
	/*
	 * Lee函数：
	 * 只推荐不存在任何参数的sql语句使用该函数
	 * */
	public  function query($str)
	{
	    try 
	    {
	        $s = $str;
	        //发送语句
			$this->pre = self::$SQL->prepare($s);
			//执行语句
			$this->pre->execute();
			//设置返回关联数组
			$this->pre->setFetchMode(PDO::FETCH_ASSOC);
			//重置各种条件
			$this->reset();
			return $this->pre->fetchAll();
	    }
	    catch(PDOException $e)
	    {
	       exit("错误：".$e->getMessage());
	    }
	}
	
	/*
	 * Lee函数：
	 * 只推荐不存在任何参数的sql语句使用该函数
	 * */
	public  function send($str)
	{
	    try
	    {
	        $s = $str;
	        //发送语句
	        $this->pre = self::$SQL->prepare($s);
	        //执行语句
	        $this->pre->execute();
	        //重置各种条件
	        $this->reset();     
	    }
	    catch(PDOException $e)
	    {	        
	        exit("错误：".$e->getMessage());
	    }
	}
	
	
	/**
	 * 取数据集(返回符合条件的所有数据)
	 * @return array 
	 */
	public function select(){
		try{
			$s = "select ".($this->opt['field']? $this->opt['field']:'*')." from {$this->table} {$this->opt['where']} {$this->opt['order']} {$this->opt['limit']}";
			
			//发送语句
			$this->pre = self::$SQL->prepare($s);
			//执行语句
			$this->pre->execute();
			//设置返回关联数组
			$this->pre->setFetchMode(PDO::FETCH_ASSOC);
			//重置各种条件
			$this->reset();
			return $this->pre->fetchAll();
		}
		catch(PDOException $e)
		{
			exit("错误：".$e->getMessage());
		}
	}
	/**
	 * 取一行数据(只返回第一条数据)
	 * @return array  
	 */
	public function find(){
		try{
			$s = "select ".($this->opt['field']? $this->opt['field']:'*')." from {$this->table} {$this->opt['where']} {$this->opt['order']} limit 1";
			
			$this->pre = self::$SQL->prepare($s);
			//执行语句
			$this->pre->execute();
			//设置返回关联数组
			$this->pre->setFetchMode(PDO::FETCH_ASSOC);
			$arr = $this->pre->fetchAll();
			//重置各种条件
			$this->reset();
			return @$arr[0];
		}catch(PDOException $e){
			exit("错误：".$e->getMessage());
		}
	}
	/**
	 * 取指定字段值(当只有一条数据时，直接返回键值，否则返回数组,失败返回false)
	 * @param String $key 指定字段名
	 * @return mixed
	 */
	public function getField($key){
		$opt['field'] = $key;
		$selArr = $this->select();
		foreach($selArr as $number=>$value){
			$retuls[$number] = $value[$key];
		}
		
		if(@$retuls == null) $retuls=false;
		//判断是否单只有一条数据
		if(count($retuls) == 1 and $retuls != false){
			$retuls = $retuls[0];
		}
		return $retuls;
	}
	
	
	/**
	 * 设定指定字段值
	 * @param String $key 指定字段名
	 * @param mixed $value 字段值
	 * @return mixed
	 */
	public function setField($key,$value){
		return $this->save(array($key=>$value));
	}
	
	/**
	 * 取符合条件的数据总行数
	 */
	public function getCount(){
		try{
			$s = "select count(*) from {$this->table} {$this->opt['where']}";
			$this->pre = self::$SQL->prepare($s);
			//执行语句
			$this->pre->execute();
			$count = $this->pre->fetch();
			return Intval($count[0]);
		}catch(PDOException $e){
			exit("错误：".$e->getMessage());
		}
	}
	/**
	 * 查询条件
	 * @param String $where 查询条件
	 * @return this
	 */
	public function where($where){
		$this->opt['where'] = $where? "where ".$where:'';
		return $this;
	}
	
	/**
	 * 指定排序
	 * @param String $order 排序规则  desc.倒序    asc.正序
	 * @return this
	 */
	public function order($order){
		$this->opt['order'] = $order? "order by ".$order:'';
		return $this;
	}
	
	/**
	 * 指定欲取的数据条数
	 * @param Int $min 只传一个参数时，传入欲取的数据条数；否则传入记录偏移量，从0开始
	 * @param Int $max 欲取的的数据条数
	 * @return this
	 */
	public function limit($min,$max=null){
		$this->opt['limit'] = "limit ".intval($min).($max? ','.intval($max):'');
		return $this;
	}
	
	/**
	 * 指定欲取的字段
	 * @param String $field 指定字段名称，多个请以(,)分号隔开
	 * @param this
	 */
	public function field($field){
		$this->opt['field'] = $field;
		return $this;
	}

}

?>