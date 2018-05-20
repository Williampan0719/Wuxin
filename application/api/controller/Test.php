<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/1/10
 * Time: 下午5:07
 * @introduce
 */
namespace app\api\controller;

use app\api\logic\FormIdLogic;
use app\api\model\Contacts;
use app\api\model\User;
use extend\helper\Utils;
use extend\service\RedisService;
use extend\service\WechatService;
use think\Hook;
use think\Loader;
use think\Request;

Loader::import('thirdpart.wxpay.WxPayPubHelper.WxPayPubHelper');
Loader::import('thirdpart.wxpay.lib.WxPay');
//Loader::import('thirdpart.wxpay.lib.Testhh');
//require_once __DIR__.'/../../../extend/thirdpart/wxpay/lib/WxPay.php';

class Test
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function pan()
    {
        $wechat = new WechatService();
        $param = $this->request->param();
        return $wechat->getQrCode($param['scene'],$param['page'],430,0);
    }

    public function testWu()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function testFormId()
    {
//        $redis = new RedisService();
//        return $redis->lpop('ocJN_4jN7hgUaIrIkVC-fDhUX_SU');
        $param = $this->request->param();
        $form = new FormIdLogic();
        return $form->getFormId($param['uid']);
    }

}