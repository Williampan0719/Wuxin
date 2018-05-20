<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/03/26
 * Time: 15:54
 * @introduce
 */

namespace extend\service\payment;

use extend\helper\Files;
use extend\helper\Utils;

class WeChatEnterprisePayService
{
    protected $mch_appid;
    protected $mchid;
    protected $key;
    public function __construct()
    {
        $this->mch_appid = config('wechat.small_appid');
        $this->mchid = config('wechat.qy_mchid');
        $this->key = config('wechat.qy_key');
    }

    /**
     *    作用：生成签名
     */
    public function getSign($Obj)
    {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
        $String = $String . "&key=" . $this->key;
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
    }

    /**
     *    作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     *    作用：array转xml
     */
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /** 企业支付参数
     * auth smallzz
     * @param array $data
     * @return string
     */
    public function EnteropriseParams(array $data)
    {
        $params["mch_appid"] =        $this->mch_appid;    #商户账号appid
        $params['mchid'] =            $this->mchid;        #mchid
        $params['nonce_str'] =        Utils::randomString(32);
        $params['openid'] =           $data['openid'];
        $params['check_name'] =       $data['check_name'];          #FORCE_CHECK 校验  #不校验
        if($data['check_name'] == 'FORCE_CHECK'){
            $params['re_user_name'] =     $data['name'];       #当FORCE_CHECK存在就填写
        }
        $params['amount'] =           $data['amount']*100;
        $params['desc'] =             $data['order_desc'];
        $params['partner_trade_no']=  $data['order_sn'];
        $params['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'] ?? config('wechat')['service_ip'];#终端ip  $_SERVER['REMOTE_ADDR']??config('wechat')['service_ip'];
        #Files::CreateLog('parme.txt',var_export($params,true));
        $s = $this->getSign($params);
        $params["sign"] = $s;
        $xml=$this->arrayToXml($params);
        return $xml;
    }
}