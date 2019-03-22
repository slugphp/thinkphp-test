<?php
namespace Home\Controller;
use Think\Controller;
class FileController extends Controller
{
    public function index()
    {
        $fileModel = new \Home\Model\FileModel();
        $res = $fileModel ->copyDir('./ThinkPHP', './Public');
    }

    /**
     * 获取文件类型
     *
     * substr 查找字符串在另一个字符串中最后一次出现的位置,并返回从该位置到字符串结尾的所有字符
     */
    public function getFileExt()
    {
        $fileName = '/Home/Controller/FileController.class.php';
        // 麻烦的
        $return[] = ltrim(substr($fileName, strrpos($fileName, '.')), '.');
        $return[] = strrev(substr(strrev($fileName), 0, strpos(strrev($fileName), '.')));
        // 比较好的
        $return[] = ltrim(strrchr($fileName, '.'), '.');
        $return[] = array_pop(explode('.', $fileName));
        $return[] = pathinfo($fileName, PATHINFO_EXTENSION);

        ob_start();$s=array($return, strrpos($fileName, '.'));foreach($s as $v){var_dump($v);}die('<pre>'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
    }


}