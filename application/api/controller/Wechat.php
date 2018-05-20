<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/28
 * Time: 上午11:03
 */

namespace app\api\controller;


use app\api\logic\FormIdLogic;
use app\api\logic\WxpayLogic;
use app\api\model\Contacts;
use app\api\model\Order;
use app\api\model\Order_notify;
use app\api\model\Orderqy;
use app\api\model\Orderqy_notify;
use app\api\model\User;
use app\api\model\UserCer;
use extend\helper\Files;
use extend\service\payment\WeChatPayService;
use extend\service\WechatService;
use think\Db;
use think\Exception;

class Wechat extends BaseApi //暂时停用
{
    protected $wechatlogic = '';
    protected $usermo = null;
    protected $usercermo = null;
    protected $order = null;
    protected $orderqy = null;
    protected $orderqy_notify = null;
    protected $order_notify = null;

    function __construct($request = null)
    {
        //parent::__construct($request);
        $this->wechatlogic = new WxpayLogic();
        $this->usermo = new User();
        $this->order = new Order();
        $this->orderqy = new Orderqy();
        $this->usercermo = new UserCer();
        $this->order_notify = new Order_notify();
        $this->orderqy_notify = new Orderqy_notify();
    }

    /** 支付回调 (notbuy)  认证回调
     *  auth smallzz
     */
    public function notify(){
        ob_clean();
        $wcService = new WeChatPayService();

        $result = $wcService->notifyCallback();
        Files::WxLog('0416.txt',var_export($result,true));
        try{
            if ($result !== false) {
                Db::startTrans();
                $uid = $this->order->where(['order_sn'=>$result["out_trade_no"]])->value('uid');
                #先看是否已经退款了
                Files::WxLog($uid.'.txt',var_export($uid,true));    #记录日志
                if($this->order->where(['order_sn'=>$result["out_trade_no"],'uid'=>$uid])->value('is_refund')){
                    echo "SUCCESS";
                    exit;
                }
                $this->order_notify->save(['uid'=>$uid,'notify'=>json_encode($result)]);
                $param['order_sn'] = $result["out_trade_no"];//商户订单号
                $param['wx_order_sn'] = $result["transaction_id"];//微信支付订单号
                $param['amount'] = $result['total_fee'] / 100;//支付金额
                $flag = $this->wechatlogic->notifyRun($param);  #更新订单状态
                if ($flag) {
                    Db::commit();
                    #强校验身份
                    $resultInfo = $this->usermo->where(['id'=>$uid])->field('openid,body_name')->find();
                    $info = [
                        'name'=>$resultInfo['body_name'],
                        'openid'=>$resultInfo['openid'],
                        'uid'=>$uid,
                    ];
                    $result_sm = $this->wechatlogic->createQyPayId($info);     #使用强校验
                    Files::WxLog($uid.'.txt',var_export($result_sm,true));    #记录日志
                    $this->orderqy_notify->save(['uid'=>$uid,'notify'=>json_encode($result_sm)]);
                    if($result_sm['return_code'] == 'SUCCESS' && $result_sm['result_code'] == 'SUCCESS'){
                        #根据商户订单号处理订单状态
                        $orderqyInfo = [
                            'status'=>1,
                            'updatetime'=>date('Y-m-d H:i:s'),
                            'wx_order_sn'=>$result_sm['payment_no'] ?? '',
                        ];
                        $orderqyWhere = [
                            'id'=>$result_sm['order_id'],
                            'openid'=>$resultInfo['openid']
                        ];
                        $res_info = $this->orderqy->save($orderqyInfo,$orderqyWhere);
                        if($res_info){   #成功就处理认证状态
                            $usercermoInfo = [
                                'status'=>2,
                                'audittime'=>date('Y-m-d H:i:s')
                            ];
                            $usercermoWhere = [
                                'uid'=>$uid,
                                'type'=>1
                            ];
                            $this->usercermo->save($usercermoInfo,$usercermoWhere);   #实名
                            $this->order->save(['is_refund'=>time()],['order_sn'=>$result["out_trade_no"]]);
                        }
                        Db::commit();
                    }elseif($result_sm['result_code'] == 'FAIL' && $result_sm['err_code'] == 'NAME_MISMATCH'){     #强校验不通过，使用不校验
                        $orderqyInfo = [
                            'status'=>2,
                            'updatetime'=>date('Y-m-d H:i:s'),
                            'wx_order_sn'=>$result_sm['payment_no'] ?? '',
                        ];
                        $orderqyWhere = [
                            'id'=>$result_sm['order_id'],
                            'openid'=>$resultInfo['openid']
                        ];
                        $this->orderqy->save($orderqyInfo,$orderqyWhere);  #处理强校验失败

                        $result_nsm = $this->wechatlogic->createQyPayNId($info);     #不使用强校验
                        $this->orderqy_notify->save(['uid'=>$uid,'notify'=>json_encode($result_nsm)]);
                        Files::WxLog($uid.'.txt',var_export($result_nsm,true));    #记录日志
                        if($result_nsm['return_code'] == 'SUCCESS' && $result_nsm['result_code'] == 'SUCCESS'){
                            #根据商户订单号处理订单状态
                            $orderqyInfo = [
                                'status'=>1,
                                'updatetime'=>date('Y-m-d H:i:s'),
                                'wx_order_sn'=>$result_nsm['payment_no'] ?? '',
                            ];
                            $orderqyWhere = [
                                'id'=>$result_nsm['order_id'],
                                'openid'=>$resultInfo['openid']
                            ];
                            $res_info = $this->orderqy->save($orderqyInfo,$orderqyWhere);
                            if($res_info){   #成功就处理认证状态
                                $orderInfo = ['is_refund'=>time()];
                                $orderWhere = [
                                    'order_sn'=>$result["out_trade_no"]
                                ];
                                $this->order->save($orderInfo,$orderWhere);
                            }
                        }else{
                            $orderqyInfo = [
                                'status'=>2,
                                'updatetime'=>date('Y-m-d H:i:s'),
                                'wx_order_sn'=>$result_nsm['payment_no'] ?? '',
                            ];
                            $orderqyWhere = [
                                'id'=>$result_nsm['order_id'],
                                'openid'=>$resultInfo['openid']
                            ];
                            $res_info = $this->orderqy->save($orderqyInfo,$orderqyWhere);
                        }

                        $this->usercermo->save(['status'=>1,'audittime'=>date('Y-m-d H:i:s')],['uid'=>$uid,'type'=>1]);  #非实名
                        Db::commit();
                    }
                    #echo 'success';
                    echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";exit;
                } else {
                    Db::rollback();
                    #echo 'error';
                    echo "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>";exit;
                }
            } else {
                Db::rollback();
                #echo 'error';
                echo "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>";exit;
            }
        }catch (Exception $exception){
            #echo 'error';

            Files::CreateLog('huidiao222.txt',$exception->getMessage());
            echo "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>";
            exit;

        }

    }

