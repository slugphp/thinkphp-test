<?php
namespace Home\Controller;
use Think\Controller;
class StrController extends Controller
{
    function __construct()
    {
        header("Content-type:text/html;Charset=utf-8");
        define('UC_KEY', 'xxx');

    }

    // test
    public function index()
    {
        echo count('abc');
    }

    /**
     * 中文处理
     */
    public function chinese()
    {
        // 截取中文无乱码
        $str = '截取a中文无乱码';
        $res = mb_substr($str, 0, 4, 'utf-8');
        ob_start();$s=array($res);foreach($s as $v){var_dump($v);}die('<pre>'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
        for ($i=0; $i < 100; $i++) {
            echo mt_rand(1, 26), '<br>';
        }
    }

    public function getFirstCharter($str)
    {
        if (empty($str))  return false;
        $fchar = ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=iconv('UTF-8','gb2312',$str);
        $s2=iconv('gb2312','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return null;
    }

    // URL、路径处理
    public function pathAndUrl()
    {
        // url处理用此方法
        $parse = parse_url('http://127.0.0.1/test/tp/index.php/home/str');
        // 路径处理用此方法
        $dir   = dirname($_SERVER['SCRIPT_FILENAME']);
        $dirs  = dirname('Public/Uploads/photo_lib/tmp/');
        $path  = pathinfo('C:/xampp/htdocs/test/tp/');
        $url1 = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        $url = pathinfo($url1);
        ob_start();$s=array($url1, $url);foreach($s as $v){var_dump($v);}die('<pre>'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
    }

    // 十进制转26进制a-b(or 52进制a-zA-Z)
    protected function tenToTwentysix($num)
    {
        $num = intval($num);
        $str = 'abcdefghigklmnopqrstuvwxyz';  // 0-25
        // $str = '0123456789abcdefghigklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ';  // 62进制
        $cot = strlen($str);
        $res = '';
        do {
            $num_tmp = $num % $cot;
            $num     = intval($num / $cot);
            $res     = $str[$num_tmp] . $res;
        } while ($num);
        return  $res;
    }

    // ob_start测试
    public function ob()
    {
        // test1
        ob_start();
        var_dump('expression');
        $d = ob_get_contents(); // 获得输出值且上边代码输出
        echo '---', $d, '<br>';
        // test2
        ob_start();
        var_dump('expression');
        $s = ob_get_clean();    // 获得输出值上边代码不输出（已清除）
        echo '---', $s, '<br>';
    }

    // 加密解密
    public function encode()
    {

        echo $this->authcode('admin', 'ENCODE', UC_KEY, 1000), '<br>';

    }
    public function decode()
    {
        echo $this->authcode('b2edIy3RYkUQvsIpIiDaGTGPaBobvKh0wCMLft5a1blnyA'), '<br>';

    }

    // 自己写一个
    public function myAuthCode($str, $operation = 'DECODE')
    {
        $key = md5(UC_KEY);

    }

    /**
     * Discuz!经典加密解密代码
     * @param  string  $string    字串
     * @param  string  $operation 加密(ENCODE)or解密(DECODE)
     * @param  string  $key       密匙
     * @param  integer $expiry    [description]
     * @return string             返回字串
     */
    private function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;

        // 密匙
        $key = md5($key ? $key : UC_KEY);
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
        // 解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;

        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        ob_start();$s=array($key_length, $cryptkey, $rndkey);foreach($s as $v){var_dump($v);}die('<pre>'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * html字符串处理
     *|————————————————————————————————————|
     *|字符 |  描述   | html实体 |         |
     *|     |  空格   |  &nbsp;  |         |
     *|  <  |  小于号 |  &lt;    | special |
     *|  >  |  大于号 |  &gt;    | special |
     *|  &  |  和号   |  &amp;   | special |
     *|  "  |  引号   |  &quot;  | special |
     *|  '  |  撇号   |  &apos;  | special |
     *|  ￠ |  分     |  &cent;  |         |
     *|  £  |  镑     |  &pound; |         |
     *|  ¥  |  日圆   |  &yen;   |         |
     *|  €  |  欧元   |  &euro;  |         |
     *|  §  |  小节   |  &sect;  |         |
     *|  ©  |  版权   |  &copy;  |         |
     *|  ®  |  商标   |  &reg;   |         |
     *|  ™  |  商标   |  &trade; |         |
     *|  ×  |  乘号   |  &times; |         |
     *|  ÷  |  除号   |  &divide;|         |
     *|————————————————————————————————————|
     */
    public function html()
    {
        $str = '&ldquo;壁纸&rdquo;&mdash;&mdash;国外<div>天下&lt;dd&gt;&nbsp;安静哦&mdash;&mdash;';
        $str1 = html_entity_decode($str);       // html实体 --> 字符
        $str2 = htmlentities($str);             // 字符 --> html实体
        $str3 = htmlspecialchars_decode($str);  // special html实体 --> 字符
        $str4 = htmlspecialchars($str);         // special 字符 --> html实体
        print_r(array($str, $str1, $str2, $str3, $str4, $str5));
    }

    public function serialize()
    {
        $str = 'a:6:{s:8:"required";b:0;s:8:"listable";b:0;s:6:"prefix";s:1:"1";s:5:"types";a:7:{i:1;s:9:"跟进中";i:2;s:9:"已解决";i:3;s:15:"一起网商家";i:4;s:18:"非一起网商家";i:5;s:18:"非一起网订单";i:12;s:6:"公告";i:14;s:6:"表扬";}s:5:"icons";a:7:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:12;s:0:"";i:14;s:0:"";}s:10:"moderators";a:7:{i:1;s:1:"1";i:2;s:1:"1";i:3;N;i:4;N;i:5;N;i:12;s:1:"1";i:14;N;}}';
        $res = unserialize($str);
        ob_start();$s=array($res);foreach($s as $v){var_dump($v);}die('<pre style="white-space:pre-wrap;word-wrap:break-word;">'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'');
    }
}