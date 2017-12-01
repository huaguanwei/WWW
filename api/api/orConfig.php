<?php
		//配置信息：
		$config = array(
            'tablepre' => '',//数据库表前缀
            'db'        => '',//库名
            'table'    => '',//表名
            'dbHost'   => '192.168.0.80',
            'dbUser' 	=> 'SCOTT',
            'dbPassword'=>'root',
            'dbName'   => 'SCOTT',
            'PORT'    => '1521',
            'SID'    => 'orcl',
            'dbCharset' => "AL32UTF8",
            'data'    => array(), //中间数据容器
            'fields'  => array(), //字段信息
            'sql'    => array(), //sql集合，调试用
            'dbDebug' => true,//默认开启错误提示，false为不提示
    	);
		$status = array(
						    'CODE0' => "0",
						    'MSG0'  => "正常",
						
						    'CODE1' => "1",
						    'MSG1'  => "缺少参数",
						
						    'CODE2' => "2",
						    'MSG2'  => "密码错误",
						
						    'CODE3' => "3",
						    'MSG3'  => "账号不存在",
						
						    'CODE4' => "4",
						    'MSG4'  => "参数错误",
						);
?>