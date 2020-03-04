<?php

use Swoole\WebSocket\Server;

//端口号 必须开启阿里云的网络安全组 服务器必须关闭防火墙
$ws = new Server('0.0.0.0',9502);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    var_dump($request->fd, $request->get, $request->server);
    $ws->push($request->fd, "hello, welcome\n");
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $info = json_decode($frame->data,true);
    if($info['type'] == 'login'){
        //swoole 提供的redis客户端
        $redis = new Swoole\Coroutine\Redis();
        $key = "online_list";
        $redis->connect('127.0.0.1','6379');
        $list = $redis->get($key);
        $userlist = json_decode($list,true);
        if(empty($userlist)){
            $userlist =[];
        }
        $userlist[] = [
            'clint_id' =>$frame->fd,
            'username' =>$info['con'],
        ];
        $str =json_encode($userlist,JSON_UNESCAPED_UNICODE);
        $redis->set($key,$str);
        $message = [
            'type' =>'login',
            'is_me' => 1,
            'username' => $info['con'],
            'online_list'=>$userlist,
        ];
        $res = json_encode($message,JSON_UNESCAPED_UNICODE);
        $ws->push($frame->fd,$res);

        foreach ($userlist as $key=>$value){
            if($frame->fd != $value['clint_id']){
                $message = [
                    'type' =>'login',
                    'is_me' => 0,
                    'username' => $info['con'],
                    'online_list'=>$userlist,
                ];
                $res = json_encode($message,JSON_UNESCAPED_UNICODE);
                $ws->push($value['clint_id'],$res);
            }
        }
    }else if ($info['type'] == 'message'){
        //swoole 提供的redis客户端
        $redis = new Swoole\Coroutine\Redis();
        $key = "online_list";
        $redis->connect('127.0.0.1',6379);
        $list = $redis->get($key);
        $userlist = json_decode($list,true);

        foreach ($userlist as $k => $v) {
            if($v['clint_id'] == $frame->fd ){
                $name =$v['username'];
            }
        }
        foreach ($userlist as $key => $value) {
            if($value['clint_id'] == $frame->fd){
                $message = [
                    'type'=>'message',
                    'is_me' => 1,
                    'username' => $name,
                    'message' =>$info['con'],
                    'online_list'=>$userlist,
                ];
            }else{
                $message = [
                    'type'=>'message',
                    'is_me' => 0,
                    'username' => $name,
                    'message' =>$info['con'],
                    'online_list'=>$userlist,

                ];
            }

            $res = json_encode($message,JSON_UNESCAPED_UNICODE);
            $ws->push($value['clint_id'],$res);

        }

    }
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    //swoole 提供的redis客户端
    $redis = new Swoole\Coroutine\Redis();
    $key = "online_list";
    $redis->connect('127.0.0.1',6379);
    $list = $redis->get($key);
    $userlist = json_decode($list,true);
    foreach ($userlist as $key => $value){
        if($value['clint_id'] == $fd){
            unset($userlist[$key]);
            $name = $value['username'];
        }
    }
    $str = json_encode($userlist);
    $redis->set($key,$str);
    foreach ($userlist as $k =>$v) {
        $message = [
            'type'=>'loginout',
            'is_me' => 0,
            'username' => $name,
            'online_list'=>$userlist,
        ];
        $res = json_encode($message,JSON_UNESCAPED_UNICODE);
        $ws->push($v['clint_id'],$res);
    }
});

$ws->start();

