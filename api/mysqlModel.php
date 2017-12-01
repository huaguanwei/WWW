	<?php
	header("Content-type: text/html; charset=utf-8");
	class Model {

		private $class; //当前类名
		private static $link; //数据库链接句柄
		private $data = array(); //中间数据容器
		private $condition = ''; //查询条件
		private $fields = array(); //字段信息
		private $sql = array(); //sql集合，调试用
		private $back = array(); //需要返回的处理结果
		public $primaryKey = 'id'; //表主键		
				
		//构造函数初始化
		public function __construct($dbParam = array()) {
			$this->connect();
			$this->init($dbParam);
			
		}
		
		
		//链接数据库
		private function connect() {
			include 'myConfig.php';
			self::$link = @mysql_connect($config['dbHost'], $config['dbUser'], $config['dbPassword'], true);
			mysql_query("SET character_set_connection=".$config['dbCharset'].",character_set_results=".$config['dbCharset'].",character_set_client=binary", self::$link);
			@mysql_select_db($config['dbName'], self::$link);
			return self::$link;
		}
		
		
		//表基本信息初始化
		protected function init($name = '') {
			include 'myConfig.php';
			$this->class = get_class($this);
			if($config['tablepre']){
				$this->table = $config['tablepre'].$name;
			}else{
				$this->table = $name;
			}
			return $this;
		}
		
		
		//字段信息处理
		private function implodefields($data) {
			if (!is_array($data)) {
			$data = array();
			}
			$this->fields = !empty($this->data['fields']) ? array_merge($this->data['fields'], $data) : $data;
			foreach($this->fields as $key => $value) {
			$fieldsNameValueStr[] = "`$key`='$value'";
			$fieldsNameStr[] = "`$key`";
			$fieldsValueStr[] = "'$value'";
			}
			return array($fieldsNameValueStr, $fieldsNameStr, $fieldsValueStr);
		}
		
		//字段信息处理
		public function fields($fields){
			$data = explode(',',$fields);
			$re = '';
			foreach($data as $key=>$val){
				$re .= "`$val`,";
			}
			return substr($re,0,-1);
		}
		
		//where条件处理
		private function condition($where = NULL) {
			$sql_where = '';
			if($where){
				foreach($where as $key=>$val){
					   //$args_key = array_shift($val);
							$sql_where .= "AND `$key`= '$val'" ;
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
		
		
		//结果数量
		public function getCount($where = '', $fields = '*') {
			$this->condition($where);
			$sql = "SELECT count({$fields}) as count FROM `{$this->table}` {$this->condition}";
			$data = $this->fetch($this->query($sql));
			return	$data[0]['count'];
		}
		
		
		
		//插入数据
		public function insert($data = array()) {
			$fields = $this->implodefields($data);
			$sql = "insert INTO `{$this->table}` (".implode(', ',$fields[1]).") values (".implode(', ',$fields[2]).")";
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
			$sql = "UPDATE `{$this->table}` SET ".implode(', ',$fields[0])." {$this->condition}";
			$this->query($sql);
		}
		
		
		//删除数据
		public function delete($where = NULL) {
			if(!is_array($where) && strtolower(substr(trim($where), 0, 6)) == 'delete'){
				$sql = $where;
			}else{
			$this->condition($where);
			$sql = "DELETE FROM `{$this->table}` {$this->condition}";
			}
			$this->query($sql);
		}
			
			
		//整合sql语句查询数据
		public function select($fields = NULL) {
			if($fields){
				$data = $this->fields($fields);
				$this->sql = "SELECT ".$data." FROM `{$this->table}` ".$this->sql;
			}else{
				$this->sql = "SELECT * FROM `{$this->table}` ".$this->sql;
			}
				return $this->back($this->fetch($this->query($this->sql)));
		}
		
		//联表查询
		public function selects($fields1 = NULL ,$fields2 = NULL ){
			$sql_where1 = $sql_where2 = $sql_wheres = $sql_wheress = '';
			if(!$fields1 && !$fields2){
				$this->sql = "SELECT * FROM `{$this->table}` ".$this->sql;
			}else if($fields1 && $fields2){
				$data1 = explode(',',$fields1);
				$data2 = explode(',',$fields2);
				foreach($data1 as $key => $val){
					$sql_wheres .= " "."`$this->table`".".".$val.",";
					$sql_where1 = substr($sql_wheres,0,strlen($sql_wheress)-1); 
				}
				foreach($data2 as $key => $v){
					$sql_wheress .= " "."`$this->tables`".".".$v.",";
					$sql_where2 = substr($sql_wheress,0,strlen($sql_wheress)-1); 
				}
				$this->sql = "SELECT ".$sql_where1.",".$sql_where2." FROM `{$this->table}` ".$this->sql;
			}else if(!$fields1 && $fields2){
				$data2 = explode(',',$fields2);
				foreach($data2 as $key => $v){
					$sql_wheress .= " "."`$this->tables`".".".$v.",";
					$sql_where2 = substr($sql_wheress,0,strlen($sql_wheress)-1); 
				}
				$this->sql = "SELECT ".$sql_where2." FROM `{$this->table}` ".$this->sql;
			}else if($fields1 && !$fields2){
				$data1 = explode(',',$fields1);
				foreach($data1 as $key => $val){
					$sql_wheres .= " "."`$this->table`".".".$val.",";
					$sql_where1 = substr($sql_wheres,0,strlen($sql_wheress)-1); 
				}
				$this->sql = "SELECT ".$sql_where1." FROM `{$this->table}` ".$this->sql;
			}
			return $this->back($this->fetch($this->query($this->sql)));
		}
		
		//联表查询
		public function join($table = NULL){
			if($table){
				include 'myConfig.php';
				if($config['tablepre']){
					$this->tables = $config['tablepre'].$table;
				}else{
					$this->tables = $table;
				}
				//select id, name, action from user as u left join user_action a on u.id = a.user_id
				$this->sql = " LEFT JOIN `{$this->tables}`";
			}else{
				return false;
			}
			return $this;
		}
		
		
		//联表查询
		public function on($where = NULL){
			if($where){
				$re = explode(',',$where);
				$this->sql = $this->sql." ON "."`$this->table`".".".$re['0']."="."`$this->tables`
				".".".$re['1'];
			}else{
				return false;
			}
			return $this;
		}
		
		//条件语句
		public function where($sql = NULL){
			if($sql){
				$this->condition($sql);
				$this->sql = "{$this->condition}";
			}
			return $this;
		}
		
		//ORDER BY语句
		public function order($order = NULL,$sort = ''){
			if($order){
				$data = $this->fields($order);
				$this->sql = $this->sql." ORDER BY ".$data." ".$sort;
			}
			return $this;
		}
		
		//LIMIT语句
		public function limit($start = '',$end = ''){
			if($start && $end){
				$this->sql = $this->sql. " LIMIT " .$start.','.$end;
			}else if(!$end){
				$this->sql = $this->sql. " LIMIT " .$start;
			}
			return $this;
		}
		
		public function query($sql) {
			$result = @mysql_query($sql, self::$link);
			return $result;
			
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
        include 'myConfig.php';
        $re = array(
            'code' => $status['CODE0'],
            'msg'  => $status['MSG0'],
            'data' => $result,
        );
        return json_encode($re);
    	}
		
		//返回结果$onlyone为true返回一条否则返回所有,$type有MYSQL_ASSOC,MYSQL_NUM,MYSQL_BOTH
		public function fetch($result, $type = MYSQL_ASSOC) {
				$b = array();
				while($a = @mysql_fetch_array($result,$type)){  
						$b[] = $a;
					}
				return $b;		
		}
		
		
		//析构函数
		public function __destruct(){ 
			//var_dump($this->sql);
			//mysql_free_result($this->table);
			//mysql_free_result($this->sql);    
			mysql_close($this->connect());
		}
	}