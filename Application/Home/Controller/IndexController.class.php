<?php

/**
 * 微信采集数据方法
 * @author weilong qq:973885303
 */

namespace Home\Controller;

use Think\Controller;

class IndexController extends BaseController
{
    protected $keyword;
    protected $keywordArr = array('装修', '家装', '装饰', '装潢', '设计', '房产');
    protected $baseUrl = 'http://weixin.sogou.com';
    protected $pushUrl = 'http://baidu.com';
    protected $userConfig = array();
    protected $aritcleConfig = array();

    /**
     * 自动加载配置
     */
    public function __construct()
    {
        parent::__construct();

        $this->aritcleConfig['url'] = $this->baseUrl . '/gzhjs?cb=sogou.weixin.gzhcb&';
        $this->aritcleConfig['param'] = array(
            'openid' => '',
            'ext' => '',
            'gzhArtKeyWord' => '',
            'page' => '1',
            't' => time() . '372',
        );
        $this->userConfig['url'] = $this->baseUrl . '/weixin?';
        $this->userConfig['param'] = array(
            'fr' => 'sgsearch',
            'ie' => 'utf8',
            'type' => '1',
        );
    }

    /**
     * 采集控制
     */
    public function index()
    {
        die('123');
        // 标记，轮流采集用户、文章
        $mark = F('mark') ?: 1;

        // 采集用户
        if ($mark == 1) {
            $this->doCollectUser();
            F('mark', 2);
        }

        // 采集文章
        if ($mark == 2) {
            $this->doCollectArticle();
            F('mark', 1);
        }

        // 采集被限制，需等待X小时自动解除（搜狗限制的几小时未知，6小时可以了）
        if (is_array($mark) && $mark[0] == 3) {
            if (time() - $mark[1] > 6*3600) {
                F('mark', 1);
            }
        }

    }

    /**
     * 推送控制
     */
    public function push()
    {
        set_time_limit(0);
        $this->pushArticle();
    }

