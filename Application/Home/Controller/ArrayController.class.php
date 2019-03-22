<?php
namespace Home\Controller;
use Think\Controller;
class ArrayController extends Controller
{
    function __construct()
    {
        header("Content-type:text/html;Charset=utf-8");

    }

    public function index()
    {
        $fruits = array('sweet' => 'sugar', 'sour' => 'lemon', 'myfruits' => array('a' => 'apple', 'b' => 'banana'));
        $keys = $this->array_all_keys($fruits);
        $s=array($fruits, $keys);foreach($s as $v){var_dump($v);}die;
    }

    // 返回多维数组所有键值
    protected function array_all_keys($array)
    {
        foreach ($array as $k => $v) {
            $keys[] = $k;
            if (is_array($v)) $keys = array_merge($keys, $this->array_all_keys($v));
        }
        return $keys;
    }

}

