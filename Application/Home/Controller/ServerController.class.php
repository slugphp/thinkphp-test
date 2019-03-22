<?php
namespace Home\Controller;
use Think\Controller;
class ServerController extends Controller
{
    public function index()
    {

    }

    public function ini()
    {
        /**
         * safe_mode
         * exec()：禁止执行
         * 文件操作函数：严格按权限
         * set_time_limit()：在安全模式下不起作用
         */

    }

    /**
     * $_SERVER
     */
    public function ip()
    {
        $clientIp = $_SERVER['HTTP_CLIENT_IP'] ?: $_SERVER['HTTP_X_FORWARDED_FOR'];
        $clientIp = $clientIp ?: $_SERVER['REMOTE_ADDR'];
        $serverIp = $_SERVER['SERVER_ADDR'];
        ob_start();$s=array($clientIp, $serverIp);foreach($s as $v){var_dump($v);}die('<pre>'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
    }

}