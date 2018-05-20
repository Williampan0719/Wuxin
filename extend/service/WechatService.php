<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/1/5
 * Time: 下午2:52
 */

namespace extend\service;


use extend\helper\Curl;
use extend\helper\Files;
use think\Exception;

class WechatService
{
    protected $config = [];
    protected $redis = null;
    protected $wechattpl = null;
    function __construct()
    {
        $this->config = config('wechat');
        $this->redis = new RedisService();
        $this->wechattpl = new WechatTpl();
    }

    /**code换取 session_key
     * auth smallzz
     * @param $code
     * @return bool|mixed
     */
    public function getSessionKey($code)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->config['small_appid'] . '&secret=' . $this->config['small_appsecret'] . '&js_code=' . $code . '&grant_type=authorization_code';
        $res = Curl::getJson($url);
        return $res;
    }

    /**
     * 发送客服消息(文字模板)
     * @param string $openid
     * @param string $content
     * @return \Exception|mixed|Exception
     */
    public function sendTextMsg2(string $openid, string $content)
    {
        $data = [
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content' => $content,
            ],
        ];
        try {
            return $this->sendMsg2($data);

        } catch (Exception $exception) {

            return $exception;
        }
    }

    /**
     * 推送消息
     * @param array $data
     * @return mixed
     */
    protected function sendMsg2(array $data)
    {

        $baseAccessToken = $this->getServerAccessToken2();
        if ($baseAccessToken != false) {

            $accessToken = $baseAccessToken;
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accessToken;

        $params = $this->jsonEncode($data);
        try {
            $result=Curl::buildHttp($url, $params, 'post', $this->header, true);
            return $result;

        } catch (Exception $exception) {

            return $exception;
        }
    }

    /**检验数据的真实性，并且获取解密后的明文.
     * auth smallzz
     * @param $appid
     * @param $sessionKey
     * @param $encryptedData   加密的用户数据
     * @param $iv       与用户数据一同返回的初始向量
     * @param $data     解密后的原文
     * @return int      成功0，失败返回对应的错误码
     */
    public function decryptData($sessionKey, $encryptedData, $iv, &$data)
    {
        if (strlen($sessionKey) != 24) {
            return -41001;
        }
        $aesKey = base64_decode($sessionKey);
        if (strlen($iv) != 24) {
            return -41002;
        }
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return -41003;
        }
        if ($dataObj->watermark->appid != $this->config['small_appid']) {
            return -41004;
        }
        $data = $result;
        return $data;
    }

    /** 获取小程序access_token
     * auth smallzz
     * @param $key
     * @param $data
     * @return bool
     */
    public function getAccessToken()
    {
        #$this->redis->del($this->config['small_accesstoken']);
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->config['small_appid'] . '&secret=' . $this->config['small_appsecret'];
        $tokenCache = $this->redis->get($this->config['small_accesstoken']);
        if ($tokenCache == false) {
            $result = Curl::getJson($url);
            #$result = json_decode($json, true);
            $result['time'] = time();
            $this->redis->set($this->config['small_accesstoken'], json_encode($result));
            $accessToken = $result['access_token'] ?? false;
        } else {
            $tokens = json_decode($tokenCache, true);

            if ($tokens['time'] + 7000 < time()) {
                $result = Curl::getJson($url);
                #$result = json_decode($json, true);
                $result['time'] = time();
                $this->redis->set($this->config['small_accesstoken'], json_encode($result));
                $accessToken = $result['access_token'] ?? false;
            } else {
                $accessToken = $tokens['access_token'];
            }
        }
        return $accessToken;
    }
    /** 获取微信公众号access_token
     * auth smallzz
     * @param $key
     * @param $data
     * @return bool
     */
    public function getAToken()
    {
        #$this->redis->del($this->config['wechat_accesstoken']);
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->config['wx_appid'] . '&secret=' . $this->config['wx_appsecret'];
        $tokenCache = $this->redis->get($this->config['wechat_accesstoken']);
        if ($tokenCache == false) {
            $result = Curl::getJson($url);
            #$result = json_decode($json, true);
            $result['time'] = time();
            $this->redis->set($this->config['wechat_accesstoken'], json_encode($result));
            $accessToken = $result['access_token'] ?? false;
        } else {
            $tokens = json_decode($tokenCache, true);
            if ($tokens['time'] + 7000 < time()) {
                $result = Curl::getJson($url);
                #$result = json_decode($json, true);
                $result['time'] = time();
                $this->redis->set($this->config['wechat_accesstoken'], json_encode($result));
                $accessToken = $result['access_token'] ?? false;
            } else {
                $accessToken = $tokens['access_token'];
            }
        }
        return $accessToken;
    }

    /** 获取api_ticket
     * auth smallzz
     * @return bool
     */
    public function getTicket(){
        #$this->redis->del($this->config['wechat_api_ticket']);
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->getAToken().'&type=wx_card';
        $tokenCache = $this->redis->get($this->config['wechat_api_ticket']);
        if ($tokenCache == false) {
            $result = Curl::getJson($url);

            //var_dump($result);exit;
            $result['time'] = time();
            $this->redis->set($this->config['wechat_api_ticket'], json_encode($result));
            $apiTicket = $result['ticket'] ?? false;
        } else {
            $tokens = json_decode($tokenCache, true);
            //var_dump($tokens);exit;
            if ($tokens['time'] + 7000 < time()) {
                $result = Curl::getJson($url);
                $result['time'] = time();
                $this->redis->set($this->config['wechat_api_ticket'], json_encode($result));
                $apiTicket = $result['ticket'] ?? false;
            } else {
                $apiTicket = $tokens['ticket'];
            }
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/tmp/tick.txt','no:'.$apiTicket."\r\n",FILE_APPEND);
        return $apiTicket;
    }

    /** 获取Ticket
     * auth smallzz
     * @return bool
     */
    public function getSmallTicket(){
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->getAccessToken().'&type=wx_card';
        $tokenCache = $this->redis->get($this->config['small_wechat_api_ticket']);
        if ($tokenCache == false) {
            $result = Curl::getJson($url);
            $result['time'] = time();
            $this->redis->set($this->config['small_wechat_api_ticket'], json_encode($result));
            $apiTicket = $result['ticket'] ?? false;
        } else {
            $tokens = json_decode($tokenCache, true);
            if ($tokens['time'] + 7000 < time()) {
                $result = Curl::getJson($url);
                $result['time'] = time();
                $this->redis->set($this->config['small_wechat_api_ticket'], json_encode($result));
                $apiTicket = $result['ticket'] ?? false;
            } else {
                $apiTicket = $tokens['ticket'];
            }
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/tmp/tick.txt','small:'.$apiTicket."\r\n",FILE_APPEND);
        return $apiTicket;
    }
    /** 生成二维码
     * auth smallzz
     * @param string $path
     * @param int $width
     * @return string
     */
    public function getQrCodes(string $scene,string $page, int $width,int $type)
    {
        $url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $this->getAccessToken();
        $data['path'] = $page.$scene;
        $data['width'] = $width;
        $result = Curl::postJson($url, $data, false);
        #创建画布
        $file = $_SERVER['DOCUMENT_ROOT'] . '/video/';
        $hz = Files::createFileName().'.png';
        $url = 'https://'.$_SERVER['HTTP_HOST'].'/video/'.$hz;
        file_put_contents($file.$hz,$result);
        if($type == 1){
            return $file.$hz;
        }
        return $url;
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-10
     *
     * @description 创建二维码(推广用)
     * @param string $scene
     * @param string $page
     * @param int $width
     * @param int $type
     * @return string
     */
    public function getQrCode(string $scene='',string $page='',int $width=0,int $type){
        $url = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $this->getAccessToken();
        $data = [];
//        if(!empty($scene)){
//            $data['scene'] = $scene;
//        }
        if(!empty($page)){
            $data['path'] = $page.'?scene='.$scene;
        }
        if(!empty($width)){
            $data['width'] = $width;
        }
        $result = Curl::postJson($url, $data, false);
        #var_dump($result);exit;
        #创建画布
        $file = $_SERVER['DOCUMENT_ROOT'] . '/qrcode/';
        $hz = $scene.'_'.time().'.png';
        $url = 'https://'.$_SERVER['HTTP_HOST'].'/qrcode/'.$hz;
        file_put_contents($file.$hz,$result);
        if($type == 1){
            return $file.$hz;
        }
        return $url;
        #return $file.$hz;
    }
    /**发送模版消息
     * auth smallzz
     * @param array $param
     * @return bool|mixed
     */
    public function tplSend(array $param){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$this->getAccessToken();
        $data = '';
        $param['page']  = empty($param['page']) ? '' : $param['page'];
        switch ($param['type']){
            case 'auth_fail': //认证失败
                $param['page'] = 'pages/index/index';
                $data = $this->wechattpl->auth_fail($param);
                break;
            case 'wechat_fail': //微信认证失败
                $data = $this->wechattpl->wechat_fail($param);
                break;
            case 'auth_pass': //认证成功
                $param['page'] = 'pages/index/index';
                $data = $this->wechattpl->auth_pass($param);
                break;
            case 'buy': //购买推送
                $data = $this->wechattpl->buy($param);
                break;
            case 'bought': //被购买推送
                $data = $this->wechattpl->bought($param);
                break;
            case 'verify_pass': //审核通过
                $param['page'] = 'pages/index/index';
                $data = $this->wechattpl->verify_pass($param);
                break;
            case 'verify_fail': //审核未通过
                $param['page'] = 'pages/index/index';
                $data = $this->wechattpl->verify_fail($param);
                break;
        }
        $result=Curl::postJson($url,$data);
        return $result;

    }

    /** 获取用户信息
     * auth smallzz
     * @param string $openid
     * @return bool|mixed
     */
    public function getUnionid(string $openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->getAToken().'&openid='.$openid.'&lang=zh_CN';
        try{
            $result = Curl::getJson($url);
        }catch (Exception $exception){
            return false;
        }
        return $result;
    }
    /** 随机32位
     * auth smallzz
     * @param int $length
     * @return string
     */
    public function _getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    /** 生成签名
     * auth smallzz
     * @param string $arr
     * @return string
     */
    function _signature(array $array){
        sort($array);
        $str = "";
        foreach ($array as $k => $v) {
            $str .= $v;
        }
        $sign = sha1($str);
        return $sign;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2017-12-29
     *
     * @description 重新加载用户信息
     * @param string $wx_openid
     * @return bool|mixed
     */
    public function loadUserInfo(string $wx_openid)
    {
        $serviceToken=$this->getServerAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/user/info';
        $data = [
            'access_token' => $serviceToken,
            'openid' => $wx_openid,
            'lang' => 'zh_CN',
        ];
        $json = Curl::buildHttp($url, $data);
        $result = json_decode($json, true);
        $result['openid'] ? $userInfo = $result : $userInfo = false;
        return $userInfo;
    }

    protected function getServerAccessToken(){
        $data = [
            'appid' => $this->config['wx_appid'],
            'secret' => $this->config['wx_appsecret'],
            'grant_type' => 'client_credential',
        ];
        return $this->getCacheToken($this->config['is_wechat_key'],$data);
    }

    protected function getServerAccessToken2(){
        $data = [
            'appid' => $this->config['gzh_appid'],
            'secret' => $this->config['gzh_appsecret'],
            'grant_type' => 'client_credential',
        ];
        return $this->getCacheToken($this->config['is_wechat_key'],$data);
    }

    /** 反回正确的token
     * auth smallzz----bilibilihome
     * @param $key
     * @param $data
     * @return bool|mixed
     */
    private function getCacheToken($key,$data){
        $url = "https://api.weixin.qq.com/cgi-bin/token";
        //$tokenCache =$this->redis->get($key);
//        if($tokenCache == false){
//            $json = Curl::buildHttp($url, $data);
//            $result = json_decode($json, true);
//            $result['time'] = time();
//            $this->redis->set($key, json_encode($result));
//            $accessToken = $result['access_token'] ?? false;
//        }else{
//            $tokens = json_decode($tokenCache, true);
//            if ($tokens['time'] + 7100 < time()) {
//                $json = Curl::buildHttp($url, $data);
//                $result = json_decode($json, true);
//                $result['time'] = time();
//                $this->redis->set($key, json_encode($result));
//                $accessToken = $result['access_token'] ?? false;
//            }else{
//                $accessToken = $tokens['access_token'];
//            }
//        }
        $json = Curl::buildHttp($url, $data);
        $result = json_decode($json, true);
        $result['time'] = time();
        $accessToken = $result['access_token'] ?? false;
        return $accessToken;
    }

    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     * @return string
     */
    protected function jsonEncode(array $arr): string
    {
        if (count($arr) == 0) return "[]";
        $parts = [];
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length)) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::jsonEncode($value); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::jsonEncode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (!is_string($value) && is_numeric($value) && $value < 2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //All other things
                $parts [] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

}