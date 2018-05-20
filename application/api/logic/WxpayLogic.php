<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/26
 * Time: 上午11:35
 */

namespace app\api\logic;


use app\api\model\Order;
use app\api\model\Orderqy;
use app\common\logic\BaseLogic;
use extend\helper\Curl;
use extend\helper\Files;
use extend\helper\Utils;
use extend\service\payment\WeChatEnterprisePayService;
use extend\service\payment\WeChatPayService;
use think\Db;
use think\Exception;
use think\Validate;

class WxpayLogic extends BaseLogic
{
    protected $wechatpay = null;
    protected $sslcert = '';
    protected $sslkey = '';
    protected $rule =   [
        'order_desc'  => 'require',
        'openid'  => 'require',
        'order_sn'   => 'require',
        'amount'     => 'require',
    ];
    protected $msg = [
        'order_desc.require' => 10010,
        'openid.require'     => 10011,
        'order_sn.require'     => 10012,
        'amount.require'  => 10013,
    ];
    protected $order = null;
    protected $orderqy = null;
    function __construct()
    {
        parent::__construct();
        $this->wechatpay = new WeChatPayService();
        $this->sslcert   = config('wechat.sslcert_path');
        $this->sslkey    = config('wechat.sslkey_path');
        $this->order     = new Order();
        $this->orderqy   = new Orderqy();
    }

    /** 创建微信支付订单
     * auth smallzz
     * @param array $param
     * @return bool|string
     */
    public function createPayOrder(array $param){
        try{
            $orderInfo = [
                'uid'=>$param['uid'],
                'openid'=>$param['openid'],
                'type'=>$param['type'],
                'order_sn'=> Utils::createOrderSn('UTSEOR'),
                'amount'=>$param['amount'],
                'order_desc'=>$param['order_desc'],
            ];
            if(!empty($param['buyid'])){
                $orderInfo['buyid'] = $param['buyid'];
            }
            $this->order->save($orderInfo);
            $id = $this->order->getLastInsID();
        }catch (Exception $exception){
            return false;
        }
        return $id;
    }
    /** 创建微信支付
     * auth smallzz
     * @param array $param
     */
    public function createPay(array $param){
        try{
            $orderInfo = [
                'order_desc'=>$param['order_desc'],
                'openid'=>$param['openid'],
                'order_sn'=>$param['order_sn'],
                'amount'=>$param['amount'],
                #'notify_url'=>$param['notify_url'],
            ];
            $validate = new Validate($this->rule, $this->msg);
            if(!$validate->check($orderInfo)){
                return $this->ajaxError($validate->getError());
            }
            $type = 0;
            if(!empty($param['check_type'])){
                $type = 1;
            }
            #Files::WxLog('testaa.txt',$type);
            $response = $this->wechatpay->payInfo($orderInfo,$type);

        }catch (Exception $exception){
            return $this->ajaxError(10002);
        }
        return $this->ajaxSuccess(10001, ['sign' => $response['response'],'order_sn'=>$orderInfo['order_sn'],'fmd'=>$response['prepay_id']]);
    }

    /** 创建企业支付订单
     * auth smallzz
     * @param array $param
     * @return bool|int
     */
    private function createQyPayOrder(array $param){
        try{
            $orderInfo = [
                'uid' => $param['uid'],
                'openid' => $param['openid'],
                'order_sn' => $param['order_sn'],
                'order_desc' => $param['order_desc'],
                'amount' => $param['amount'],
                'type' => $param['type'],
            ];
            Db::name('orderqy')->insert($orderInfo);
            $id = Db::name('orderqy')->getLastInsID();
            #$this->orderqy->save($orderInfo);
            #$id = $this->orderqy->getLastInsID();
        }catch (Exception $exception){
            return false;
        }
        return $id;
    }
    /** 创建企业支付  强校验真实身份
     * auth smallzz
     * @param array $param
     * @return array|bool
     */
    public function createQyPayId(array $param){
        $param['order_sn'] = Utils::createOrderSn('ETNTOE');
        $param['amount'] = config('config')['cer_fee'];
        $param['type'] = 1;
        $param['order_desc'] ='退款实名认证';
        $res = $this->createQyPayOrder($param);
        if(!$res) return $this->ajaxError(10003);
        $url = config('wechat.url');
        $weep = new WeChatEnterprisePayService();
            $orderInfo = [
                'uid'=>$param['uid'],
                'name'=>$param['name'],
                'openid'=>$param['openid'],
                'amount'=>$param['amount'],
                'order_desc'=>'退款实名认证',
                'order_sn'=>$param['order_sn'],
                'check_name'=>'FORCE_CHECK',
            ];
        $data = $weep->EnteropriseParams($orderInfo);
        $xml = Curl::postXmlSSLCurl($data, $url, $this->sslcert,$this->sslkey);
        $response = Utils::parseMsgData($xml);
        $response['order_id'] = $res;
        return $response;
    }

    /**创建企业支付  不校验真实身份
     * auth smallzz
     * @param array $param
     * @return array|bool
     */
    public function createQyPayNId(array $param){
        $param['order_sn'] = Utils::createOrderSn('ETNTOE');
        $param['amount'] = config('config')['cer_fee'];
        $param['type'] = 0;
        $param['order_desc'] ='退款非实名认证';
        $res = $this->createQyPayOrder($param);
        if(!$res) return $this->ajaxError(10003);
        $url = config('wechat.url');
        $weep = new WeChatEnterprisePayService();
        $orderInfo = [
            'uid'=>$param['uid'],
            'name'=>$param['name'],
            'openid'=>$param['openid'],
            'amount'=>$param['amount'],
            'order_desc'=>'退款非实名认证',
            'order_sn'=>$param['order_sn'],
            'check_name'=>'NO_CHECK',
        ];
        $data = $weep->EnteropriseParams($orderInfo);
        $xml = Curl::postXmlSSLCurl($data, $url, $this->sslcert,$this->sslkey);
        $response = Utils::parseMsgData($xml);
        $response['order_id'] = $res;
        return $response;
    }

    /** 支付回调处理
     * auth smallzz
     * @param array $param
     */
    public function notifyRun(array $param){
        $orderinfo = [
            'status'=>1,
            'wx_order_sn'=>$param['wx_order_sn'],
            'updatetime'=>date('Y-m-d H:i:s')
        ];
        try{
            $this->order->save($orderinfo,['order_sn'=>$param['order_sn']]);
        }catch (Exception $exception){
            return false;
        }
        return true;
    }

}