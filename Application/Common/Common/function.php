<?php

/**
 * 数字转汉字描述
 *
 * 数字字符串处理
 * 1230 3008 7634 . 33  从左到有一位一位
 * 【零_八_万】分三部分组合
 * 处理特殊情况，一十开头，0000的
 */
function numToStr($num)
{
    // 判断正确数字
    if (!preg_match('/^(\d*)(\.\d+)?$/', $num)) return '不是正确的数字！';

    // 准备参数
    $num = ltrim($num, '0');
    $num_arr  = ['', '一', '两', '三', '四', '五', '六', '七', '八', '九'];
    $desc_arr = ['', '十', '百', '千', '万', '十', '百', '千', '亿', '十', '百', '千', '万亿', '十', '百', '千', '万万亿', '十', '百', '千'];

    // 按字符串处理
    $count = strlen($num);
    if ($count > count($desc_arr)) return '数字超出计算范围！';
    $point = strpos($num, '.') ?: $count;    // 没有点时取count

    for ($i = 0; $i < $count; $i++) {
        $cn_zero = $num[$i] !== '0' && $num[$i - 1] === '0' && $num[$i] !== '.' ? '零' : '';    // 是否输出零
        $cn_num  = $i > $point && !$num_arr[$num[$i]] ? '零' : $num_arr[$num[$i]];    // 输出字数
        $j = $point - $i - 1;    // 计算单位数
        $cn_desc = $num[$i] == '0' && $j % 4 != 0 || substr($num, $i - 3, 4) === '0000' ? '' : $desc_arr[$j];    // 输出单位
        if ($i == 0 && $cn_desc == '十') $cn_num = '';    // 国人习惯一十开头不读一
        if ($i === $point) $res .= '点';    // 输出点
        $res .=  $cn_zero . $cn_num . $cn_desc;
    }
    return $res;
}

/**
 * 字符串++
 *
 * 如：abc++ = abd
 */
