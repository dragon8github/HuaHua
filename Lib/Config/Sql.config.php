<?php


if(!@$_SESSION["test"])
{
        return array(
            'DB_HOST'		=>	'127.0.0.1',	//主机地址
            'DB_NAME'		=>	'huahua',			//数据库名
            'DB_PORT'		=>	'',			//端口
            'DB_PREFIX'		=>	'',			//表前缀
            'DB_CHARSET'	=>	'utf8',			//数据库编码
            'DB_USER'		=>	'root',			//用户名
            'DB_PWD'		=>	'asfdWf12312',		//密码
        );
}

return array(
    'DB_HOST'		=>	'localhost',	//主机地址
    'DB_NAME'		=>	'huahua',			//数据库名
    'DB_PORT'		=>	'',			//端口
    'DB_PREFIX'		=>	'',			//表前缀
    'DB_CHARSET'	=>	'utf8',			//数据库编码
    'DB_USER'		=>	'root',			//用户名
    'DB_PWD'		=>	'123',		//密码
);

?>