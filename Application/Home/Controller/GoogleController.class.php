<?php
namespace Home\Controller;
use Think\Controller;
class GoogleController extends Controller
{
    function __construct()
    {
        header("Content-type:text/html;Charset=utf-8");
        define('UC_KEY', 'xxx');

    }

    public function index()
    {
        $q   = '天气';
        $url = "http://173.194.14.53/custom?num=4&newwindow=1&hl=zh-CN&site=search&q=$q&btnG=Google+%E6%90%9C%E7%B4%A2";
        $s = file_get_contents($url);
        dump($s);

    }



}