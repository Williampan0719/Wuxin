<?php
/**腾讯优图
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/27
 * Time: 下午3:21
 */

namespace extend\service;
use extend\service\TencentYoutuyun\Youtu;
use extend\service\TencentYoutuyun\Conf;


class Txyt
{
    private $config = null;
    function __construct()
    {
        $this->config = config('txyoutu');
        Conf::setAppInfo($this->config['APP_ID'], $this->config['SECRET_ID'], $this->config['SECRRT_KEY'], $this->config['USER_ID'],conf::API_YOUTU_END_POINT );
    }
    #通用ocr
    public function generalocr(string $imagepath){
        $res = YouTu::generalocr($imagepath, $seq = '');
        $arr = [];

        if($res['errorcode'] == 0 && $res['errormsg'] == 'OK'){
            foreach ($res['items'] as $k=>$v){
                $arr[] = $v['itemstring'];
            }
            $data = [];
            $name_1 = array_search('真实姓名',$arr);
            $data['name'] = $name_1 ? $arr[$name_1+1] : '';
            $idcard_1 = array_search('身份证号',$arr);
            $data['idcard'] = $idcard_1 ? $arr[$idcard_1-1] : '';
            $school_1 = array_search('学校',$arr);
            $data['school'] = $school_1 ? $arr[$school_1+1] : '';
            $diploma_1 = array_search('学历',$arr);
            $data['diploma'] = $diploma_1 ? $arr[$diploma_1+1] : '';
            $addschool_1 = array_search('入学年份',$arr);
            $data['addschool'] = $addschool_1 ? $arr[$addschool_1+1] : '';
            $validatime_1 = array_search('验证时间',$arr);
            $data['validatime'] = $validatime_1 ? $arr[$validatime_1+1] : '';
            return $data;
        }
        return false;
    }

}