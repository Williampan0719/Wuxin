<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/22
 * Time: 上午9:06
 */

namespace app\backend\controller;


use app\backend\logic\TradingLogic;
use app\backend\model\AdminRole;
use extend\helper\Curl;
use extend\helper\Utils;
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

    public function testTd(){
        $url = 'https://api.tongdun.cn/bodyguard/apply/v4.1?partner_code=xrqb&partner_key=d422ae7d6daa41fa808c59422fb31f17&app_name=pgyqb_web';
        $param = $this->request->param();
        $data['id_number'] = $param['idcard'];
        $data['account_name'] = $param['name'];
        $data['biz_code'] = 'pgysmrzweb';
        $result = Curl::postUrlencoded($url, $data,true);
        dump($result);exit;
    }

    public function roleTest()
    {

    }
}