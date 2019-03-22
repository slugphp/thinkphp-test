<?php

/**
 * 微信采集数据方法
 * @author weilong qq:973885303
 */

namespace Home\Model;

use Think\Model;

class CollectModel extends Model
{
    protected $tableName = 'user';
    // 采集配置
    protected $baseUrl = 'http://weixin.sogou.com';
    protected $userConfig = array();
    protected $aritcleConfig = array();
    // 又拍云配置
    protected $upYunSavePath = 'xxx';
    protected $upYunShowPath = 'xxx';
    // 保存cookie
    protected $setCookie;

    /**
     * 自动加载配置
     */
    public function __construct()
    {
        parent::__construct();

        // 初始化cookie
        $this->curlRequest('http://pb.sogou.com/pv.gif');

    }

    /**
     * 采集公众号信息
     */
    public function collectUser($userConfig)
    {
        $this->userConfig = $userConfig;
        // 获取内容
        $content = $this->loadContent('user');
        if (!$content) {
            showMsg(401, 'cant connect', $content);
            return false;
        }

        // html处理
        import('Vendor.simpleHtmlDom.simple_html_dom');
        $html = new \simple_html_dom();
        $html->load($content);

        // 是否访问频繁
        $errorTip = $html->find('.p2', 0)->plaintext;
        if (strpos($errorTip, '您的访问过于频繁')) {
            echo $content;
            showMsg(402, 'request frequently', $content);
            return false;
        }

        // 此页所有公众号信息
        $div = $html->find('.wx-rb_v1');
        if (!$div) {
            showMsg(403, 'cant parse html', $content);
            return false;
        }
        $user = M('user');
        foreach ($div as $v) {
            // 获取一个公众号信息
            $userData = array(
                'name' => $v->find('h3', 0)->plaintext,
                'wechatid' => ltrim($v->find('h4 span', 0)->plaintext, '微信号：'),
                'url' => $v->href,
                'avatar' => $v->find('img', 0)->src ?: '',
                'desc' => $v->find('.sp-txt', 0)->plaintext ?: '',
                'company' => $v->find('.sp-txt', 1)->plaintext ?: '',
                'last_article' => $v->find('.sp-txt a', 0)->plaintext ?: '',
                'keyword' => $this->userConfig['param']['query'],
            );
            if ($userData['last_article'] == $userData['company']) {
                $userData['company'] = '';
            }
            // 查询此号存在？不存在，则添加
            $userInfo = $user->where(array('wechatid' => $userData['wechatid']))->find();
            $uid = $userInfo['uid'];
            if (!$userInfo) {
                $userData['status'] = 2;
                $userData['last_modify_time'] = time();
                $uid = $user->add($userData);
                $res[] = $uid;

            // 存在，判断所有字段，更新
            } else {
                // 判断是否有新文章
                $userNewData = array();
                if ($userData['last_article'] != $userInfo['last_article']) {
                    $userNewData['url'] = $userData['url']; // 要更新url
                    $userNewData['last_article'] = $userData['last_article'];
                    $userNewData['status'] = 2;
                    $userNewData['last_modify_time'] = time();
                    $user->where(array('uid' => $uid))->save($userNewData);
                    $res[] = $uid . '_have_new_article';
                }
                // 判断是否有信息更改
                if ($userData['avatar'] != $userInfo['avatar'] || $userData['desc'] != $userInfo['desc'] || $userData['company'] != $userInfo['company']) {
                    $userData['post_status'] = 1;
                    $user->where(array('uid' => $uid))->save($userData);
                    $res[] = $uid . '_update';
                    // PushController::pushUser($userData);
                }
            }
            // 避免影响
            unset($userData, $uid);
        }
        return $res;
    }

    /**
     * 采集最近的文章列表
     */
    public function collectArticle($aritcleConfig)
    {
        $this->aritcleConfig = $aritcleConfig;
        $uid = $this->aritcleConfig['uid'];
        // 判断参数
        $userWhere['uid'] = $uid;
        $userInfo = M('user')->where($userWhere)->find();
        if (!$userInfo) {
            showMsg(501, 'donot have the user', $content);
            return false;
        }

        // 获取内容
        $isMatched = preg_match_all('/openid\=(.*?)\&amp\;ext\=(.*?)$/i', $userInfo['url'], $matches);
        if (!$isMatched) {
            showMsg(502, 'wrong userinfo', $userInfo);
            return false;
        }
        $this->aritcleConfig['param']['openid'] = $matches[1][0];
        $this->aritcleConfig['param']['ext'] = $matches[2][0];
        $content = $this->loadContent('articlelist');
        if (!$content) {
            showMsg(503, 'cant connect', $content);
            return false;
        }

        // 解析json
        preg_match('/\{(.*)\}/', $content, $matches);
        $res = json_decode($matches[0], true);
        if (!$res) {
            showMsg(504, 'wrong json', $content);
            return false;
        }
        if ($res['code'] == 'needlogin') {
            // url的openid对应的ext失效
            M('user')->where(array('uid' => $uid))->save(array('status' => 1, 'last_article' => ''));
            showMsg(507, 'need login', $userInfo['url']);
            return false;
         }

        $article = M('article');
        import('Vendor.simpleHtmlDom.simple_html_dom');
        $html = new \simple_html_dom();
        $countArticle = count($res['items']);
        for ($i = $countArticle - 1; $i >= 0; $i--) {
            $item = $res['items'][$i];
            // 解析xml
            $item = str_replace('<?xml version="1.0" encoding="gbk"?>', '<?xml version="1.0" encoding="utf8"?>', $item);
            $articleArr = xmlToArray($item);
            $time = $articleArr['item']['display']['lastModified'];

            // 获取链接
            $contentUrl = $this->baseUrl . $articleArr['item']['display']['url'];
            $trueUrl = $this->curlRequest($contentUrl);
            if (!$trueUrl) {
                showMsg(505, 'wrong trueUrl', $contentUrl);
                break;
            }
            // html处理
            $articleContent = file_get_contents($trueUrl);
            $html->load($articleContent);
            $title = $html->find('#activity-name', 0)->plaintext;
            $content = $html->find('.rich_media_content', 0)->innertext;
            if (!$content) {
                showMsg(506, 'wrong content', array($articleContent, $content));
                break;
            }
            // 判断标题是否采集过
            $whereTmp['uid'] = $uid;
            $whereTmp['title'] = trim($title);
            if ($article->where($whereTmp)->find()) {
                unset($item, $articleArr, $whereTmp);
                continue;
            }
            $article_data = array(
                'uid' => $uid,
                'title' => trim($title) ?: '',
                'url' => $contentUrl ?: '',
                'realurl' => $trueUrl ?: '',
                'summary' => $articleArr['item']['display']['content168'] ?: '',
                'time' => $time ?: '',
                'content' => trim($content) ?: '',
                'addtime' => time(),
            );
            $result[] = $article->add($article_data);
        }

        return $result ?: 0;
    }

