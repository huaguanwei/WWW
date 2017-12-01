<?php
	//include 'mysqlModel.php'; //------------- new_file.php封装函数文件
	//$a = new Model('user');//----------- 'DEPT'为表名称
	//include 'oracleModel.php'; //------------- new_file.php封装函数文件
	//$a = new Model('DEPT');//----------- 'DEPT'为表名称

	//查询
	//$result1 = $a->where(array('DEPTNO' => 10))->rownum(3)->order('DEPTNO','desc')->select('DEPTNO');
	//$result1 = $a->insert(array('DEPTNO' => 50,'DNAME' => "ACCOUNTING",'LOC' => "HUAHUAH"));
	//$all = new $result1;
	//$result1 = $a->where(array('u_id' => 2))->order('u_id','desc')->limit(3)->select('u_id');
	//$result1 = $a->join('h_menu')->on('id,id')->selects('id,username','id,menu_name');
	//$result1 = $a->join('menu')->on('u_id,m_id')->selects('u_id','m_id');
	//$result = $a->object_array($result1)['1'];
	//var_dump($result1);
	
	
	//测试mysql
	//$result1 = $a->where(array('u_id' => 1))->order('u_id','asc')->limit(0,3)->select('u_id,u_name');		//搜索
	//$result2 = $a->delete(array('u_id' => 25));			//删除
	//$result3 = $a->insert(array('u_id' => 25,'u_name' => "ceshi",'u_pwd' => 123456));		//插入
	//$result4 = $a->update(array('u_name' => "huaguanwei"),array('u_id' => 25));		//修改
	//$result5 = $a->join('menu')->on('u_id,m_id')->selects('u_id','m_id');				//联表查询
	
	
	//10	ACCOUNTING	NEW YORK
	//测试oracle
	//$result1 = $a->select();		//搜索
	//$result2 = $a->delete(array('DEPTNO' => 50));			//删除
	//$result3 = $a->insert(array('DEPTNO' => 50,'DNAME' => "HUAHUA"));			//插入
	//$result4 = $a->update(array('DNAME' => "ACCOUNTING"),array('DEPTNO' => 10));		//修改
	//$result5 = $a->join('menu')->on('id,id')->selects('id,username','id,menu_name');
	//var_dump($result1);
	//SELECT "h_user"."id","h_user"."username","h_menu"."id","h_menu"."menu_name" FROM "h_user" LEFT JOIN "h_menu" ON "h_user"."id" = "h_menu"."id"
	//$result6 = $a->joins('"h_menu" ON "h_user"."id" = "h_menu"."id"')
	//			->selectss('"h_user"."id","h_user"."username","h_menu"."id","h_menu"."menu_name"');
	//var_dump($result1);
	
	
	//测试redis
	// $redis = new Redis();  
    // $redis->connect("192.168.0.56","6379");  //php客户端设置的ip及端口  
    // //存储一个 值  
    // $redis->set("say","Hello World");  
    // echo $redis->get("say");     //应输出Hello World
		$table_name = '';
        try{
            if(empty($table_name)) throw new Exception('表名错误');
            if(empty($fields)) throw new Exception('字段错误');
            if(empty($gis_type)) throw new Exception('空间类型错误');
            if(empty($address)) throw new Exception('三维地址错误');
            if(empty($indoor)) throw new Exception('室内地图错误');
        }catch (Exception $e){
            $error = $e->getMessage();
            exit(json_encode(array('error' => $error)));
        }

?>
