<?php
namespace Home\Controller;
use Think\Controller;
class WeixinController extends Controller
{
    public function index()
    {
        echo 'test';
    }

    /**
     * 微信第三方登录头像
     */
    public function thirdLoginHeadImg()
    {
        $headImg = 'aHR0cDovL3d4LnFsb2dvLmNuL21tb3Blbi9rTXFaM012eFpJVUs5d0R4OGlhcXA2aDZpY0pHMmNtWjBMc3h3NmdFQWRrbWxpYW5IdHZMOXAxeHZXZjNmMlNaWjN1S0xhU1ZRU1hwaWJxaWFPaGdMMmlhZjJaOFBMU1FVMDZYUEovMA==';
        $headImgUrl = base64_decode($headImg);
        $ch = curl_init($headImgUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($ch);
        curl_close($ch);
        $img = './Public/thirdLoginImg.jpg';
        file_put_contents($img, $out);
        echo "<img src='/$img'/>";
    }

}