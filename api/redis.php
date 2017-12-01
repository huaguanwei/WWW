<?php
/**
 * Redis缓存操作
 * @author hxm
 * @version 1.0
 * @since 2015.05.04
 */
class RCache  
{
  private $redis = null; //redis对象
    
  private $sId  = 1;  //servier服务ID
    
  private $con  = null;//链接资源
    
  /**
   * 初始化Redis
   *
   * @return Object
   */
  public function __construct()
  {
    if ( !class_exists('Redis') )
    {
      throw new QException('PHP extension does not exist: Redis');
    }
    $this->redis = new Redis();
  }
    
  /**
   * 连接redis服务
   */
  private function connect()
  {
  	include 'redisconfig.php';
    if(! isset($config) )
    {
      echo ('缓存配置文件不存在');
    }
    $host = $config['host'];
    $port = $config['port'];
    $host = $host;
    $port = $port;
    try {
      $this->redis->connect( $host , $port );
    } catch (Exception $e) {
      exit('redis连接失败！'.$e);
    }
  }
    
  /**
   * 写入缓存
   */
  public function set( $key , $value , $expire = 0)
  {
    $data = $this->get($key); //如果已经存在key值
    if( $data ) 
    {
      return $this->redis->getset( $key , $value);
    } else {
      return $this->redis->set( $key , $value);
    }
  }
    
  /**
   * 读取缓存
   */
  public function get($key)
  {
    $this->connect();
    return $this->redis->get($key);
  }
  
  /**
   * 清洗（删除）已经存储的所有的元素
   */
  public function flush(){
  	$this->connect();
  	$this->redis->flushdb();
  	
  }
  /**
   * 删除
   */
  public function del($key)
  {
    $this->connect();
    return $this->redis->del($key);
  }
  /**
   * 析构函数
   * 最后关闭memcache
   */
  public function __destruct()
  {
    if($this->redis)
    {
      $this->redis->close();
    }
  }
}