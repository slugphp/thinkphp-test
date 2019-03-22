<?php
namespace Home\Controller;
use Think\Controller;
class SocketController extends Controller
{
    // 产生一个服务器
    public function index()
    {
        // 初始设置
        set_time_limit(0);
        error_reporting(E_ALL);

        // 创建连接
        $commonProtocol = getprotobyname("tcp");
        $socket = socket_create(AF_INET, SOCK_STREAM, $commonProtocol);
        socket_bind($socket, 'localhost', 1337);
        socket_listen($socket);

        // 接收
        $connection = socket_accept($socket);
        if ($connection) {
            socket_write($connection, "You have connected to the socket...\n\r");
        }
    }

    //
    public function test()
    {
        // Create the socket and connect
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $connection = socket_connect($socket,’localhost’, 1337);
        while ($buffer = socket_read($socket, 1024, PHP_NORMAL_READ)) {
            if ($buffer == 'NO DATA') {
                echo('<p>NO DATA</p>');
                break;
            } else {
                // Do something with the data in the buffer
                echo('<p>Buffer Data: ' . $buffer . '</p>');
            }
        }
        echo('<p>Writing to Socket</p>');
        // Write some test data to our socket
        if (!socket_write($socket, 'SOME DATA\r\n')) {
            echo('<p>Write failed</p>');
        }
        // Read any response from the socket
        while ($buffer = socket_read($socket, 1024, PHP_NORMAL_READ)) {
            echo('<p>Data sent was: SOME DATA<br> Response was:' . $buffer . '</p>');
        }
        echo('<p>Done Reading from Socket</p>');
    }
}