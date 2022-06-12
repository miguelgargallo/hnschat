<?php
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;

    require "/var/www/html/hnschat/etc/vendor/autoload.php";
    include "/var/www/html/hnschat/etc/includes.php";
    include "/var/www/html/hnschat/etc/ws/functions.php";

    loadClasses();
    connectRedis();
    loadEmojis();

    $socket = new WebSocket();
    $server = IoServer::factory(new HttpServer(new WsServer($socket)), 4444);

    $server->loop->addPeriodicTimer(1, function() use ($socket) {
        Users::active();
    });

    $server->loop->addPeriodicTimer(1, function() use ($socket) {
        $users = [];
        $typers = Users::typing();

        if ($typers) {
            foreach ($typers as $key => $typer) {
                $typing = $typer->typing;

                if ($typing) {
                    $users = Conversations::conversationForID($typing["to"])->users;

                    if ($users) {
                        foreach ($users as $key => $user) {
                            if (!in_array($typing["from"], $user->domains)) {
                                $user->socket->send("TYPING ".json_encode($typing));
                            }
                        }
                    }
                }
            }
        }
    });
    $server->run();
?>