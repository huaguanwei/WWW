<?php
		//配置信息：
		$config = array(
						'tablepre' => '',//数据库表前缀
						'db'        => '',//库名
						'table'		=> '',//表名
						'dbHost' 	=> '192.168.0.80',
						'dbUser' 	=> 'SCOTT',
						'dbPassword'=>'root',
						'dbName' 	=> 'SCOTT',
						'PORT'    => '1521',
						'dbCharset' => "AL32UTF8",
						'data'		=> array(), //中间数据容器
						'fields'	=> array(), //字段信息
						'sql'		=> array(), //sql集合，调试用
		);
		$status = array(
						    'CODE0' => 0,
						    'MSG0'  => "正常",
						
						    'CODE1' => 1,
						    'MSG1'  => "用户名不存在",
						
						    'CODE2' => 2,
						    'MSG2'  => "密码错误",
						
						    'CODE3' => 3,
						    'MSG3'  => "请重新登陆",
						
						    'CODE4' => 4,
						    'MSG4'  => "参数错误",
						
						    'CODE5' => 5,
						    'MSG5'  => "请求失败",
						
						    'CODE6' => 6,
						    'MSG6'  => "验证码错误",
						
						    'CODE7' => 7,
						    'MSG7'  => "系统异常"
						);
?>