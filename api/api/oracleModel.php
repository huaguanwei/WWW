	<?php
	header("Content-type: text/html; charset=utf-8");
							//配置文件
	
	class Model {
        private $conn = ''; //数据库连接结果
		private $sql = ''; //sql集合，调试用
        private $resultsql = array();//sql数组集合
		private $back = array(); //需要返回的处理结果
		//构造函数初始化
		public function __construct($dbParam = array()) {
            session_start();
			$this->connect();
			$this->init($dbParam);
		}
		
		
		//链接数据库
		private function connect() {
		    include 'orConfig.php';
		    $this->conn = oci_connect($config['dbUser'],$config['dbPassword'],"(DEscriptION=(ADDRESS=(PROTOCOL =TCP)(HOST=".$config['dbHost'].")(PORT = ".$config['PORT']."))(CONNECT_DATA =(SID=".$config['SID'].")))",$config['dbCharset']);
            if(!$this->conn){
                $this->errdie('Could not connect Oracle: ');
            }
		    return $this->conn;
        }
		
		
		//左表基本信息初始化
		protected function init($name = '') {
			include 'orConfig.php';
			$this->class = get_class($this);
			if($config['tablepre']){
				$this->table = $config['tablepre'].$name;
			}else{
				$this->table = $name;
			}
			return $this;
		}
		//右表基本信息初始化
		protected function inits($name = '') {
			include 'orConfig.php';
			$this->class = get_class($this);
			if($config['tablepre']){
				$this->tables = $config['tablepre'].$name;
			}else{
				$this->tables = $name;
			}
			return $this->tables;
		}
		
		//字段信息处理
		public function fields($fields){
			$data = explode(',',$fields);
			$re = '';
			foreach($data as $key=>$val){
				$re .= "$val,";
			}
			return $this->check(substr($re,0,-1));
		}
		
		//字段信息处理
		private function implodefields($data) {
			if (!is_array($data)) {
			$data = array();
			}
			$this->fields = !empty($this->data['fields']) ? array_merge($this->data['fields'], $data) : $data;
			foreach($this->fields as $key => $value) {
			$fieldsNameValueStr[] = "$key='$value'";
			$fieldsNameStr[] = "$key";
			$fieldsValueStr[] = "'$value'";
			}
			return array($fieldsNameValueStr, $fieldsNameStr, $fieldsValueStr);
		}
		
		
		//where条件处理
		private function condition($where = NULL) {
			$sql_where = '';
			if($where){
				foreach($where as $key=>$val){
					  // $args_key = array_shift($val);
					  		$key = $this->check($key);
							$sql_where .= "AND $key= '$val'" ;
				}
			}
			if($sql_where){
            $sql_where = substr($sql_where, 3);
			}
			if($sql_where){
				$sql_where = " WHERE ".$sql_where;
			}
			$this->condition = $sql_where;
			
			return $this;
		}
		
		
		//插入数据
		public function insert($data = array()) {
			$fields = $this->implodefields($data);
			$sql = "insert INTO {$this->table} (".$this->check(implode(', ',$fields[1])).") values (".$this->check(implode(', ',$fields[2])).")";
			$this->query($sql);
		}
		
		
		//更新数据
		public function update($data = array() ,$where = '') {
			$numargs = func_num_args();
			if ($numargs == 1) {
			$where = $data;
			$data = array();
			}
			$fields = $this->implodefields($data);
			$this->condition($where);
			$sql = "UPDATE {$this->table} SET ".$this->check(implode(', ',$fields[0]))." {$this->condition}";
			$this->query($sql);
		}
		
		
		//删除数据
		public function delete($where = NULL) {
			if(!is_array($where) && strtolower(substr(trim($where), 0, 6)) == 'delete'){
			$sql = $where;
			}else{
			$this->condition($where);
			$sql = "DELETE FROM {$this->table} {$this->condition}";
			}
			$this->query($sql);
		}
			
			
		//查询数据
		public function select($fields = NULL) {
			
			if($fields){
				$data = $this->check($this->fields($fields));
				$this->sql = "SELECT ".$data." FROM {$this->table} ".$this->sql;
			}else{
				$this->sql = "SELECT * FROM {$this->table} ".$this->sql;
			}
				return $this->fetch($this->query($this->sql));
		}
		
		//条件语句
		public function where($sql = NULL){
			if($sql){
				$this->condition($sql);
				$this->sql = "{$this->condition}";
			}
			return $this;
		}
		
		//联表查询
		public function join($table = NULL){
			if($table){
				$table = $this->check($this->inits($table));
				$this->sql = " LEFT JOIN ".$table;
				$this->tables = $table;
			}else{
				return false;
			}
			return $this;
		}
		
		//链表查询条件
		public function on($where = NULL){
			if($where){
				$data = explode(',',$where);
				$this->table = $this->check($this->table);
				$this->sql = $this->sql." ON {$this->table}.".$this->check($data['0'])." = {$this->tables}.".$this->check($data['1']);
			}else{
				return false;
			}
			return $this;
		}
		
		//链表查询
		public function selects($fields1 = NULL ,$fields2 = NULL ){
			$sql_where1 = $sql_where2 = $sql_wheres = $sql_wheress = '';
			if(!$fields1 && !$fields2){
				$this->sql = "SELECT * FROM {$this->table} ".$this->sql;
			}else if($fields1 && $fields2){
				$data1 = explode(',',$fields1);
				$data2 = explode(',',$fields2);
				foreach($data1 as $key => $val){
					$val = $this->check($val);
					$sql_wheres .= " ".$this->table.".".$val.",";
					$sql_where1 = substr($sql_wheres,0,strlen($sql_wheress)-1); 
				}
				foreach($data2 as $key => $v){
					$v = $this->check($v);
					$sql_wheress .= " ".$this->tables.".".$v.",";
					$sql_where2 = substr($sql_wheress,0,strlen($sql_wheress)-1); 
				}
				$this->sql = "SELECT ".$sql_where1.",".$sql_where2." FROM {$this->table} ".$this->sql;
			}else if(!$fields1 && $fields2){
				$data2 = explode(',',$fields2);
				foreach($data2 as $key => $v){
					$v = $this->check($v);
					$sql_wheress .= " ".$this->tables.".".$v.",";
					$sql_where2 = substr($sql_wheress,0,strlen($sql_wheress)-1); 
				}
				$this->sql = "SELECT ".$sql_where2." FROM {$this->table} ".$this->sql;
			}else if($fields1 && !$fields2){
				$data1 = explode(',',$fields1);
				foreach($data1 as $key => $val){
					$val = $this->check($val);
					$sql_wheres .= " ".$this->table.".".$val.",";
					$sql_where1 = substr($sql_wheres,0,strlen($sql_wheress)-1); 
				}
				$this->sql = "SELECT ".$sql_where1." FROM {$this->table} ".$this->sql;
			}
			return $this->fetch($this->query($this->sql));
		}
		
		//rownum语句
		public function rownum($start = '',$end = ''){
			if($end){
				if(strpos($this->sql,'WHERE') === false){
					$this->sql = $this->sql. " WHERE ROWNUM >" .$start." AND ROWNUM < ".$end;
				}else{
					$this->sql = $this->sql. " AND ROWNUM >" .$start." AND ROWNUM < ".$end;
				}
			}else{
				if(strpos($this->sql,'WHERE') === false){
					$this->sql = $this->sql. " WHERE ROWNUM < " .$start;
				}else{
					$this->sql = $this->sql. " AND ROWNUM < " .$start;
				}
			}
			return $this;
		}
		
		//ORDER BY语句
		public function order($order = NULL,$sort = ''){
			if($order){
				$data = $this->check($this->fields($order));
				$this->sql = $this->sql." ORDER BY ".$data." ".$sort;
			}
			return $this;
		}
		
		
		
		//执行sql语句
		public function query($sql) {
			$sql = $this->sql;
			$result = oci_parse($this->conn,$sql);
			if(!$result){
                $this->errdie('Cannot use database');
            }
			oci_execute($result, OCI_DEFAULT); 
			return $result;
		}

		//执行原生sql语句
		public function sql_result($sql) {
			$result = oci_parse($this->conn,$sql);
			oci_execute($result, OCI_DEFAULT); 
			return $this->fetch($result);
		}
		
		//分页返回数据
		public function page($data,$page_num)
		{
			$count = count($data);
			$num = 10;
			$page_num_all = ceil($count/10);
			$start_key = $page_num*10-10;
			$end_key = $page_num*10-1;
			$res = array_slice($data, $start_key, 10);
			$return = array(
							'count' => $count,
							'page_num_all' => $page_num_all,
							'data' => $res
				);
			return $return;
		}

		//stdClass Object转array  
		public function object_array($array) {  
		    if(is_object($array)) {  
		        $array = (array)$array;  
		     } if(is_array($array)) {  
		         foreach($array as $key=>$value) {  
		             $array[] = $this->object_array($value);  
		             }  
		     }  
		     return $array;  
		} 
		
		//返回值处理
		public function back($result){
	        include 'orConfig.php';
	        $re = array(
	            'code' => $status['CODE0'],
	            'msg'  => $status['MSG0'],
	            'data' => $result,
	        );
	        return json_encode($re);
    	}
    	
    	//返回数据处理
    	public function re_json($code,$msg,$data)
    	{
    		include 'orConfig.php';
    		$array = array(
    				'code' => $status[$code], 
    				'msg'  => $status[$msg],
    				'data' => $data
    			);
    		return json_encode($array);
    	}

    	//整合字母大小写问题
    	public function check($re = NULL){
            if(strtoupper($re)===$re){
       			return $re;
    		}else{
      			$re = "\"$re\"";
                return $re;
    		}
    	}
    	
    	//多表查询（大于两个表）
	    public function joins($join){
	      if($join){
	        $this->resultsql[] = $this->sql = " LEFT JOIN ".$join;
	      }else{
	        return false;
	      }
	      return $this;
	    }
	    
	    //联表查询（大于两个表）
	    public function selectss($fields = NULL){
	      $result = '';
	      foreach($this->resultsql as $key=>$val){
	          $result .= $val;
	      }
	      if($fields){
	        $this->sql = "SELECT ".$fields." FROM {$this->table} ".$result;
	      }else{
	        $this->sql = "SELECT * FROM {$this->table} ".$result;
	      }
	      var_dump($this->sql);
	      return $this->fetch($this->query($this->sql));
	    }


//        //存入sessionid
//        public function  session($id){
//            session_start();
//            $_SESSION['id'] = $id;
//        }
//
//        //sessionid取出
//        public function  get_sessionid($key){
//            return array('id' => $_SESSION['id']);
//        }


        /**
         * 设置session
         */
        public static function set($name, $data, $expire=1440){
            $session_data = array();
            $session_data['data'] = $data;
            $session_data['expire'] = time()+$expire;
            $_SESSION[$name] = $session_data;
        }

        /**
         * 读取session
         */
        public static function get($name){
            if(isset($_SESSION[$name])){
                if($_SESSION[$name]['expire']>time()){
                    return $_SESSION[$name]['data'];
                }else{
                    self::clear($name);
                }
            }
            return false;
        }

        /**
         * 清除session
         */
        public static function clear($name){
            unset($_SESSION[$name]);
        }

		//返回结果$onlyone为true返回一条否则返回所有,$type有MYSQL_ASSOC,MYSQL_NUM,MYSQL_BOTH
	    public function fetch($result, $type = OCI_RETURN_NULLS) {
	        $data = array();
	        $a = oci_fetch_all($result,$results);
	        for ($i=0; $i < $a; $i++) { 
	          foreach ($results as $key => $value) {
	            $data[$i][$key] = $value[$i];
	          }
	        }  
	        return $data;    
	    }


	    //错误处理
        private function errdie($sql = '') {
            include 'orConfig.php';
            if ($config['dbDebug']) {
                if(!$this->sql){
                    die('</BR><B>ORACLE ERROR</B></BR>
                    '.$sql.'</BR>');
                }else{
                    die('</BR><B>ORACLE ERROR</B></BR>
                    SQL:'.$this->sql.'</BR>');
                }

            }
            die('DB ERROR！！！');
        }

//
	}