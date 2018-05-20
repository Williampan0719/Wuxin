<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/3
 * Time: 下午3:58
 */

namespace app\api\controller;


use app\api\model\Contacts;
use extend\service\RedisService;
use think\Db;
use think\Request;
class Config extends BaseApi
{
    protected $config = null;
    protected $contacts = null;
    protected $user = null;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->config = new \app\api\model\Config();
        $this->contacts = new Contacts();
        $this->user = new \app\api\model\User();
    }
    /**
     * @api {get} /api/conf/getaut 获取认证支付金额
     * @apiGroup conf
     * @apiName  getaut
     * @apiVersion 1.0.0
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/conf/getaut
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function getAut(){
        $fee = $this->config->getValue(['nameen'=>'AUDIT'],'value');
        return $this->ajaxSuccess(104,['value'=>$fee]);
    }
    /**
     * @api {get} /api/conf/getbuy 获取购买支付金额
     * @apiGroup conf
     * @apiName  getbuy
     * @apiVersion 1.0.0
     * @apiParam {string} uid  用户id
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/conf/getbuy
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function getBuy(){
        $param = $this->request->param();
        $uid = intval($param['uid']);
        #$count = $this->contacts->where(['uid'=>$uid])->count(); #判断是不是首次支付
        $count = Db::table('tut_contacts')->where(['uid'=>$uid])->count(); #判断是不是首次支付
        #判断角色
        $role = $this->user->where(['id'=>$uid])->value('role');
        $redis = new RedisService();
        $replay = $redis->get('config_param_value') ?? [];
        $info = json_decode($replay,true);
        switch ($role){
            case 1: #家长
                if($count > 0){
                    if(empty($info['JJ_STABLE_AMOUNT'])){
                        $amount = $this->getVal('JJ_STABLE_AMOUNT');
                    }else{
                        $amount = $info['JJ_STABLE_AMOUNT'];
                    }
                    return $this->ajaxSuccess(104,['value'=>$amount]);
                    #return $this->ajaxSuccess(104,['value'=>config('config')['buy_fee']]);
                }else{
                    if(empty($info['JJ_FIRST_AMOUNT'])){
                        $amount = $this->getVal('JJ_FIRST_AMOUNT');
                    }else{
                        $amount = $info['JJ_FIRST_AMOUNT'];
                    }
                    return $this->ajaxSuccess(104,['value'=>$amount]);
                    #return $this->ajaxSuccess(104,['value'=>config('config')['buy_first_fee']]);
                }
            case 2: #家教
                if($count > 0){
                    if(empty($info['JZ_STABLE_AMOUNT'])){
                        $amount = $this->getVal('JZ_STABLE_AMOUNT');
                    }else{
                        $amount = $info['JZ_STABLE_AMOUNT'];
                    }
                    return $this->ajaxSuccess(104,['value'=>$amount]);
                    #return $this->ajaxSuccess(104,['value'=>config('config')['buy_fee']]);
                }else{
                    if(empty($info['JZ_FIRST_AMOUNT'])){
                        $amount = $this->getVal('JZ_FIRST_AMOUNT');
                    }else{
                        $amount = $info['JZ_FIRST_AMOUNT'];
                    }
                    return $this->ajaxSuccess(104,['value'=>$amount]);
                    #return $this->ajaxSuccess(104,['value'=>config('config')['buy_first_fee']]);
                }
        }
    }
    private function getVal($name){
        return $this->config->where(['nameen'=>$name])->value('value');
    }
}