    /**
     * curl请求，通过设置cookie模拟浏览器
     * @param  string $url 地址
     * @return string      请求页面内容
     */
    public function curlRequest($url = '')
    {
        // curl请求
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 1); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, false); // post提交方式？
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($ch, CURLOPT_COOKIE, $this->setCookie);
        $response = curl_exec($ch); // 运行curl
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        // 分离header和body
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        // 解析http码，若出现302 antispider，则进行验证
        list($httpCode) = explode("\r", $header);
        if (strpos($httpCode, '302')) {
            $isMatched = preg_match_all('/Location:\s(.*)\s/', $response, $matchesHref);
            // 验证跳转链接
            if (strpos($matchesHref[1][0], 'antispider')) {
                $msg = '进行验证：<a target="_blank" href="' . $matchesHref[1][0] . '">点击进入</a>';
                showMsg(400, $msg, $matchesHref);
                F('mark', array(3, time()));
                die;
            } else {
                return $matchesHref[1][0];
            }
        }

        // 更新cookie
        $isMatched = preg_match_all('/Set-Cookie:\s(.*?)=(.*?);/', $header, $matchesCookie);
        foreach ($matchesCookie[1] as $k => $v) {
            $this->setCookie .= $v . '=' . $matchesCookie[2][$k] . '; ';
        }

        // 返回
        return $body;
    }

    /**
     * 返回一页html信息
     */
    protected function loadContent($type = 'user')
    {
        $content = '';
        // 采集文章列表
        if ($type == 'articlelist') {
            $url = $this->aritcleConfig['url'] . http_build_query($this->aritcleConfig['param']);
            $content = $this->curlRequest($url);
            if (!$content) {
                return false;
            }
        }

        if ($type == 'article') {
        }

        // 采集账户信息
        if ($type == 'user') {
            $url = $this->userConfig['url'] . http_build_query($this->userConfig['param']);
            $content = $this->curlRequest($url);
            if (!$content) {
                return false;
            }
        }

        // 返回
        return $content;
    }

    /**
     * 解析新闻内容，主要把图片替换为upyun
     */
    public function parseContent($content)
    {
        // 判断非空
        $content = trim($content);
        if ($content == '') {
            return false;
        }

        // 解析所有图片
        import('Vendor.simpleHtmlDom.simple_html_dom');
        $html = new \simple_html_dom();
        $upYun = new \Home\Model\UpyunModel();
        $html->load($content);
        $c = count($html->find('img'));
        foreach($html->find('img') as $k => &$img) {
            // 去掉最后一张图片，绝大多数最后一张图片都是广告
            if ($k == $c - 1) {
                foreach ($img as $k => &$v) {
                    $v = null;
                }
                break;
            }
            // 图片地址
            $imgDataSrc = $img->attr['data-src'];
            if ($img->src || !$imgDataSrc) {    // 已有了
                continue;
            }
            // 图片没类型时
            $imgDataType = $img->attr['data-type'];
            if (!$imgDataType) {
                $imgDataType = 'png';
            }
            // 保存图片到本地
            $imgPath = './Public/img/' . time() . mt_rand() . '.' . $imgDataType;
            $putResult = $this->getUrlImage($imgDataSrc, $imgPath);
            if (!$putResult) {
                showMsg(601, 'down img wrong', $imgDataSrc);
                continue;
            }
            // 上传到又拍云
            $upYunPath = date('Ym') . '/' . date('d') . '/' . time() . mt_rand() . '.' . $imgDataType;
            $upYunRes = $upYun->AddImage($this->upYunSavePath . $upYunPath, $imgPath);
            // 上传成功替换图片
            if ($upYunRes == 200) {
                $img->src = $this->upYunShowPath . $upYunPath;
                @unlink($imgPath);
            } else {
                showMsg(602, 'upyun wrong', array($upYunRes, $imgPath));
            }
            unset($imgDataSrc);
        }
        return $html->save();
    }

    /**
     * 下载远程图片方法
     * @param  string $url       地址
     * @param  string $folder    文件夹
     * @param  string $file_name 文件名
     * @return array             文件信息
     */
    protected function getUrlImage($url = '', $imgPath = '')
    {
        // 生成目录
        $ch = curl_init();
        // 生成图片
        $fp = fopen($imgPath, 'wb');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        // 返回
        return getimagesize($imgPath);
    }
}
