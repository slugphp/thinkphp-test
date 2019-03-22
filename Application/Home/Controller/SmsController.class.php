<?php
namespace Home\Controller;
use Think\Controller;
class SmsController extends Controller
{
    function __construct()
    {}

    public function index()
    {
        set_time_limit(0);
        $sendSmsApiArrYes[] = [
            'url' => 'http://open.e.189.cn/api/common/sendSmsCode.do',
            'postData' => ['mobile' => 'mobile', 'appKey' => '189mail', 'apptype' => 'web',]
        ];
        $sendSmsApiArrTest[] = [
            'url' => 'http://mail.10086.cn/s',
            'getData' => [
                        'func' => 'login:sendSmsCode',
                        'cguid' => date('His') . mt_rand(100, 999) .  mt_rand(1000, 9999),
                        'randnum' => '0.' . mt_rand(1000, 9999) . mt_rand(1000, 9999) . mt_rand(1000, 9999) . mt_rand(1000, 9999),
                    ],
            'postData' => '<object><string name="loginName">$mobile</string><string name="fv">4</string><string name="clientId">1003</string><string name="version">1.0</string></object>',
        ];
        $mobile = 13466668888;
        foreach ($sendSmsApiArrTest as $k => $api) {
            $api['url'] = $api['url'] . '?' . http_build_query($api['getData']);
            if (is_string($api['postData'])) {
                $api['postData'] = str_replace('$mobile', $mobile, $api['postData']);
            }
            if (is_array($api['postData'])) {
                $api['postData']['mobile'] = $mobile;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api['url']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'text/javascript;charset=UTF-8','SOAPAction: ""',
                ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if ($api['postData']) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $api['postData']);
            }
            $response = curl_exec($ch);
            curl_close($ch);
            ob_start();$s=array($response);foreach($s as $v){var_dump($v);}die('<pre style="white-space:pre-wrap;word-wrap:break-word;">'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'');
            writeFileLog('./Public/log/send_sms_api.log', $api, $response);
        }
    }

    public function getChinaUnicomPhoneNum()
    {
        set_time_limit(0);
        while (1) {
            $num = $this->getChinaUnicomPhoneNumMethod();
            writeFileLog('./Public/log/china_unicom_num.log', $num);
            sleep(1);
        }
    }

    public function getChinaUnicomPhoneNumMethod()
    {
        $url = 'http://num.10010.com/NumApp/GoodsDetail/queryMoreNums?callback=jsonp_queryMoreNums&province=11&cityCode=110&rankMoney=0&groupKey=xxx&mid=&q_p=11&net=01&roleValue=&preFeeSel=1&keyValue=&Show4GNum=TRUE&goodsNet=4&q_p=' . mt_rand(10, 99) . '&_=' . time() . mt_rand(100, 999);
        $ch = curl_init();    // 初始化
        curl_setopt($ch, CURLOPT_URL, $url);    // url地址
        curl_setopt($ch, CURLOPT_HEADER, 0);    // 是否显示头信息
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);    // 最长秒数
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 是否获取文本，不获取文本则以文件流形式输出
        $response = curl_exec($ch);    // 获取文本为1则得到字串
        curl_close($ch);    // 关闭
        $resJson = str_replace(['jsonp_queryMoreNums(', ');'], [], $response);
        $res = json_decode($resJson, true);
        $num = [];
        foreach ($res['moreNumArray'] as $v) {
            if (preg_match('/1\d{10}/', $v)) {
                $num[] = $v;
            }
        }
        return array_unique($num);
    }
}
