<?php
namespace Home\Controller;
use Think\Controller;
class ImgController extends Controller
{
    public function index()
    {
        echo base64_encode(file_get_contents('./Public/22.png'));
        $str = '/9j/4AAQSkZJRgABAQAASABIAAD/4QBYRXhpZgAATU0AKgAAAAgAAgESAAMAAAABAAEAAIdpAAQAAAABAAAAJgAAAAAAA6ABAAMAAAABAAEAAKACAAQAAAABAAACgKADAAQAAAABAAACgAAAAAD/7QA4UGhvdG9zaG9wIDMuMAA4QklNBAQAAAAAAAA4QklNBCUAAAAAABDUHYzZjwCyBOmACZjs+EJ+/8AAEQgCgAKAAwERAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/bAEMAAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/bAEMBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/dAAQAUP/aAAw';
        $this->parseImgStr($str, './Public/test.jpg');
    }

    /**
     * getimagesize()运行时间测试
     *
     * res: URL模式很慢
     */
    public function gisTime()
    {
        // 准备参数
        $img_local = './Public/get.jpg';
        $img_host  = 'http://localhost/test/tp/Public/get.jpg';       
        $img_url   = 'http://desk.fd.zol-img.com.cn/g2/M00/06/03/Cg-4WlVIeFSIaYNQAAcIw7QPVPAAAC8swKpRt8ABwjb455.jpg';
        echo now_time(), '<br>';
        // 本地相对路径----0.01s
        for ($i = 0; $i < 10; $i++) { 
            getimagesize($img_local);
        }
        echo now_time(), '<br>';
        // 本地host----0.1s
        for ($i = 0; $i < 10; $i++) { 
            getimagesize($img_host);
        }
        echo now_time(), '<br>';
        // 联网url----5s+
        for ($i = 0; $i < 10; $i++) { 
            getimagesize($img_url);    // 很慢很卡
        }
        echo now_time(), '<br>';
    }

    /**
     * 验证图片是否为正常图片
     *
     * res: URL模式很慢
     */
    public function yanImg()
    {
        // 准备参数
        $img = './Public/get.jpg';
        echo '<br>', now_time(), '<br>';
        // 本地相对路径----0.01s
        for ($i = 0; $i < 100; $i++) { 
            if ($this->fileYan($img))
                echo 1;
        }
        echo '<br>', now_time(), '<br>';
        // 本地host----0.1s
        for ($i = 0; $i < 100; $i++) { 
            if ($this->getimagesizeYan($img))
                echo 2;
        }
        echo '<br>', now_time(), '<br>';
        // 联网url----5s+
        for ($i = 0; $i < 100; $i++) { 
            if ($this->gdYan($img))
                echo 3;
        }
        echo '<br>', now_time(), '<br>';
    }

    private function fileYan($img)
    {
        $file = fopen($img, "rb");
        $bin  = fread($file, 3); //只读2字节
        fclose($file);
        $strInfo  = @unpack("C2chars", $bin);
        $typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
        $fileType = '';
        if ($typeCode != 255216 /*jpg*/ && $typeCode != 7173 /*gif*/ && $typeCode != 13780 /*png*/) {
            return false;
        } else {
            return true;
        }
    }

    private function getimagesizeYan($img)
    {
        if (getimagesize($img)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * gd验证图片
     *
     * 略慢
     * 
     * @param  str $img 图片
     * @return boolen
     */
    private function gdYan($img)
    {
        $img_data = file_get_contents($img);
        $im = @imagecreatefromstring($img_data);
        if ($im == false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 数据流转图片
     */
    private function parseImgStr($str, $img)
    {
        file_put_contents($img, base64_decode($str));
    }

}