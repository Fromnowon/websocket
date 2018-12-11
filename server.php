<?php

class SocketService
{
    private $address = '127.0.0.1';
    private $port = 8083;
    private $_sockets;

    public function __construct($address = '', $port = '')
    {
        if (!empty($address)) {
            $this->address = $address;
        }
        if (!empty($port)) {
            $this->port = $port;
        }
    }

    public function service()
    {
        //获取tcp协议号码。
        $tcp = getprotobyname("tcp");
        $sock = socket_create(AF_INET, SOCK_STREAM, $tcp);
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
        if ($sock < 0) {
            throw new Exception("failed to create socket: " . socket_strerror($sock) . "\n");
        }
        socket_bind($sock, $this->address, $this->port);
        socket_listen($sock, $this->port);
        echo "listen on $this->address $this->port ... \n";
        $this->_sockets = $sock;
    }

    public function run()
    {
        $this->service();
        $clients[] = $this->_sockets;
        $result_socket = null;
        while (true) {
            $changes = $clients;
            $write = NULL;
            $except = NULL;
            socket_select($changes, $write, $except, NULL);//此方法会自动移除不活动的socket
            foreach ($changes as $key => $_sock) {
                if ($this->_sockets == $_sock) { //若为新连接，则握手并记录
                    if (($newClient = socket_accept($_sock)) === false) {
                        die('failed to accept socket: ' . socket_strerror($_sock) . "\n");
                    }
                    $line = trim(socket_read($newClient, 1024));
                    $this->handshaking($newClient, $line);
                    //获取client ip
                    socket_getpeername($newClient, $ip);
                    $clients[$ip] = $newClient;
                    echo '新连接：' . $ip . "\n";
                } else {
                    //接收消息
                    $t = socket_recv($_sock, $buffer, 2048, 0);
                    if ($t != false) {
                        //处理消息
                        $msg = $this->message($buffer);
                        echo $msg . "\n";
                        $msg = json_decode($msg, true);
                        //获取客户端ip
                        socket_getpeername($_sock, $client_ip);
                        //状态码：[-1:断开连接并清空用户信息, 0:一轮比赛结束，1:完成配置, 2:发送内容,3:返回参赛人数,4:返回客户端ip，5：开启测试解除限制]
                        switch ($msg['code']) {
                            default:
                                //其余莫名其妙的code全归为断开连接
                            case -1:
                                //弹出socket
                                unset($clients[$client_ip]);
                                socket_close($_sock);
                                //告诉result.php用户断开
                                if (is_resource($result_socket))
                                    $this->send($result_socket, json_encode(array('op' => 3, 'data' => $client_ip)));
                                break;
                            case 0:
                                //一轮结束，向客户端发送消息
                                foreach ($clients as $ip => $client) {
                                    if ($ip != 0 && $ip != $this->address) {
                                        $this->send($client, json_encode(array('op' => 0)));
                                    }
                                }
                                break;
                            case 1:
                                //完成配置
                                unset($clients[$client_ip]);
                                socket_close($_sock);
                                break;
                            case 2:
                                //发送消息
                                echo "{$client_ip} 发送:", $msg['content'], "\n";
                                if (is_resource($result_socket))
                                    $this->send($result_socket, json_encode(array('op' => 1, 'data' => $client_ip)));//告诉result可以取结果了
                                break;
                            case 3:
                                //首次联系result.php
                                $result_socket = $_sock;
                                $clients_arr = array();
                                foreach ($clients as $ip => $client) {
                                    if ($ip != 0 && $ip != $this->address) {
                                        array_push($clients_arr, $ip);
                                    }
                                }
                                $this->send($_sock, json_encode(array('op' => 0, 'data' => $clients_arr)));
                                break;
                            case 4:
                                //返回客户端ip
                                $this->send($_sock, json_encode(array('op' => 1, 'ip' => $client_ip)));
                                $this->send($result_socket, json_encode(array('op' => 2, 'data' => $client_ip)));//告诉result.php有新成员加入
                        }
                    } else {
                        //网络掉线会进入此分支
                        //弹出socket
                        socket_getpeername($_sock, $client_ip);
                        unset($clients[$client_ip]);
                        socket_close($_sock);
                        //告诉result.php用户断开
                        $this->send($result_socket, json_encode(array('op' => 3, 'data' => $client_ip)));
                    }
                }
            }
            echo "当前连接数:" . (count($clients) - 1) . "\n";
            print_r($clients);
            echo '---------------------------------------------------------' . "\n";
        }
    }


    /**
     * 握手处理
     * @param $newClient //socket
     * @return int 接收到的信息
     */
    public function handshaking($newClient, $line)
    {

        $headers = array();
        $lines = preg_split("/\r\n/", $line);
        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $this->address\r\n" .
            "WebSocket-Location: ws://$this->address:$this->port/websocket/websocket\r\n" .
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        return socket_write($newClient, $upgrade, strlen($upgrade));
    }

    /**
     * 解析接收数据
     * @param $buffer
     * @return null|string
     */
    public function message($buffer)
    {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;
        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else if ($len === 127) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        } else {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }

    /**
     * 发送数据
     * @param $newClinet //新接入的socket
     * @param $msg //要发送的数据
     * @return int|string
     */
    public function send($newClinet, $msg)
    {
        $msg = $this->frame($msg);
        socket_write($newClinet, $msg, strlen($msg));
    }

    public function frame($s)
    {
        $a = str_split($s, 125);
        if (count($a) == 1) {
            return "\x81" . chr(strlen($a[0])) . $a[0];
        }
        $ns = "";
        foreach ($a as $o) {
            $ns .= "\x81" . chr(strlen($o)) . $o;
        }
        return $ns;
    }

    /**
     * 关闭socket
     */
    public function close()
    {
        socket_close($this->_sockets);
    }
}

require_once './module/getHostIp.php';
require_once './module/sqlConn.php';
require_once './module/sqlHandler.php';
$ip = (new GetHostIP())->byShell();

if ($argc > 1) {
    if ($argc > 2) {
        echo "参数过多，请检查！\n";
    } else $ip = $argv[1];
}

//数据库操作
$conn = sql_conn("localhost", "root", "8ud7fh", 'my_contest');
$sql_handler = new sqlHandler($conn);
$date = date('Y-m-d H:i:s');
$sql_handler->update('server_ip', "`ip`='{$ip}',`update_time`='{$date}'", "`id`=1");

//运行服务
$sock = new SocketService($ip);
echo '若要修改服务器IP则将其作为参数传入重新执行，如：[php.exe server.php 192.168.1.1]' . "\n";
$sock->run();
