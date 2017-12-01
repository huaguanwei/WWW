<?php
use Workerman\Worker;
include __DIR__ . '/ceshi.php';
require_once __DIR__ . '/Autoloader.php';
$a = new Model();
echo $a->a();
// 注意：这里与上个例子不通，使用的是websocket协议
$ws_worker = new Worker("websocket://0.0.0.0:2000/chat");

// 启动4个进程对外提供服务
$ws_worker->count = 4;

// 当收到客户端发来的数据后返回hello $data给客户端
$ws_worker->onMessage = function($connection, $data)
{
    // 向客户端发送hello $data
    $connection->send('hello socket!' . $data);
};

// 运行worker
Worker::runAll();