<?php

$server = new swoole_websocket_server("0.0.0.0", 9505);

$conn;

$server->on('open', function (swoole_websocket_server $server, $request)  {
    global $conn;
    echo "server: handshake success with fd{$request->fd}\n";
    $conn [$request->fd] = $request->fd;
    foreach ($conn as $v){
        $server->push($v, $request->fd.' 上线');
    }
});

$server->on('message', function (swoole_websocket_server $server, $frame){
    global $conn;
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    foreach ($conn as $v){
        $server->push($v, $frame->fd.' : '.$frame->data);
    }

});

$server->on('close', function (swoole_websocket_server $server, $fd) {
    global $conn;
    unset($conn[$fd]);
    foreach ($conn as $v){
        $server->push($v, $fd.' 下线');
    }

    echo "client {$fd} closed\n";
});
$server->start();