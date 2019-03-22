<?php

namespace Home\Model;
use Think\Model;

class UpyunModel extends Model
{
    protected $tableName = 'user';
    private $mUserName = 'xxx';
    private $mUserPassword = 'xxx';
    private $mUrlBase = 'http://v0.api.upyun.com';

    /**
     *
     * @param $UPYpath
     * @param $path
     * @return mixed
     */
    public function AddImage($UPYpath, $path)
    {
        $process = curl_init("{$this->mUrlBase}{$UPYpath}");
        curl_setopt($process, CURLOPT_USERPWD, "{$this->mUserName}:{$this->mUserPassword}");
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'PUT');
        $body = fopen($path, 'rb');
        fseek($body, 0, SEEK_END);
        $length = ftell($body);
        fseek($body, 0);
        //curl_setopt($process, CURLOPT_POSTFIELDS, array('file'=>$path));
        curl_setopt($process, CURLOPT_INFILE, $body);
        curl_setopt($process, CURLOPT_INFILESIZE, $length);

        $_headers = array('Expect:');
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        array_push($_headers, "Content-Length: {$length}");
        array_push($_headers, "Date: {$date}");
        curl_setopt($process, CURLOPT_HTTPHEADER, $_headers);
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 0);
        $contents = curl_exec($process);
        $info = curl_getinfo($process, CURLINFO_HTTP_CODE);
        curl_close($process);
        return $info;
    }

    //删除图片
    public function DeleteImage($UPYpath)
    {
        $process = curl_init("{$this->mUrlBase}{$UPYpath}");
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($process, CURLOPT_USERPWD, "{$this->mUserName}:{$this->mUserPassword}");
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $contents = curl_exec($process);
        print(curl_getinfo($process, CURLINFO_HTTP_CODE) . '<br/>');
        curl_close($process);
        return $contents;
    }
}

?>