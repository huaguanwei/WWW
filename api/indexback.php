<?php  
$redis = new redis();  
$redis->connect('127.0.0.1', 6379);  
$redis->delete('test');  
var_dump($redis->lpush("test","111"));   //结果：int(1)  
var_dump($redis->rpush("test","222"));   //结果：int(2)  
var_dump($redis->lgetrange('test',-2,-1));
?>