<?php
/**
 * Created by PhpStorm.
 * User: laoluo
 * Date: 2018/5/23
 * Time: 下午2:46
 * @introduce
 */

namespace extend\service;


use extend\helper\Curl;

class ComWechat
{
    protected $config = null;

    function __construct()
    {
        $this->config = config('comwx');
        $this->redis = new RedisService();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-23
     *
     * @description 获取access_token
     * @return bool
     */
    public function getAccessToken($corpsecret = 'wx_corpsecret')
    {
        $tokencache = $this->redis->get($this->config[$corpsecret]);

        $tokens = json_decode($tokencache,true);
        if(empty($token['time']) || $tokens['time']+7100 < time()){
            $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$this->config['corpid'].'&corpsecret='.$this->config[$corpsecret];
            $result = Curl::getJson($url);
            $result['time'] = time();
            $this->redis->set($this->config[$corpsecret],json_encode($result));
            if (isset($result['access_token'])) {
                return $result['access_token'];
            } else {
                return false;
            }
        }
        return $tokens['access_token'];
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-23
     *
     * @description 获取session_key
     * @return bool
     */
    public function getSessionKey($access_token,$code)
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/miniprogram/jscode2session?access_token='.$access_token.'&js_code=' . $code . '&grant_type=authorization_code';
        $res = Curl::getJson($url);
        return $res;
    }
}