function strPlusPlus($string) {
    $n = strlen($string);
    if ($n < 0) {
        return false;
    }
    $arr = ['0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, 'a' => 10, 'b' => 11, 'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17, 'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23, 'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29, 'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35];

    // 从最后一个字母对应的数字算起
    $new_str = '';
    for ($i = $n - 1; $i >= 0; $i--) {
        $num_tmp = $arr[$string[$i]] + 1;
        $new_num = $num_tmp % 36;
        $new_str .= array_search($new_num, $arr);
        if ($num_tmp < 36) break;
    }

    // 'zzz'情况
    if ($i < 0) {
        return '0' . strrev($new_str);
    } else {
        return substr($string, 0, $i) . strrev($new_str);
    }
}

/**
 * php 运行时间调试
 * @param  分组标记 $mark
 * @param  换行符 $p
 * @return 标记时间数组
 */
function timeDebug($mark, $echo = true, $p = '<br>')
{
    global $timeDebug;
    $mt = microtime();
    if (!$mark) $mark = 'timeDebug';
    $timeDebug[$mark][] = (float) (substr((string) $mt, 17) . substr((string) $mt, 1, 6));
    $arr = $timeDebug[$mark];
    $endKey = count($arr) - 1;
    if (array_key_exists($endKey - 1, $arr)) {
        $timeDiff = sprintf('%.5f', $arr[$endKey] - $arr[$endKey - 1]);
        $p = '    >> ' . $timeDiff . 's' . $p;
        $markDiff = $endKey . '_' . $endKey - 1;
        $markDiffKey = $mark . '_diff';
        $timeDebug[$markDiffKey][$markDiff] = $timeDiff;
    }
    if ($echo) {
       echo $mark, '  ', date('Y-m-d H:i:s') . substr((string) microtime(), 1, 6), $p;
    }
    return $timeDebug;
}

/**
 * 计算一个数字内包含几个数字
 * @param  num $num
 * @return num 0-9个
 *  如：countNum(13466357088) == 8 && countNum(13466357088, 8) == 2
 */
function countNum($num, $n) {
    $num = (string) $num;
    $res = [];
    $count = strlen($num);
    for ($i = 0; $i < $count; $i++) {
        if (isset($n) && $n >= 0 && $n <= 9) {
            // 若存在，数有几个$n
            if ($num[$i] == $n) {
                $res[] = $num[$i];
            }
        } else {
            if (!in_array($num[$i], $res)) {
                $res[] = $num[$i];
            }
        }
    }
    return count($res);
}

/**
 * post数据方法
 * @param  array $data 数组
 * @return boolean
 */
function postData($url, $data) {
    return file_get_contents($url, false, stream_context_create(array('http' => array('method' => 'POST', 'content' => http_build_query($data)))));
}

/**
 * 递归的生成目录
 * @param  str $dir 必须是目录
 */
function mkdirs($dir)
{
    return is_dir($dir) ?: mkdirs(dirname($dir)) && mkdir($dir);
    // if (is_dir($dir)) return true;
    // mkdirs(dirname($dir));
    // if (mkdir($dir)) return true;
}

/**
 * 写入文件日志方法
 * @param  错误相关信息。此方法会将此数组json串化(中文仍可显示)做一行写入文件
 */
function writeFileLog() {
    // 处理动态参数
    $param = func_get_args();
    $file = $param[0];
    $arr['time'] = date('Y-m-d H:i:s');    // 增加时间参数
    array_shift($param);
    $arr['data'] = $param;
    if (count($arr_tmp) == 1) {
        $arr['data'] = $arr_tmp;
    }
    // json串化，并处理汉字显示
    $str = json_encode($arr);
    $res = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
    // 打开（创建）文件，写入并关闭
    $file_info = pathinfo($file);
    mkdirs($file_info['dirname']);
    $fp = fopen($file, "a+");
    fwrite($fp, $res . "\r\n");
    fclose($fp);
}

function randFileLine($fileName, $maxLineLength = 4096) {
    $handle = @fopen($fileName, "r");
    if ($handle) {
        $random_line = null;
        $line = null;
        $count = 0;
        while (($line = fgets($handle, $maxLineLength)) !== false) {
            $count++;
            // P(1/$count) probability of picking current line as random line
            if(rand() % $count == 0) {
                $random_line = $line;
            }
        }
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
            fclose($handle);
            return null;
        } else {
            fclose($handle);
        }
        return $random_line;
    }
    return false;
}

/**
 * 格式化时间为几分钟前
 * @param  number $time 时间戳
 * @return string
 */
function time_tran($time) {
    $now_time = time();
    $dur = $now_time - $time;
    $the_time = date("Y-m-d H:i:s", $time);
    if ($dur < 0) {
        return $the_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) {  //3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return $the_time;
                    }
                }
            }
        }
    }
}

/**
 * 发送邮件到139邮箱，会短信通知
 * @param  string $title
 * @param  string $content
 * @param  string $file
 * @return boolean
 */
function mailTo139($title = '', $content = '', $file = '')
{
    vendor('PHPMailer.class#phpmailer');
    vendor('PHPMailer.class#smtp');

    $mail  = new \PHPMailer();
    $mail->CharSet       = "UTF-8";    //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置为 UTF-8
    $mail->IsSMTP();    // 设定使用SMTP服务
    $mail->SMTPAuth      = true;    // 启用 SMTP 验证功能
    // $mail->SMTPSecure = "ssl";    // SMTP 安全协议
    $mail->Host          = "smtp.163.com";    // SMTP 服务器
    $mail->Port          = 25;    // SMTP服务器的端口号
    $mail->Username      = C('MAIL_USERNAME');    // SMTP服务器用户名
    $mail->Password      = C('MAIL_PASSWORD');    // SMTP服务器密码
    $mail->SetFrom('wilonx@163.com', '自动发送');    // 设置发件人地址和名称
    $mail->Subject       = $title;    // 设置邮件标题
    $mail->AltBody       = $content;    // 可选项，向下兼容考虑
    $mail->MsgHTML($content);    // 设置邮件内容
    $mail->AddAddress('13466357088@139.com', '手机提醒');
    if ($file) {    // 附件
        $mail->AddAttachment($file);
    }
    return $mail->Send();
}
?>