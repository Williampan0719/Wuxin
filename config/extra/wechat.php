<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/22
 * Time: 上午10:51
 * @introduce
 */
//define('ENV_PRODUCT', is_file(__DIR__ . '/../.env.product'));
if (ENV_PRODUCT) {
    return [
        'wx_appid' => 'wx8b227f7d16bad291',
        'wx_appsecret' => '59013e2ef014445ddd337a0eca3ad4fc',
        'gzh_appid' => 'wx349ee50cf38f3943', //公众号
        'gzh_appsecret' => '16eba773eafd7438d282bc43ed83db37',
        'gzh_token' => 'pgyxwd',
        'small_appid' => 'wxc06f8e2831ab996a', //小程序
        'small_appsecret' => '8561bcd531f0bcbc4917ac588332bccb',

        'small_accesstoken'=>'small_accesstoken',
        #微信支付
        'qy_mchid' => '1496670792',
        'qy_key' => '30bbbcdbf69c89ee8a19115b628f4695',
        #企业支付
        'url' => 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',
        'sslcert_path' => __DIR__ . '/../../extend/service/payment/enterprise/apiclient_cert1.pem',
        'sslkey_path' => __DIR__ . '/../../extend/service/payment/enterprise/apiclient_key1.pem',
        #ip名单
        'server_ip' => '139.196.172.132',
        #回调
        'notify_url' => 'https://tutor.pgyxwd.com/api/wec/not',

        'app_url' => 'https://tutor.pgyxwd.com/',

        'is_wechat_key'=>'is_accessToken',
    ];
}else{
    return [
        'wx_appid' => 'wx8b227f7d16bad291',
        'wx_appsecret' => '59013e2ef014445ddd337a0eca3ad4fc',
        'gzh_appid' => 'wx349ee50cf38f3943', //公众号
        'gzh_appsecret' => '16eba773eafd7438d282bc43ed83db37',
        'gzh_token' => 'pgyxwd',
        'small_appid' => 'wxc06f8e2831ab996a',
        'small_appsecret' => '8561bcd531f0bcbc4917ac588332bccb',

        'small_accesstoken'=>'small_accesstoken',
        #微信支付
        'qy_mchid' => '1496670792',
        'qy_key' => '30bbbcdbf69c89ee8a19115b628f4695',
        #企业支付
        'url' => 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',
        'sslcert_path' => __DIR__ . '/../../extend/service/payment/enterprise/apiclient_cert1.pem',
        'sslkey_path' => __DIR__ . '/../../extend/service/payment/enterprise/apiclient_key1.pem',
        #ip名单
        'server_ip' => '139.196.172.132',
        #回调
        'notify_url' => 'https://tutortest.pgyxwd.com/api/wec/not',

        'app_url' => 'https://tutortest.pgyxwd.com/',

        'is_wechat_key'=>'is_accessToken',
    ];
}