<?php
	Class Json {
		public function NewArr($result = ''){
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
			$re = array(
							'code' => CODE0,
							'msg'  => MSG0,
							'data' => $result,
			);
			//$back = array_merge($status,$result);
			$new_back = json_encode($re);
			return $new_back;
		}
	}
?>