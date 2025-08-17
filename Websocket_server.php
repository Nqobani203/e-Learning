<?php
// websocket_server.php
$host = '127.0.0.1'; // Localhost
$port = 8080; // Port for the WebSocket server

// Create a WebSocket server
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);
socket_listen($socket);

$clients = [];

while (true) {
    $changed_sockets = $clients;
    $changed_sockets[] = $socket;
    socket_select($changed_sockets, $null, $null, 0, 10);

    if (in_array($socket, $changed_sockets)) {
        $new_socket = socket_accept($socket);
        $clients[] = $new_socket;
        // Perform WebSocket handshake
        handshake($new_socket);
        unset($changed_sockets[array_search($socket, $changed_sockets)]);
    }

    foreach ($changed_sockets as $client_socket) {
        if (socket_recv($client_socket, $buffer, 1024, 0) >= 1) {
            $decodedData = json_decode($buffer, true);
            if ($decodedData['type'] === 'class_start') {
                foreach ($clients as $sock) {
                    sendToClient($sock, json_encode(['type' => 'class_start']));
                }
            }
        }
    }
}

function handshake($client_socket) {
    $headers = socket_read($client_socket, 1024);
    // ... Process headers and complete handshake here ...
    $handshake_response = "HTTP/1.1 101 Switching Protocols\r\n" .
                          "Upgrade: websocket\r\n" .
                          "Connection: Upgrade\r\n" .
                          "Sec-WebSocket-Accept: " . base64_encode(pack('H*')) . "\r\n\r\n";
    socket_write($client_socket, $handshake_response, strlen($handshake_response));
}

function sendToClient($client, $msg) {
    $msg = chr(129) . chr(strlen($msg)) . $msg;
    socket_write($client, $msg, strlen($msg));
}