    public function notifybuy(){
        ob_clean();
        $wcService = new WeChatPayService();
        $modelPush = new WechatService();
        $formIdSer = new FormIdLogic();
        $result = $wcService->notifyCallback();
        Db::startTrans();
        try{
            if ($result !== false) {

                #获取uid
                $replay = $this->order->where(['order_sn'=>$result["out_trade_no"]])->field('uid,buyid,openid')->find();
                if($this->order->where(['order_sn'=>$result["out_trade_no"],'uid'=>$replay['uid']])->value('status')){
                    echo "SUCCESS";
                    return "SUCCESS";
                }
                #$uid = $this->order->where(['order_sn'=>$result["out_trade_no"]])->value('uid');
                Files::WxLog('buy'.$replay['uid'].'.txt',var_export($replay['uid'],true));    #记录日志
                $this->order_notify->save(['uid'=>$replay['uid'],'notify'=>json_encode($result)]);  #记录db日志
                $param['order_sn'] = $result["out_trade_no"];//商户订单号
                $param['wx_order_sn'] = $result["transaction_id"];//微信支付订单号
                $param['amount'] = $result['total_fee'] / 100;//支付金额
                $flag = $this->wechatlogic->notifyRun($param);  #更新订单状态
                if($flag){
                    $contacts = new Contacts();
                    $datas['uid'] = $replay['uid'];
                    $datas['to_uid'] = $replay['buyid'];
                    $datas['money'] = $param['amount'];
                    $datas['create_at'] = date('Y-m-d H:i:s');
                    $contacts->save($datas);       #存关联表
                    $userInfo  = $this->usermo->finds($replay['buyid'],'body_name,role,sex,openid');
                    #发送购买模版消息
                    $chenghu = '';
                    $page = '';
                    if($userInfo['role'] == 1){
                        $page = 'pages/tutor/link/link';
                        $chenghu = '女士';
                        if($userInfo['sex'] == 1){
                            $chenghu = '先生';
                        }
                    }elseif($userInfo['role'] == 2){
                        $page = 'pages/parent/link/link';
                        $chenghu = '老师';
                    }
                    $pusharr = [
                        'type'=>'buy',
                        'openid'=>$replay['openid'],
                        'page'=>$page,
                        'form_id'=>$formIdSer->getFormId($replay['uid']),
                        'key1'=> $userInfo['body_name'].$chenghu.'的联系方式',
                        'key2'=>$param['amount'],
                        'key3'=>date('Y年m月d日 H时'),
                    ];
                    $r = $modelPush->tplSend($pusharr);
                    Files::CreateLog('audit.txt',var_export($r,true));
                    #发送被购买模版消息
                    $userInfo_  = $this->usermo->finds($replay['uid'],'body_name,role,sex,openid');
                    $chenghu_ = '';
                    if($userInfo_['role'] == 1){
                        $chenghu_ = '女士';
                        if($userInfo_['sex'] == 1){
                            $chenghu_ = '先生';
                        }
                    }elseif($userInfo_['role'] == 2){
                        $chenghu_ = '老师';
                    }
                    $pusharr_ = [
                        'type'=>'bought',
                        'openid'=>$userInfo['openid'],
                        'page'=>$page,
                        'form_id'=>$formIdSer->getFormId($replay['buyid']),
                        'key1'=> $userInfo_['body_name'].$chenghu_.'已取得您的联系方式',
                        'key2'=>date('Y年m月d日 H时'),
                    ];
                    $rs = $modelPush->tplSend($pusharr_);
                    Files::CreateLog('audit.txt',var_export($rs,true));
                    Db::commit();
                    Files::WxLog('buy'.$replay['uid'].'.txt','执行完毕');    #记录日志
                }
                echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";exit;
                #echo 'success';
            }
            echo "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>";exit;
            #echo 'error';
        }catch (Exception $exception){
            Files::CreateLog('buynotify.txt',$exception->getMessage());
            Db::rollback();
            echo "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>";exit;


        }
    }



}
