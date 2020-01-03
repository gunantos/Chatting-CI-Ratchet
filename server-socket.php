<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;

    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/socket_app.php';

$port = isset($_SERVER['CHAT_SERVER_PORT']) ? $_SERVER['CHAT_SERVER_PORT'] : 9191;
$bindAddr = isset($_SERVER['CHAT_BIND_ADDR']) ? $_SERVER['CHAT_BIND_ADDR'] : '0.0.0.0';
 $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Chat()
            )
        ),
        $port,
        $bindAddr
    );

    $server->run();
printf("Chat server running on %s:%s.\n--\n", $bindAddr, $port);
$server->run();