    /**
     * test
     */
    public function test()
    {
        $content1 = '<div><img data-src="http://mmbiz.qpic.cn/mmbiz/zWIozXic63Ut8DslvcnAQMtYtsOCyGQWtTvn0xJuIfibOYkhe2pibibyjUjRWNgXhfYFGoOWNPMIyOW0tyL2TgTXdA/0?wx_fmt=png" data-type="png" data-s="300,640" data-ratio="1.246875" data-w="320" src="http://static-news.xxxx.com/web/wechat/201512/01/14489526111753913783.png"  /> <img data-src="http://mmbiz.qpic.cn/mmbiz/zWIozXic63Ut8DslvcnAQMtYtsOCyGQWtIyqbzIFlHpOMAiccDEqDNX4cibWQSvgI7zf8RkmPtqAXkbk1ibryc6OFA/0?wx_fmt=png" data-type="png" data-s="300,640" data-ratio="0.6654545454545454" data-w="550" src="http://static-news.xxxx.com/web/wechat/201512/01/1448952611788598729.png"  /> <p><img data-src="http://mmbiz.qpic.cn/mmbiz/zWIozXic63Ut8DslvcnAQMtYtsOCyGQWtcGIpibzN89zXLExtlKoGb6rLGTkwLpcyJoA3AeLFC1QsyfLMpIUyO5A/0?wx_fmt=png" data-type="png" data-s="300,640" data-ratio="0.6345454545454545" data-w="550" src="http://static-news.xxxx.com/web/wechat/201512/01/14489526121655174589.png"  /></p></div>';
        $collect = new \Home\Model\CollectModel();
        $content2 = $collect->parseContent($content1);
        ob_start();$s=array($content1, $content2);foreach($s as $v){var_dump($v);}die('<pre style="word-wrap:break-word;">'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
    }

    /**
     * 执行采集用户信息
     */
    protected function doCollectUser()
    {
        // 一次只采集一个关键词，过多可能超时
        list($key, $page) = F('userMark') ?: array(0, 1);
        if (!array_key_exists($key, $this->keywordArr)) {
            $key = 0;
        }
        $this->keyword = $this->keywordArr[$key];
        showMsg(100, 'collect user keyword', array($this->keyword, $page));
        $this->userConfig['param']['page'] = $page % 10;
        $this->userConfig['param']['query'] = $this->keyword;
        $collect = new \Home\Model\CollectModel();
        $res = $collect->collectUser($this->userConfig);
        if ($res) {
            $page++;
            if ($page > 10) {
                $key++;
                $page = 1;
            }
            F('userMark', array($key, $page));
            $msg = 'success ' . count($res) . ' user';
            showMsg(200, $msg, $res);
        }
    }

    /**
     * 执行采集文章信息
     * @desc 用户status=2是需要采集
     */
    protected function doCollectArticle($num = 0)
    {
        // 找出有更新的用户
        $user = M('user');
        $where['status'] = 2;
        $where['last_modify_time'] = array('gt', strtotime(date('Y-m-d'))); // 不能过期,链接当天才有效
        $userInfo = $user->where($where)->order('uid desc')->find();
        if ($userInfo) {
            // 添加过滤
            $filter_str = $userInfo['name'] . $userInfo['company'];
            $newusername = trim($userInfo['wechatid']) . '_wx';
            if (strlen($newusername) > 15) {
                $newusername = substr($newusername, 0, 12) . '_wx';
            }
            $filter = false;
            foreach (C('USER_FILTER_WORD') as $k => $v) {
                if (strpos($filter_str, $v) !== false) {
                   $filter = true;
                }
            }
            foreach (C('USER_FILTER_NAME') as $k => $v) {
                if ($newusername == $v) {
                    $filter = true;
                }
            }
            if ($filter) {
                $user->where(array('uid' => $userInfo['uid']))->save(array('status' => 3));
                showMsg(209, 'has been filter', $userInfo);
                $this->doCollectArticle($num);
                return false;
            }
            // 采集新闻
            $collect = new \Home\Model\CollectModel();
            $this->aritcleConfig['uid'] = $userInfo['uid'];
            $res = $collect->collectArticle($this->aritcleConfig);
            // 更新状态
            if ($res !== false) {
                $user->where(array('uid' => $userInfo['uid']))->save(array('status' => 1));
                // 成功
                showMsg(201, 'collect ' . count($res) . ' article success', $res);
                $num += count($res);
            } else {
                showMsg(202, 'error');
            }
            // 多踩几条
            // if ($num < 10) {
            //     $this->doCollectArticle($num);
            // } else {
            //     return false;
            // }
        } else {
            showMsg(203, 'no user to collect');
            return false;
        }
    }

    /**
     * 执行推送文章，一次推10篇
     * 有文章的才推送用户
     *
     * 文章： push_status 推送状态，默认1未推送，2已推送
     * 用户：post_status 推送状态，默认1需要发送，2已发送
     */
    protected function pushArticle()
    {
        // 读取10篇未推送的数据
        $article = M('article');
        $user = M('user');
        $where['push_status'] = 1;
        $articleList = $article->where($where)->limit(10)->select();
        // 分别推送
        $userInfo = array();
        $count = 0;
        foreach ($articleList as $value) {
            $uid = $value['uid'];
            // 采集的内容空
            if (!$value['content']) {
                showMsg(204, 'hasnot content', $value['aid']);
                continue;
            }
            // 转换内容，保存
            $collect = new \Home\Model\CollectModel();
            $content = $collect->parseContent($value['content']);
            if (!$value['content'] || !$content) {
                showMsg(205, 'wrong parse content', $value['aid']);
                continue;
            }
            // 查找用户信息，一起推送
            if (!$userInfo[$uid]) {
                $userWhere['uid'] = $uid;
                $userInfo[$uid] = $user->where($userWhere)->find();
                // 没有此用户
                if (!$userInfo[$uid]['wechatid']) {
                    showMsg(209, 'article donot have user', $userInfo[$uid]);
                    continue;
                }
                // 是否推送过
                if ($userInfo[$uid]['post_status'] != 2) {
                    $user->where($userWhere)->save(array('post_status' => 2));
                    $postData['user'] = json_encode($userInfo[$uid]);
                }
                unset($userWhere);
            }
            // 推送内容
            $postData['mod'] = 'blog';
            $postData['name'] = $userInfo[$uid]['name'];
            $postData['wechatid'] = $userInfo[$uid]['wechatid'];
            $postData['title'] = $value['title'];
            $postData['summary'] = $value['summary'];
            $postData['content'] = $content;
            $postData['time'] = $value['time'];
            $return = postData($this->pushUrl, $postData);
            if ($return !== false) {
                $res = json_decode($return, true);
                $push_status = $res[0] == 200 ? 2 : 3;
                $article->where(array('aid' => $value['aid']))->save(array('push_status' => $push_status, 'content' => $content));
                showMsg(207, 'one post done', $res);
            } else {
                $article->where(array('aid' => $value['aid']))->save(array('content' => $content));
                showMsg(206, 'request error', $postData);
            }
            unset($postData);
            $count++;
        }
        // 多推送几个用户
        $userMore = $user->where(array('post_status' => 1))->select(10);
        if (!$userMore) {
            showMsg(210, 'user post done', $userMore);
            continue;
        }
        foreach ($userMore as $k => $v) {
            $postData['wechatid'] = $v['wechatid'];
            $postData['user'] = json_encode($v);
            $return = postData($this->pushUrl, $postData);
            if ($return !== false) {
                $res = json_decode($return, true);
                $post_status = $res[0] == 509 ? 2 : 3;
                $user->where(array('uid' => $v['uid']))->save(array('post_status' => $post_status));
                showMsg(211, 'one user post done', $res);
            } else {
                $article->where(array('aid' => $value['aid']))->save(array('content' => $content));
                showMsg(206, 'request error', $postData);
            }
            unset($postData);
        }
        showMsg(208, 'post done ' . $count);
        return $count;
    }
}
