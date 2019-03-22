<?php
namespace Home\Controller;
use Think\Controller;
class HttpController extends Controller
{
    public function index()
    {
        set_time_limit(0);
        // 读取采到哪儿了
        $mydomain = M('mydomain');
        $id = $mydomain->max('id');
        $domain_res = $mydomain->where("id=$id")->field('word, tld')->find();
        $word = $domain_res['word'];
        $tld  = $domain_res['tld'];
        // 采集，写入数据库
        for ($i = 0; $i < 2000000000; $i++) {
            list($word, $tld) = $this->nextDomain($word, $tld);
            $url = "http://pandavip.www.net.cn/check/checksecondhand?keyword=$word&tld=$tld";
            $res_tmp = file_get_contents($url);
            preg_match('/^\((.*?)\);$/iU', $res_tmp, $json_tmp);
            $res = json_decode($json_tmp[1], true);
            $data_tmp['word']    = $word;
            $data_tmp['tld']     = $tld;
            $data_tmp['name']    = $res['module'][0]['name'];
            $data_tmp['avail']   = $res['module'][0]['avail'];
            if ($res['module'][0]['reason']) {
                $data_tmp['reason']   = $res['module'][0]['reason'];
            }
            $data_tmp['success'] = $res['success'] == 'true' ? 1 : 0;
            $data_tmp['res']     = $res_tmp;
            $mydomain->add($data_tmp);
            unset($res_tmp, $res, $data_tmp);
            sleep(1);
        }
    }

    private function nextDomain($word, $tld)
    {
        switch ($tld) {
            case 'com':
                return [$word, 'cn'];
                break;
            case 'cn':
                return [$word, 'net'];
                break;
            default:
                return [strPlusPlus($word), 'com'];
                break;
        }
    }

    /**
     * 测试jpush
     */
    public function jpush($str = 'test')
    {
        $url = 'https://api.jpush.cn/v3/push';
        $data = '{"platform":"all","audience":"all","notification":{"alert":"Hi,JPushss!"}}';
        $header = array('User-Agent' => 'JPush-API-PHP-Client',
            'Connection' => 'Keep-Alive',
            'Charset' => 'UTF-8',
            'Content-Type' => 'application/json');
        $ch = curl_init();      // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);        // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 1);        // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, true);       // post提交方式
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERPWD, '66628689c1c7702bbd6a54f8:b7a15c297cfa16a78aebee59');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($ch);      // 运行curl
        curl_close($ch);
        ob_start();$s=array($data, $res);foreach($s as $v){var_dump($v);}die('<pre style="word-wrap: break-word;">'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
    }

    public function curl()
    {
        // 测试请求
        $url  = "http://uc.zyrb.com.cn/admin.php";
        $post_data = array("nodeid" => 3);
        $curl = curl_init();  // 初始化
        curl_setopt($curl, CURLOPT_URL, $url);  // 地址
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded\r\n'));  // http请求头
        curl_setopt($curl, CURLOPT_HEADER, 0);  // 是否显示头信息
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  // 是否获取文本，1则输出data，不获取文本0则以文件流形式输出
        curl_setopt($curl, CURLOPT_POST, 1);  // 数据发送方式post
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);  // post数据
        $data = curl_exec($curl);  // 得到字串
        curl_close($curl);  // 关闭
        dump($data);
    }

    public function post()
    {
        $post['uid'] = 1;
        $post['days'] = 30;
        $opts['http']['method']  = 'POST';
        $opts['http']['content'] = http_build_query($post);
        $context  = stream_context_create($opts);
        $url = 'http://uc.zyrb.com.cn/index.php';
        $result = file_get_contents($url, false, stream_context_create($opts));

        dump($result);
    }

    /**
     * 发送电子邮件
     */
    public function mail()
    {
        set_time_limit(0);
        $res = mailTo139('有消息', '吃了吗?', './Public/ossec.sql');
        echo intval($res);
    }
}