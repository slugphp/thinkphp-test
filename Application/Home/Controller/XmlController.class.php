<?php
namespace Home\Controller;
use Think\Controller;
class XmlController extends Controller
{
    function __construct()
    {
        header("Content-type:text/html;Charset=utf-8");
        define('UC_KEY', 'xxx');

    }

    public function index()
    {
        $str = $this->xml_serialize(array('123', '444'));
        dump($str);
        $res = simplexml_load_string($str);
        dump($res);
        $res = (array)simplexml_load_string($str);
        dump($res);
        $res = simplexml_load_string($str);
        dump(json_decode(json_encode($res), true));
    }

    // 测试
    protected function xml_serialize($arr, $htmlon = FALSE, $isnormal = FALSE, $level = 1) {
        $s = $level == 1 ? "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n" : '';
        $space = str_repeat("\t", $level);
        foreach($arr as $k => $v) {
            if(!is_array($v)) {
                $s .= $space."<item id=\"$k\">".($htmlon ? '<![CDATA[' : '').$v.($htmlon ? ']]>' : '')."</item>\r\n";
            } else {
                $s .= $space."<item id=\"$k\">\r\n".$this->xml_serialize($v, $htmlon, $isnormal, $level + 1).$space."</item>\r\n";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s."</root>" : $s;
    }



}