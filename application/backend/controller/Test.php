<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/22
 * Time: 上午9:06
 */

namespace app\backend\controller;


use app\backend\logic\TradingLogic;
use think\Loader;
use think\Request;

Loader::import('thirdpart.aop.AopClient');
Loader::import('thirdpart.aop.request.ZhimaCreditScoreBriefGetRequest');
//require_once __DIR__.'/../../../extend/thirdpart/aop/AopClient.php';
//require_once __DIR__.'/../../../extend/thirdpart/aop/request/ZhimaCreditScoreBriefGetRequest.php';

class Test extends BaseAdmin
{
    function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function index(){
        $param = $this->param;
        $new = new TradingLogic();
        $list = $new->BuyWater($param);
    }

    public function test(){
        $aop = new \AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2018050302624433';
        $aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEAzRNk5+5MV85rF8xwMvSz3fn+qeSrHQ1vORcIGGw9aUY0BwmroZuDyGmifuU4co9L2QHGbyzMHShFBsN+/5UEsO91SZvREKi5EbMAYdRcMKQ77FI4PibolvK9jz1CGX1PWCmitpmZ7F+vo6Xb2L/r1dU1gVubWyQmpPYJL1ccxSh3B5nwWm1Qd/ImfMRwl4k/LNV1W8OjuHTsuzd8CmQoQ8P4S3uF/Xa1rE4KKe4IZSj6T5ogNPhoQHqSACt/hirMoA3tfQWCGVsQ+CfYi9zINrpwWMGkVWPTv3tXP7gX0DPyEEb92dm1i7I86Isana7QZk9IabOR9DmPCFeFXZB3bwIDAQABAoIBAQCucPfdfcu8yR45oTIzdglmIZgpZhTT4rCgbGH7fF8EpK3u6p/vGP0RRiHuNQc+E3xePG9R9FYv2yhUJ4lo2Jaj9xzan0tIE21Ri+UgUmaVDa7XR90FqU0h0ZvDM0V9ryUGBmydTK0s7vvVhWkrx51RSEp6HbVZ4rdojMylU0Fqxomjdh1vYWRYw394TucTMwaJKqjS3764ivHnYFuKTKuSEhUw4k8sM3O+N9eseQUWj5w4hSWOjIhZWpEe33vAfU9oBZCjjxmqlukqVaUmA31FXIVcIg242isRXdbSdI8+6uPRdN1u8848twoTSp4KYd4pu96tr2bnENvSeekmRsF5AoGBAPEEeMwE6GuJTaU+kLU4VsNDmjGYmVhk/xLetXjyVvRy36gjVOQGns6/kzh5+lwy61FVFFy0+wOKUvObBOPmDqpIlFQXo2CmZxcEH0/pqTZPk7D6XEgFamYFIULmJuHVG5lMhuV1SjoTOc/CDuXkwbG+UeRrjlZ7KnPypGVvTb11AoGBANnS8xGxMFzdhg/vwJJ6YxM2knAhJ74rn/DbhPOS7+2J3JZ+zrvMJtGfhf7sYuNRbFFMsqNXpH5zgkelm8BeF64imvxiZ3mUMp6PA/nfpXtsx6+qQuknpEpBaatHkGwhLDyV4W4uGcw558TrR5ixq8jMUqyxiwGJLbtFpwV/ThDTAoGBANOOb1H2BOVPsj4X+wF3IIFpd5GNNx0mGdfAcV5mqfkMVFHMIZm+TeuAsYNjXmVLtM7BdYwiuCrVYha+vb4llD3YU4q07Q3LQrYrQijtG0C2/Rbdy97UEh7W5elJCFw2qXEYgVgsaikr8xTwbA9TismwG0X7OweiORNpqu/2kTB5AoGAIPNyZcjCIio4r4x7Oy1cc3mb6W56jqAWz97FW/tsZCiov/Nq3qnhbXCOWqPzyaN1mU7a7y7A6Ygbuy7oQs95EZSWkXtpX9D1X1sPJOuaIvOvuXkuKgsfJUSD8a8fv9u8z2V09q/uoyBXWUimSgkGjZerIgixmIfh7SR6QbCiZJUCgYAGR7FXlidjciXIsXd/jPq/llrHIUQpWEeKEAqFYKs4S/beUDm2CeRIgLeY/KBxsXAAudPmkFEV611ziOWlZQFGg+N66L6GTFYQe0G33icuPSHzhcVhA13OdTXTPbfKSoh6ycJuwcTozU/xx9uGZ9DXXBgBgPJg9xEOv0mSm3RQXw==';
        $aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArD++FhPAWFqYVRPqIpz34+srFuF7rtsn2HBl4cJNSbUmsQJarJpOqmGVkB5ChnkLO/7mUeTlFbm/ENYkNEU23FAYYACugpuuckXxr2Zu9nVdJFVbiRV7YrdhTppulsQsU4+QgjPvLV5RLabcCF6E/6iq8lTLwcQHHjBb4DTLfZO39SzWDQvm+9GvYYcTfH1w0AH8WUZ+oIql0qT4cXSgOffKqGnUAC4nuSXMMgDkrspNCHJtbEcAczGbDNh0wEZKANI8qjXsbrRRdt8k6XVQM7j8seBONx80FlWmGktV9eQr/jqfQsS3j1K6hct4rRQPYL0wH/zsN/eV6d9DXfk8xQIDAQAB';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \ZhimaCreditScoreBriefGetRequest();
        $request->setBizContent("{" .
            "\"transaction_id\":\"201512100936588040000000499999\"," .
            "\"product_code\":\"w1010100000000002733\"," .
            "\"cert_type\":\"IDENTITY_CARD\"," .
            "\"cert_no\":\"330227199407192015\"," .
            "\"name\":\"潘浩\"," .
            "\"admittance_score\":500," .
            "  }");
        $result = $aop->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
}