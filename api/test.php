<?php
/**
$redis = new Redis();
 
$redis->connect('127.0.0.1',6379);
 
$password = '123456';
 
$redis->auth($password);
 
$arr = array('1','2','3','4','5','6','7','8','9','10');
 
foreach($arr as $k=>$v){
 
  $redis->rpush("mylists",$v);
 
}
*/
include 'redis.php';
$redis= new RCache;
$redis->set('example','1111');
$redis->set('example','22222');
//$redis->set('example','1111');
//var_dump($redis->get('example','1111'));
//$redis->remove('example');
//var_dump($redis->del('example'));
//var_dump($redis->get('example','1111'));

?>