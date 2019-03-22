<?php

/**
 * 闲鱼采集数据
 * @author weilong qq:973885303
 */

namespace Home\Controller;
use Think\Controller;
class XianyuController extends BaseController
{
    protected $idArr = [
            '439' => '华北电力大学',
            '422839' => '佰嘉城',
            '435929' => '国仕汇',
            '498921' => '天露园',
            '441440' => '中轻大厦',
            '3665' => '龙兴园',
        ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 采集控制
     */
    public function index()
    {
        foreach ($this->idArr as $id => $name) {
            $count = 0;
            $data = $this->getXianyuList($id);
            foreach ($data as $item) {
                $res = $this->addItem($item);
                if ($res > 1) {
                    $count++;
                }
            }
            $res = $name .  "    " . $count . "\r\n";
	    writeFileLog('./Public/log/xianyu_res.log', $res);
        }
    }

    /**
     * 列表数据
     */
    public function admin()
    {
        list($list, $count) = $this->getData();
        $page = new \Think\Page($count, 30);
        $this->assign('page',$page->show());
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 喜欢
     */
    public function like()
    {
        $m = M('fish_goods');
        $id = intval($_GET['id']);
        if (!$id) {
            die('-1');
        }
        $where['id'] = $id;
        $data['like'] = 1;
        if (!$m->where($where)->save($data)) {
            $data['like'] = 0;
            $m->where($where)->save($data);
        }
        die('1');
    }

    public function getData()
    {
        // wher条件
        $where['id'] = ['gt', 1];
        if ($_GET['readed']) $where['readed'] = 0;
        if ($_GET['like']) {
            $where['like'] = 1;
        }
        if ($_GET['fish_title']) {
            $where['fish_title'] = ['like', "%{$_GET['fish_title']}%"];
        }
        // 分页
        $page = intval($_GET['p']) ?: 1;
        $pageSize = intval($_GET['pageSize']) ?: 30;
        $start = ($page - 1) * $pageSize;
        // 查找数据
        $m = M('fish_goods');
        $data = $m->where($where)
            ->limit($start, $pageSize)
            ->order('fish_time desc')
            ->select();
        // 处理时间、图片
        foreach ($data as $k => &$v) {
            $v['fish_time'] = time_tran($v['fish_time']);
            $imgArr = explode(',', $v['fish_image_url']);
            $imgArr = array_splice($imgArr, 0, 4);
            foreach ($imgArr as $img) {
                $imgRes[] = $img . '_100x100xz.jpg';
            }
            $v['fish_image_url'] = $imgRes;
            $readId[] = $v['id'];
            unset($imgArr, $imgRes);
        }
        // 标记为已读
        if ($data) {
            $readWhere['id'] = ['in', $readId];
            $readData['readed'] = 1;
            $m->where($readWhere)->save($readData);
        }
        $count = $m->where($where)->count();
        if ($_GET['json']) {
            echo json_encode($data);die;
        }
        return [$data, $count];
    }

    protected function getXianyuList($id)
    {
        if (!array_key_exists($id, $this->idArr)) {
            return [];
        }
        $url = "https://api.2.taobao.com/m/data.action?name=idleFishPoolItemList@1&data={%22topicRule%22:%22{\%22fishpoolId\%22:$id}%22}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = str_replace(['___jsonp___(', ');'], [], $response);
        return json_decode($json, true)['idleFishPoolItemList@1']['data']['items'];
    }

    protected function addItem($item)
    {
        $m = M('fish_goods');
        if ($m->where(['fish_id' => $item['id']])->find()) {
            return false;
        }
        $insertData = [
                'fish_id' => $item['id'],
                'fish_pool_id' => $item['fishpoolId'],
                'fish_pool_name' => $item['fishpoolName'],
                'fish_title' => $item['title'],
                'fish_time' => strtotime($item['firstModified']),
                'fish_price' => $item['price'],
                'fish_url' => $item['shortUrl'],
                'fish_image_url' => implode(',', $item['imageUrls']),
                'fish_text' => json_encode($item),
            ];
        $res = $m->add($insertData);
	writeFileLog('./Public/log/xianyu.log', $item);
        return $res;
    }
}
