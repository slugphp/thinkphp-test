<?php

/**
 * 微信采集数据后台
 * @author weilong qq:973885303
 */

namespace Home\Controller;
use Think\Controller;
class AdminController extends BaseController
{
    /**
     * 自动加载配置
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 后台首页，即模板
     */
    public function index()
    {
        $this->assign('cookie', $this->setCookie);
        $this->display();
    }

}