<?php
namespace Home\Controller;
use Think\Controller;
class PregController extends Controller
{
    function __construct()
    {
        header("Content-type:text/html;Charset=utf-8");
    }

    public function index()
    {
        $res = preg_match('/^1\d{8}$/', '157141087');
        ob_start();$s=array(time(), $res, $matchs);foreach($s as $v){var_dump($v);}die('<pre style="white-space:pre-wrap;word-wrap:break-word;">'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'');
    }

    /**
     * 注意 \\1
     *
     */
    public function index1()
    {
        // 原字串
        $str = '13141078884 83161143334 13263362224 18511925554 18612623450 13141102288 13161146662 13263370884 18513608884 18612625555 13141108580 13161146667 13263389994 13264459992 18600004038 18612932626';

        // 后四位匹配ABAB
        preg_match_all('/\d{7}(?!(\d)\\1)(\d\d)\\2/', $str, $res);
        dump($res);

        // 匹配手机号至少有一个8
        preg_match_all('/(?![^8]{11})\d{11}/', $str, $res1);
        preg_match_all('/(?=\d*8)\d{11}/', $str, $res2);

        ob_start();$s=array($res1, $res2);foreach($s as $v){var_dump($v);}die('<pre>'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
    }

    /**
     * [^aa] 排除
     *
     */
    public function index2()
    {
        define(BASEURL, 'http://120.25.216.189/99cms/');

        $str = '<p>岸炮<img src="Public/Uploads/yidong/images/1506/19/1643056913482701.jpg">反馈<img src=Public/Uploads/yidong/images/1506/19/1642215970297270.jpg></p>';
        dump($str);
        $res = preg_replace('/<img\s+src.*=.*([^\"].+\.(jpg|gif|bmp|bnp|png)).*>/iU', '<img src="' . (BASEURL) . '\\1" width="98%" />', $str);
        die(dump($res));
    }

    /**
     * ?会恢复贪婪
     */
    public function tanlan()
    {
        echo $str = '000abcd333abcd444abscd888' , '<br>';
        $res1 = preg_replace('/ab(.*)d/iU', 'xxx', $str);
        $res2 = preg_replace('/ab(.*?)d/iU', 'xxx', $str);  // ()里有？恢复U贪婪
        preg_match_all('/ab.*?d/i', $str, $res3);
        preg_match_all('/ab(.*?)d/iU', $str, $res4);    // ()里有？恢复U贪婪

        ob_start();$s=array($res1, $res2, $res3, $res4);foreach($s as $v){var_dump($v);}die('<pre>'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
    }

    /**
     * 原子存储 \\1
     */
    public function yuan()
    {

        echo $str = 'aag[size=5]sss[/size]adafa:)[font=新宋体][size=5][b]11111[/b][/size][/font]222222[color=#ff8c00]开[/color]:([color=#ff8c00]3333333[/color]44444444';
        preg_match_all("/\[(\w+).*?\](.*?)\[\/\\1.*?\]/i", $str, $res);
        dump($res);
        echo '<br>', $res = preg_replace("/\[(\w+).*?\](.*?)\[\/\\1.*?\]/i", '\2', $str);
        echo '<br>', $res = preg_replace("/\[(\w+).*?\](.*?)\[\/\\1.*?\]/i", '\2', $res);
        echo '<br>', $res = preg_replace("/\[(\w+).*?\](.*?)\[\/\\1.*?\]/i", '\2', $res);
    }

    // 替换小写字母为大写字母，callback+匿名函数
    public function aa()
    {
        $str = '1aj2rt34bvc5';
        echo $res = preg_replace_callback(
            "/([a-z])/i",
            function ($matchs) {
                return chr(ord($matchs[0]) - 32);
            },
            $str);
    }

   /**
    * 正则匹配ABAB数字但匹配AAAA
    */
    public function bb()
    {
        $str = "23525217676244888895";
        // preg_match_all("/(?!(\d)\\1)(\d\d)\\2/", $str, $res);
        preg_match_all("/(?!(\d))\\1/", $str, $res);
        dump($res);
    }

    /**
     * 中英文正则匹配
     */
    public function zhongwen()
    {
        $s = 'ppttfg35a zv字符';
        $res['全是中文？：'] = preg_match("/[\x7f-\xff]/", $s) ? '是' : '否';
        $res['含有中文？：'] = preg_match("/[\x7f-\xff]/", $s) ? '是' : '否';
        die(dump($res));
    }

    /**
     * 中英文正则匹配
     */
    public function hidemobile()
    {
        $content = 'mobile13166778899';
        $content = '13166778899mobile';
        $content = 'thanks13166778899mobile';
        $content = 'orderId1131667788990';
        $content = 'img:8721316677889933.jpg';
        $content = preg_replace('/^(1\d{2})(\d{4})(\d{4})$/', "$1****$3", $content);
        $content = preg_replace('/([^\d]1\d{2})(\d{4})(\d{4})$/', "$1****$3", $content);
        $content = preg_replace('/^(1\d{2})(\d{4})(\d{4}[^\d])/', "$1****$3", $content);
        $content = preg_replace('/([^\d]1\d{2})(\d{4})(\d{4}[^\d])/', "$1****$3", $content);
        ob_start();$s=array($content);foreach($s as $v){var_dump($v);}die('<pre style="white-space:pre-wrap;word-wrap:break-word;">'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'');
    }
}