<?php

/**
 * 微信采集数据方法
 * @author weilong qq:973885303
 */

namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller
{
    protected $setCookie = '';

    /**
     * 自动加载配置
     */
    public function __construct()
    {
        parent::__construct();
        // 设置cookie
        // $this->setCookie = F('setCookie');
        // if (!$this->setCookie) {
        //     $this->setCookie = M('config')->where(array('id' => 1))->getField('set_cookie');
        //     F('setCookie', $this->setCookie);
        // }
    }

    protected function showMsg($file, $code, $msg, $data)
    {
        // 写日志，只保留最近5天
        $path = './Public/log/';
        $log_name = $file . '_' . date('Y-m-d') . '.log';
        $old_log_name = $file . '_' . date('Y-m-d', time() - 432000) . '.log';
        if (file_exists($path . $old_log_name)) {
            @unlink($path . $old_log_name);
        }
        writeFileLog($path . $log_name, $code, $msg, $data);
        echo $msg . '<br>';
    }

    protected function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key != '' ? $key : $key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }

    }

}