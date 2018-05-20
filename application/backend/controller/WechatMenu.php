<?php
/**
 * Created by PhpStorm.
 * User: zzhh
 * Date: 2017/12/26
 * Time: 下午3:12
 */

namespace app\backend\controller;


use extend\helper\Curl;
use extend\service\RedisService;

class WechatMenu extends BaseAdmin
{
    private $gzh_appid = 'wx349ee50cf38f3943';
    private $gzh_secret = '16eba773eafd7438d282bc43ed83db37';
    private $redis = null;
    function __construct()
    {
        $this->redis = new RedisService();
    }
    public function getBaseAccessToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token";
        $data = [
            'appid' => $this->gzh_appid,
            'secret' => $this->gzh_secret,
            'grant_type' => 'client_credential',
        ];
        $tokencache = $this->redis->del('gzh_accesstoken');
        $tokencache = $this->redis->get('gzh_accesstoken');

        $tokens = json_decode($tokencache,true);
        if($tokens['time']+7100 < time()){
            $json = Curl::buildHttp($url, $data);
            $result = json_decode($json, true);
            $result['time'] = time();
            $this->redis->set('gzh_accesstoken',json_encode($result));
            if (isset($result['access_token'])) {
                return $result['access_token'];
            } else {
                return false;
            }
        }
        return $tokens['access_token'];
    }

    public function create_menu(){
        $accessToken = $this->getBaseAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;

//        $data = array('button' =>
//            array(
//                array('type'=>'view','name'=>'找助教','url'=>'https://tutor.pgyxwd.com/h5-index.html#/assistant'),
//                array('type'=>'view','name'=>'完善二维码','url'=>'https://tutor.pgyxwd.com/h5-index.html#/upload')
//            )
//        );
        $data = [
            'button' =>
            [
                [
                    'name'=>'找家长/家教',
                    'sub_button'=>[
                        ['type'=>'miniprogram','name'=>'找家长','url'=>'http://mp.weixin.qq.com','appid'=>'wxc06f8e2831ab996a','pagepath'=>'pages/index/index'],
                        ['type'=>'miniprogram','name'=>'找家教','url'=>'http://mp.weixin.qq.com','appid'=>'wxc06f8e2831ab996a','pagepath'=>'pages/index/index'],
                    ],
                ],
                [
                    'name'=>'我的服务',
                    'sub_button'=>[
                        ['type'=>'view','name'=>'找助教','url'=>'https://tutor.pgyxwd.com/h5-index.html#/assistant'],
                        ['type'=>'view','name'=>'完善二维码','url'=>'https://tutor.pgyxwd.com/h5-index.html#/upload'],
                    ]
                ]
            ]
        ];
        $jaondata = json_encode($data,JSON_UNESCAPED_UNICODE);
        //$res = $this->request->Curl_Post2($url,$jaondata);         //注释，禁止创建
        $res = Curl::postJson($url,$jaondata);
        var_dump($res);
    }
}