<?php
namespace Home\Controller;
use Think\Controller;
class AlgorithmController extends Controller
{
    function __construct()
    {
        header("Content-type:text/html;Charset=utf-8");
        define('UC_KEY', 'xxx');

    }

    public function maopao()
    {
        $arr = array(4, 8, 22, 9, 14, 33, 28, 9, 10);
        $num = count($arr);
        $n   = 0;
        echo $n, '开始。。', json_encode($arr), '<br>';
        for ($i = 1; $i < $num; $i++) {
            for ($j = $i; $j > 0; $j--) {
                if ($arr[$j] > $arr[$j - 1]) {
                    $tmp = $arr[$j];
                    $arr[$j] = $arr[$j - 1];
                    $arr[$j - 1] = $tmp;
                    echo ++$n, ':', json_encode($arr), '<br>';
                    unset($tmp);
                }
            }
        }
    }

    public function sanlie()
    {
        $arr = array(4, 8, 22, 9, 14, 33, 28, 9);
        echo '开始。。', json_encode($arr), '<br>';
        for ($i = 1; $i < 1000; $i++) {
            if ($arr[$i] > $arr[$j - 1]) {
                $tmp = $arr[$i];
                $arr[$i] = $arr[$j];
                $arr[$j] = $tmp;
                echo json_encode($arr), '<br>';
                unset($tmp);
            }
        }
    }

    public function threeMin($a, $b, $c)
    {
        return ($d = $a < $b) ? $a : $b < $c ? $d : $c;
    }

}