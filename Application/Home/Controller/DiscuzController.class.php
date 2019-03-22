<?php
namespace Home\Controller;
use Think\Controller;
class DiscuzController extends Controller
{
    public function index()
    {
        echo "string";
    }

    /**
     * discuz强制找回用户密码
     * 表：ucenter_members，更新结果到password字段
     */
    public function ucMemberPassword()
    {
        $password = $_GET['password'] ?: '123456';    // 新密码
        $salt = $_GET['salt'] ?: '288357';    // 表：ucenter_members，salt字段
        echo md5(md5($password) . $salt);
    }

}