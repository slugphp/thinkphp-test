<?php

/**
 * 文件方法
 * @author weilong qq:973885303
 */

namespace Home\Model;

use Think\Model;

class FileModel extends Model
{
    protected $tableName = 'user';

    /**
     * 自动加载配置
     */
    public function __construct()
    {
    }

    /**
     * 复制整个目录的函数
     * @param  string $sourceDir
     * @param  string $targetDir
     * @return
     */
    public function copyDir($sourceDir, $targetDir)
    {
        // 校验源目录
        if(!file_exists($sourceDir) || !is_dir($sourceDir)){
            return false;
        }
        if(!file_exists($targetDir) || !is_dir($targetDir)){
            @mkdir($targetDir) or return false;
        }
        // 打开目录
        $dd = opendir($sourceDir);
        // 开始遍历
        while($f = readdir($dd)) {
            if ($f == '.' || $f == '..') {
                continue;
            }
            // 为源文件和目标文件加上路径
            $file1 = trim($sourceDir, '/') . '/' . $f;
            $file2 = trim($targetDir, '/') . '/' . $f;
            // 判断若是文件则复制
            if (is_file($file1)) {
                copy($file1, $file2);
            }
            // 判断若是目录则执行递归复制
            if (is_dir($file1)) {
                $this->copyDir($file1, $file2); // 递归调用
            }
        }
        // 关闭目录
        closedir($dd);
        return true;
    }

}
