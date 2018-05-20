<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/12/22
 * Time: 下午2:55
 */

namespace extend\service\payment;


use extend\helper\Files;
use extend\service\payment\contracts\Payment;
use think\Exception;
use think\Log;
include_once(__DIR__."/../../thirdpart/wxpay/WxPayPubHelper/WxPayPubHelper.php");
class WeChatPayService implements Payment

{
    private $config;

    /**
     * WeChatPayService constructor.
     */
    public function __construct()
    {
        $this->config = config('wechat');
    }
    /**
     * @Author liyongchuan
     * @DateTime 2018-01-10
     *
     * @description 微信支付生成参数(在用)
     * @param array $data
     * @param null $callback
     * @return array
     * @throws Exception
     */
    public function payInfo(array $data = [], $type = 0)
    {
        $jsApi = new \JsApi_pub();
        $input = new \UnifiedOrder_pub();
        new \WxPayConf_pub($this->config);
        if($type == 1){   #区分回调 0认证流程，1购买流程
            $notifyUrl = url('api/wec/notbuy', '', 'html', true);
        }else{
            $notifyUrl = url('api/wec/not', '', 'html', true);
        }       #干，不知道你这样写是干毛，特么都传了回调还判断个鸡毛啊。。。。。。。。。。。。
        #Files::WxLog('testaa.txt',$notifyUrl);
        $input->setParameter("body", $data['order_desc']);//商品描述
        $input->setParameter('openid',$data['openid']);
        $input->setParameter("out_trade_no", $data['order_sn']);//商户订单号
        $input->setParameter("total_fee", $data['amount']*100);//总金额
        $input->setParameter("notify_url", $notifyUrl);//通知地址
        $input->setParameter("trade_type", "JSAPI");//交易类型

        try {
            $prepay_id = $input->getPrepayId();
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
        if ($prepay_id !== null) {
            $jsApi->setPrepayId($prepay_id);
            $response = [
                'response'=>$jsApi->getParameters(),
                'prepay_id'=>$prepay_id,
                'order_sn'=>base64_encode(Files::createStr(3).$data['order_sn'])
                ];
            return $response;
        } else {
            throw new Exception("错误：获取prepayid失败");
        }
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-01-03
     *
     * @description 微信支付回调函数
     * @param array $data
     * @param null $class(用)
     * @param null $callback
     * @return mixed
     */
    public function notifyCallback(array $data = [], $callback = null)
    {
        //使用通用通知接口
        $notify = new \Notify_pub();
        new \WxPayConf_pub($this->config);
        Files::WxLog('0419.txt',var_export($this->config,true));
        //存储微信的回调
        $xml = file_get_contents("php://input");
        $notify->saveData($xml);
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "fail");     #返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
        } else {
            $notify->setReturnParameter("return_code", "success");//设置返回码
        }
        if ($notify->checkSign() == TRUE || $notify->checkSign() == 1) {
            if ($notify->data["return_code"] == "fail") {
                Log::record("【微信支付】=======通信失败");
                return false;
            } else if ($notify->data["result_code"] == "fail") {
                Log::record("【微信支付】=======交易失败=====错误代码{$notify->data["err_code"]}=======错误代码描述{$notify->data["err_code_des"]}");
                return false;
            } else {

                return $notify->data;
            }
        }

    }
    public function notifyCallbacks(array $data = [], $callback = null)
    {
        //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/video/log.txt','liyongchuan'."\r\n",FILE_APPEND);
        //使用通用通知接口
        $notify = new \Notify_pub();
        new \WxPayConf_pub($this->config);
        //存储微信的回调
        $xml = file_get_contents("php://input");
        Files::CreateLog(date('Y-m-d').'.txt',$xml);
        #将xml转数组
        $data = $this->xmlToArray($xml);
        $notify->saveData($xml);
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "fail");//返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
        } else {

            $notify->setReturnParameter("return_code", "success");//设置返回码
        }
        if ($notify->checkSign() == TRUE || $notify->checkSign() == 1) {
            if ($data["return_code"] == "fail") {
                Log::record("【微信支付】=======通信失败");
                return false;
            } else if ($data["result_code"] == "fail") {
                Log::record("【微信支付】=======交易失败=====错误代码{$notify->data["err_code"]}=======错误代码描述{$notify->data["err_code_des"]}");
                return false;
            } else {
                #Files::CreateLog('zzhh1.txt',var_export($data,true));
                #return $notify->data;
                return $data;
            }
        }

    }
    public function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
}