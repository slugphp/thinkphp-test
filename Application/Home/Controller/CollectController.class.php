<?php

/**
 * 采集数据推送，保证更新
 * @author weilong qq:973885303
 */

namespace Home\Controller;

use Think\Controller;

class CollectController extends BaseController
{
    // 又拍云配置
    protected $upYunSavePath = '/static-news/web/toutiao/';
    protected $upYunShowPath = 'http://static-news.xxxx.com/web/toutiao/';
    protected $dbname;
    /**
     * 推送控制
     * 每1min请求一次
     */
    public function push()
    {
        set_time_limit(0);

        // 判断时间点
        $h = date('H');
        if ($h < 9 || $h >= 21) die;
        $i = date('i');

        // caiji365，3分钟才一次
        if (1 && $i % 3 == 0) {
            $caiji365Replace = [
                    ['/\<div\salign\=center\>div\>/i', '/(<)?\sbr\s\/\>/i'],
                    ['<div>', '<br\>']
                ];
            $this->pushModel('caiji365', $caiji365Replace, 60*60);
        }

        // zhanhui，5分钟才一次
        if (1 && $i % 5 == 0) {
            $this->pushModel('zhanhui', [], 60*60*24*30);
        }

        // tbspp，1分钟才一次
        // if (1 && $i % 1 == 0) {
        //     $this->pushModel('tbspp', [], 60*60*24*30);
        // }

        // meilele，1分钟才一次
        if (1 && $i % 1 == 0) {
            $meileleReplace = [
                    ['/<strong>(.*?)装修网<\/strong>/i'],
                    ['<strong>一起装修网</strong>', ]
                ];
            $this->pushModel('meilele', $meileleReplace, 60*60);
        }

        // zxlc，1分钟才一次
        if (1 && $i % 1 == 0) {
            $zxlcReplace = [
                    ['/<strong>(.*?)装修网<\/strong>/i'],
                    ['<strong>一起装修网</strong>', ]
                ];
            $this->pushModel('zxlc', $zxlcReplace, 60*60);
        }

        //note 新增11个新表
	    // 1 sfsj，1分钟才一次
        if (1 && $i % 1 == 0) {
            $sfsjReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('sfsj', $sfsjReplace, 60*60);
        }
        // 2 wsj，1分钟才一次
        if (1 && $i % 1 == 0) {
            $wsjReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('wsj', $wsjReplace, 60*60);
        }
        // 3 wsh，1分钟才一次
        if (1 && $i % 1 == 0) {
            $wshReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('wsh', $wshReplace, 60*60);
        }
        // 4 zxlc，1分钟才一次
        if (1 && $i % 1 == 0) {
            $fshoneReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('fshone', $fshoneReplace, 60*60);
        }
        // 5 zxlc，1分钟才一次
        if (1 && $i % 1 == 0) {
            $bshzhuangxReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('bshzhuangx', $bshzhuangxReplace, 60*60);
        }
        // 6 cfsj，1分钟才一次
        if (1 && $i % 1 == 0) {
            $cfsjReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('cfsj', $cfsjReplace, 60*60);
        }
        // 7 rtsj，1分钟才一次
        if (1 && $i % 1 == 0) {
            $rtsjReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('rtsj', $rtsjReplace, 60*60);
        }
        // 8 ktsj，1分钟才一次
        if (1 && $i % 1 == 0) {
            $ktsjReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('ktsj', $ktsjReplace, 60*60);
        }
        // 9 fshuitwo，1分钟才一次
        if (1 && $i % 1 == 0) {
            $fshuitwoReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('fshuitwo', $fshuitwoReplace, 60*60);
        }
        // 10 bangong，1分钟才一次
        if (1 && $i % 1 == 0) {
            $bangongReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('bangong', $bangongReplace, 60*60);
        }
        // 11 zyshxiang，1分钟才一次
        if (1 && $i % 1 == 0) {
            $zyshxiangReplace = [
                ['/<strong>(.*?)装修网<\/strong>/i'],
                ['<strong>一起装修网</strong>', ]
            ];
            $this->pushModel('zyshxiang', $zyshxiangReplace, 60*60);
        }

       echo 'done';
    }

