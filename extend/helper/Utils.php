<?php
namespace extend\helper;

use extend\service\storage\OssService;

class Utils
{


    /** 随机生成串
     * auth smallzz
     * @param int $length
     * @return string
     */
    public static function randomString($length = 88)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 处理json
     * @param $str
     * @return array|mixed
     */
    public static function parseJson($str)
    {
        $result = json_decode(str_replace('&quot;', '"', $str), true) ? json_decode(str_replace('&quot;', '"', $str), true) : [];

        return $result;
    }


    /** xml解析
     * auth smallzz
     * @param string $msgData
     * @return array|bool
     */
    public static function parseMsgData(string $msgData)
    {
        $pos = strpos($msgData, 'xml');

        if (!$pos) {
            return false;
        }

        $message = simplexml_load_string($msgData, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (is_object($message)) {
            return get_object_vars($message);
        }

        return false;
    }

    /** 生成订单号
     * auth smallzz
     * @param string $fix UTSEOR用户主动  ETNTOE企业主动
     * @return string
     */
    public static function createOrderSn(string $fix = 'UTSEOR'){

         return $fix.str_replace('.', '', microtime(true)).rand(0,9).rand(0,99999);
    }

    /**
     * 生成token
     * @param int $length
     * @return string
     */
    public static function createToken($length = 88)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+=";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     *
     * 用户的密码进行加密
     * @param $password
     * @param string $encrypt
     * @return array|string
     */
    public static function genPassword($password, $encrypt = '')
    {
        $pwd = [];
        $pwd['encrypt'] = $encrypt ? $encrypt : self::salt();
        $pwd['password'] = sha1(md5(trim($password)) . $pwd['encrypt']);
        return $encrypt ? $pwd['password'] : $pwd;
    }

    /**
     * 生成盐值
     * @return string
     */
    public static function salt()
    {
        return substr(uniqid(), -5);
    }

    /**
     * @Author liyongchuan
     * @DateTime
     *
     * @description 根据身份证获取性别
     * @param string $idcard
     * @return int
     */
    public static function getSexByID(string $idcard)
    {
        $s = (int)substr($idcard, -2, 1);
        if ($s % 2 == 0) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * @Author zhanglei
     * @DateTime 2017-12-13
     *
     * @description 获取年龄
     * @param $birthday
     * @return bool|int
     */
    public static function getAge($birthday)
    {
        $age = strtotime($birthday);
        if ($age === false) {
            return false;
        }
        list($y1, $m1, $d1) = explode('-', date('Y-m-d', $age));
        $now = strtotime('now');
        list($y2, $m2, $d2) = explode('-', date('Y-m-d', $now));
        $age = $y2 - $y1;
        if ((int)($m2 . $d2) < (int)($m1 . $d1)) {
            $age -= 1;
        }

        return $age;
    }

    /**
     * @Author liyongchuan
     * @DateTime
     *
     * @description 根据身份证获取年龄
     * @param string $idcard
     * @return int
     */
    public static function getAgeByID(string $idcard)
    {
        $year = date('Y',time())-substr($idcard,6,4);
        $month = ((date('m',time()).date('d',time()))<substr($idcard,10,4)) ? -1 : 0;
        return $year+$month;
    }

    /**
     * @Author panhao
     * @DateTime 2017-12-27
     *
     * @description 获取当前时间毫秒级
     * @return float
     */
    public static function getMicroTime()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-04
     *
     * @description 根据城市获取省份
     * @param string $city
     * @param int $cut 是否删除最后一个字
     * @return array
     */
    public static function getProvinceByCity(string $city, int $cut = 1)
    {
        $citys = config('city');
        $province = '';
        if ($cut == 1) {
            $city = mb_substr($city,0,mb_strlen($city)-1,'utf-8');
        }
        foreach ($citys as $key => $value) {
            foreach ($value['child'] as $k => $v) {
                if ($city == $v) {
                    $province = $value['label'];
                    break;
                }
            }
        }
        return ['province'=>$province,'city'=>$city];
    }

    /**
     * @Author panhao
     * @DateTime 2017-12-26
     *
     * @description 上传oss图片(base64)
     * @param array $img
     * @param string $type
     * @return array
     */
    public static function ossUpload64(array $img, string $type)
    {
        //上传图片
        $oss = new OssService(config('oss.default_bucket_name'));
        $data = [];
        foreach ($img as $key => $value) {
            $time = Utils::getMicroTime();
            $tempImgUrl = $oss->ossBase64Upload($value);

            $ossUrl = $type . '/' . $time . config('oss.temp_file_suffix');
            $oss->uploadOss($ossUrl, $tempImgUrl, true);
            $data[] = $ossUrl;
        }

        return $data;
    }

    /**
     * @Author panhao
     * @DateTime 2018-02-04
     *
     * @description 上传图片
     * @param $newupfile
     * @param $upfile
     * @param string $path
     * @return bool
     */
    public static function picOssUp($newupfile,$upfile,$path = 'qrcode'){
        $oss = new OssService(config('oss')['default_bucket_name']);
        $info = $oss->uploadOss($path.'/'.$newupfile,$upfile,true);
        #清理垃圾
        if(is_file($upfile)){
            unlink($upfile);
        }
        return $path.'/'.$newupfile;

    }

    /**
     * @Author panhao
     * @DateTime 2018-02-04
     *
     * @description 上传图片文件
     * @param $file
     * @param string $path
     * @return array|bool
     */
    public static function uploadPic($file,$path = 'qrcode')
    {
        $filetime = Files::createFileName();
        $newfile = 'tmp/'. $filetime . '.jpg';
        $res = move_uploaded_file($file['file']['tmp_name'], $newfile);
        if (!empty($res)) {
            return self::picOssUp($filetime . '.jpg', $newfile,$path);
        }
        return [];
    }

}