    /**
     * 推送
     * 可在 Public/log/下看日志文件
     * @param    $dbname   推送表
     * @param    $replace  替换规则
     * @param    $pregTime 发表提前时间
     * @param    $type  article=>新闻   blog=>专栏
     */
    public function pushModel($dbname, $replace, $pregTime = 0, $type = 'article')
    {
        $this->dbname = $dbname;
        $type = $type == 'article' ? 'article' : 'blog';
        $url = 'http://news.xxxx.com/dataapi.php?mod=' . $type;
        $model = M($dbname);
        $ispushed = array('ispushed' => 0);
        $ispush = array('ispushed' => 1);

        // 没有数据，空？
        $data = $model->where($ispushed)->limit('id asc')->find();
        if (!$data['content']) {
            $this->showMsg($dbname, 401, 'empty db', $data);
		return false;
        }

        // 内容更新图片链接
        $content = $data['content'];
        $content = $this->parseContent($content);
        if (!$content) {
            $this->showMsg($dbname, 401, 'parse content error', $data);
		return false;
        }

        // replace
        if ($replace) {
            $content = preg_replace($replace[0], $replace[1], $content);
        }

        // 推送
        $postData['title'] = $data['title'];
        $postData['content'] = $content;
        $postData['dateline'] = time() - intval($pregTime);
        if ($data['tagname']) {
            $postData['tagname'] = $data['tagname'];
        }
        $key = 'xxxx';
        $postData['token'] = $this->authcode('xxxxToutiao', 'ENCODE', $key);
        $res = postData($url, $postData);
        $model->where(array('id' => $data['id']))->save($ispush);
    	$res['cid'] = $data['id'];
        $this->showMsg($dbname, 200, 'success', $res);
    }

    /**
     * post 请求
     * @param  string $url
     * @param  array  $data
     * @return [type]
     */
    public function post($url = '', $data = array())
    {
        $curl = curl_init();  // 初始化
        curl_setopt($curl, CURLOPT_URL, $url);  // 地址
        curl_setopt($curl, CURLOPT_POST, 1);  // 数据发送方式post
        curl_setopt($curl, CURLOPT_HEADER, 0);  // 是否显示头信息
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  // 是否获取文本，不获取文本则以文件流形式输出
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  // post数据
        $response = curl_exec($curl);  // 得到字串
        curl_close($curl);  // 关闭
        return $response;
    }

    protected function parseContent($content)
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
        if (count($html->find('img')) < 1) {
            return $content;
        }
        foreach($html->find('img') as $k => $img) {
            // 图片地址
            $imgDataSrc = $img->attr['src'];
            if (!$imgDataSrc) {    // 已有了
                continue;
            }
            // 图片没类型时
            $imgDataType = 'png';

            // 保存图片到本地
            $imgPath = './Public/img/' . time() . mt_rand() . '.' . $imgDataType;
            $putResult = $this->getUrlImage($imgDataSrc, $imgPath);
            if (!$putResult) {
                $this->showMsg($this->dbname, 601, 'down img wrong', $imgDataSrc);
                continue;
            }
            // 上传到又拍云
            $upYunPath = date('Ym') . '/' . date('d') . '/' . time() . mt_rand() . '.' . $imgDataType;
            $upYunRes = $upYun->AddImage($this->upYunSavePath . $upYunPath, $imgPath);

            // 上传成功替换图片
            if ($upYunRes == 200) {
                $html->find('img')[$k]->src = $this->upYunShowPath . $upYunPath;
                @unlink($imgPath);
            } else {
                $this->showMsg($this->dbname, 602, 'upyun wrong', array($upYunRes, $imgPath));
            }
            unset($imgDataSrc);
        }
        return $return = $html->save();
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
