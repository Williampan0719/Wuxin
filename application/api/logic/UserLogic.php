<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/22
 * Time: 下午4:33
 */

namespace app\api\logic;


use app\api\model\Complain;
use app\api\model\Config;
use app\api\model\Contacts;
use app\api\model\Learn;
use app\api\model\Order;
use app\api\model\Position;
use app\api\model\Statistic;
use app\api\model\Tutor;
use app\api\model\User;
use app\api\model\UserCer;
use app\api\model\UserCerInfo;
use app\api\model\UserImage;
use app\backend\model\Assistant;
use app\common\logic\BaseLogic;
use extend\helper\Files;
use extend\helper\Rsa;
use extend\helper\Utils;
use extend\service\Geohash;
use extend\service\message\CreateBlueService;
use extend\service\RedisService;
use extend\service\Txyt;
use extend\service\WechatService;
use think\Db;
use think\Exception;
use think\Loader;
use think\Validate;

Loader::import('thirdpart.aop.AopClient');
Loader::import('thirdpart.aop.request.ZhimaCreditScoreBriefGetRequest');

class UserLogic extends BaseLogic
{
    protected $wechatser = null;
    protected $user = null;
    protected $redis = null;
    protected $wxpay = null;
    protected $usercer = null;

    public function __construct()
    {
        parent::__construct();
        $this->wechatser = new WechatService();
        $this->user      = new User();
        $this->redis     = new RedisService();
        $this->wxpay     = new WxpayLogic();
        $this->usercer   = new UserCer();
    }

    /** 用户授权注册
     * auth smallzz
     * @param array $params
     * @return array
     */
    public function UserReg(array $param){
        $codeInfo = $this->wechatser->getSessionKey($param['code']);
        if ($codeInfo) {
            try{
                $encryptedData = $param['encryptedData'];
                $iv = $param['iv'];
                $result = $this->userDataSave($codeInfo['session_key'], $encryptedData, $iv);
                $info = json_decode($result, true);
                #保存用户基本信息
                $data['nickname'] =  $info['nickName'];

                $data['sex'] =       $info['gender'];
                $data['country'] =   $info['country'];
                $data['province'] =  $info['province'];
                $data['city'] =      $info['city'];
                $data['language'] =  $info['language'];
                #检查用户的信息存在么
                if ($this->user->checkOpenid($info['openId']) > 0) {
                    $this->user->save($data, ['openid' => $info['openId']]);
                }else {
                    $data['openid'] = $info['openId'];
                    $data['portrait'] = $info['avatarUrl'];
                    $data['addtime'] = date('Y-m-d H:i:s');
                    $data['logintime'] = date('Y-m-d H:i:s');
                    if (!empty($param['resource'])) {
                        $data['resource'] = $param['resource'];
                    }
                    $this->user->save($data);
                }
                $uid = $this->user->userDetailAll($info['openId'], 'id')['id'];
            }catch (Exception $exception){
                return $this->ajaxError(203);
            }
            return $this->ajaxSuccess(202, ['openid' => $info['openId'], 'uid' => $uid, 'nickname' => $info['nickName'],'sex'=>$info['gender'],'portrait'=>$info['avatarUrl']]);
        }
        return $this->ajaxError(203);
    }
    /** 授权获取手机号
     * auth smallzz
     * @param array $params
     * @return array
     */
    public function GetMobile(array $params)
    {
        $codeInfo = $this->wechatser->getSessionKey($params['code']);
        if ($codeInfo) {
            try {
                $encryptedData = $params['encryptedData'];
                $iv = $params['iv'];
                $res = $this->userDataSave($codeInfo['session_key'], $encryptedData, $iv);
                $infos = json_decode($res, true);
                $ass = new Assistant();
                $one = $ass->getOne(['status'=>1],'id','last_distribution asc'); //绑定助教
                #保存用户基本信息
                $user = new User();
                $user->save(['phone'=>$infos['purePhoneNumber'],'assistant'=>$one['id']],['openid'=>$params['openid']]);
                $ass->editOne(['last_distribution'=>time()],$one['id']);
                $result = $this->ajaxSuccess(104, ['phone'=>$infos['purePhoneNumber']],'授权成功');
            } catch (Exception $exception) {
                $result = $this->ajaxError(114);
            }
        } else {
            $result = $this->ajaxError(216);
        }
        return $result;
    }

    /** 验证码对比
     * auth smallzz
     * @param array $param
     */
    public function Contrastcode(array $param){
        try{
            $code = $this->redis->get('vercode'.$param['phone']);
            if(!empty($code)){
                $codes = json_decode($code,true);
                if($codes['time']+600 > time()){
                    if($param['code'] == $codes['code']){
                        return $this->ajaxSuccess(206, []);
                    }
                    return $this->ajaxError(207);
                }
                return $this->ajaxError(208);
            }
        }catch (Exception $exception){
            return $this->ajaxError(100);
        }
        return $this->ajaxError(208);
    }
    /**#家教/家长 个人资料信息填写
     * auth smallzz
     * @param array $param
     * @return array
     */
    public function UserInfo(array $param){
        $data['code'] = $param['code'];
        if($param['role'] == 2){   #家教
            $data['phone'] = $param['phone']??'';
            $data['wechat'] = $param['wechat']??'';
            $geo = new Geohash(); //我的住址
            $geo_hash = $geo->encode($param['lat'],$param['lng']);
            $tutor = new Tutor();
            $one = $tutor->getOne($param['uid']);
            if (!empty($one)) {
                $tutor->save(['lng' => $param['lng'], 'lat' => $param['lat'], 'geo_hash' => $geo_hash,'city'=>$param['city']],['uid'=>$param['uid']]);
            }else{
                $tutor->save(['uid'=>$param['uid'],'lng' => $param['lng'], 'lat' => $param['lat'], 'geo_hash' => $geo_hash,'city'=>$param['city']]);
            }
        }else{                     #家长
            $data['phone'] = $param['phone']??'';
            $data['wechat'] = $param['wechat']??'';
            $data['role_pos'] = $param['role_pos']??'';
            $learn = new Learn();
            $one = $learn->getOne($param['uid']);
            if (empty($one)) {
                $learn->save(['uid' => $param['uid']]);
            }
        }

        $validate = $this->validate($data,'UserValidate.UserInfo');
        if(true !== $validate){
            // 验证失败 输出错误信息
            return $this->ajaxError(0,[],$validate);
        }
        unset($data['code']);
        try{
            $code = $this->redis->get('vercode'.$param['phone']);
            if(!empty($code)){
                $codes = json_decode($code,true);
                if($codes['time']+600 > time()){
                    if($param['code'] == $codes['code']){
                        $this->user->save($data,['id'=>$param['uid']]);
                        return $this->ajaxSuccess(106, []);
                    }
                    return $this->ajaxError(207);
                }
                return $this->ajaxError(208);
            }
        }catch (Exception $exception){
            return $this->ajaxError(100,['info'=>$exception->getMessage()]);
        }
    }

    /** 授权获取用户手机号
     * auth smallzz
     * @param array $param
     * @return array
     */
    public function GetUserMobile(array $param){

        $codeInfo = $this->wechatser->getSessionKey($param['code']);
        if ($codeInfo) {
            try{
                $encryptedData = $param['encryptedData'];
                $iv = $param['iv'];
                $res = $this->userDataSave($codeInfo['session_key'], $encryptedData, $iv);
                $info = json_decode($res, true);
                #保存用户基本信息
                $data['mobile'] = $info['purePhoneNumber'];
                $validate = $this->validate($data,'UserValidate.UserMobile');
                if(true !== $validate){
                    // 验证失败 输出错误信息
                    return $this->ajaxError(0,[],$validate);
                }
                $this->user->save($data, ['openid' => $param['openid']]);
            }catch (Exception $exception){
                return $this->ajaxError(114);
            }
            return $this->ajaxSuccess(104, $data);
        }
        return $this->ajaxError(114);
    }

    /** 发送验证码
     * auth smallzz
     * @param array $params
     */
    public function Vercode(array $param){
        try{
            if(strlen($param['phone']) !== 11 || !is_numeric($param['phone'])){
                return $this->ajaxError(601);
            }
            $code = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
            $arrcode = json_encode(['code'=>$code,'time'=>time()]);
            $this->redis->set('vercode'.$param['phone'],$arrcode);
            $this->redis->expire('vercode'.$param['phone'],600);
            $createBlue = new CreateBlueService();
            $createBlue->setAccountType('cron');
            $createBlue->sendSMS($param['phone'],$code.'（5分钟内有效）该验证码用于您在家教汪平台完善资料。');

        }catch (Exception $exception){
            return $this->ajaxError(115);
        }
        return $this->ajaxSuccess(105, []);
    }


    /**用户数据保存
     * auth smallzz
     * @param string $sessionKey
     * @param string $encryptedData
     * @param string $iv
     * @return bool|int
     */
    public function userDataSave(string $sessionKey, string $encryptedData, string $iv)
    {
        #先解密
        $wechat = new WechatService();
        $resut = $wechat->decryptData($sessionKey, $encryptedData, $iv, $data);
        if ($resut !== false) {
            return $resut;
        }
        return false;
    }

    /** 实名认证
     * auth smallzz
     * @param array $param
     */
    public function Namecertification(array $param){ //停用
        #检查openid真实性
        //try{
            Files::CreateLog('panpay.txt',date('Y-m-d H:i:s',time()));
            $openidinfo = $this->user->userDetailAll($param['openid'],'id');
            #检查有没有认证过  || 检查认证是否成功
            $is_audit = $this->usercer->where(['uid'=>$openidinfo['id'],'type'=>1])->field('id,status,audittime')->find();
            if($is_audit['status'] == 2){
                return $this->ajaxError(217);
            }
            if(empty($openidinfo['id'])) return $this->ajaxError(211);
            $rule = ['name'=>'require|max:50','idcard'=>'require|max:18|min:15'];
            $msg = ['name.require'  => 903,'name.max'  => 904,'idcard.require'  => 905,'idcard.max'  => 906,'idcard.min'  => 906];
            $validate = new Validate($rule, $msg);
            if(!$validate->check(['name'=>$param['name'],'idcard'=>$param['idcard']])){
                return $this->ajaxError($validate->getError());
            }
            #认证费
            $fee = $this->getVal('AUDIT');
            #创建支付订单
            $dataInfo = [
                'uid'=>$openidinfo['id'],
                'openid'=>$param['openid'],
                'type'=>1,
                'amount'=>$fee,
                'order_desc'=>'认证费',
            ];
            $orderid = $this->wxpay->createPayOrder($dataInfo);  #TODO
            if(!$orderid) return $this->ajaxError(10003);

            #获取订单号
            $ordersn = (new Order())->getOrderSn($orderid);
            if(!$ordersn) return $this->ajaxError(1015);

            $payInfo = [
                'order_desc'=>'认证费',
                'openid'=>$param['openid'],
                'order_sn'=>$ordersn,
                'amount'=>$fee,
                #'notify_url'=>config('wechat')['notify_url'],
            ];
            $wxpay = new WxpayLogic();
            $res = $wxpay->createPay($payInfo);

            if($res['code'] == 10001){
                $this->user->save(['head_name'=>mb_substr($param['name'],0,1,'utf8'),'body_name'=>$param['name'],'idcard'=>$param['idcard']],['id'=>$openidinfo['id']]);
            }

            if($is_audit['id']){   #存在与否  存在
                $this->usercer->save(['status'=>0,'audittime'=>NULL],['uid'=>$openidinfo['id'],'type'=>1]);
            }else{
                $this->usercer->save(['uid'=>$openidinfo['id'],'type'=>1]);
            }
//        }catch (Exception $exception){
//            return $res;
//        }
        return $res;
    }

    /** 获取支付后的验证结果
     * auth smallzz
     * @param array $param
     */
    public function GetPayResult(array $param){ //停用

        $uid = intval($param['uid']);
        if(empty($uid)) {
            return $this->ajaxError(201);
        }
        $res = 0;
        sleep(rand(1,2));  #随机暂停
        $is_audit = $this->usercer->where(['uid'=>$uid,'type'=>1])->field('id,status,audittime')->find();
        if(!empty($is_audit['audittime'])){
            $res = $is_audit['status'];
        }
        #获取openid
        $result = $this->user->where(['id'=>$uid])->field('body_name,openid,role')->find();
        switch ($res){
            case 1:
                #发送认证失败的模版消息
                $form_id = $param['fmd'];$key1 =$result['body_name'];$key2 = date('Y年m月d日 H时');$key3 = '实名认证失败';
                $r = $this->wechatser->tplSend(['type'=>'auth_fail','openid'=>$result['openid'],'form_id'=>$form_id,'key1'=>$key1,'key2'=>$key2,'key3'=>$key3]);

                if($result['role'] == 1){    #家长认证失败
                    #家长状态修改
                    $this->user->save(['certime'=>1],['id'=>$uid]);
                }
                $message = '实名认证失败，不是实名';break;
            case 2:
                #发送认证成功的模版消息
                $form_id = $param['fmd'];$key1 = $result['body_name'];$key2 = date('Y年m月d日 H时');$key3 = '实名认证成功';
                $this->wechatser->tplSend(['type'=>'auth_pass','openid'=>$result['openid'],'form_id'=>$form_id,'key1'=>$key1,'key2'=>$key2,'key3'=>$key3]);
                if($result['role'] == 1){  #家长认证成功    光是实名还没用，还有微信呢
                    #家长状态修改
                    $this->user->save(['certime'=>0],['id'=>$uid]);
                }
                $message = '实名认证成功';break;
            default:
                $message = '您的实名认证未通过，请重新提交';
        }
        return $this->ajaxSuccess(104,['status'=>$res,'message'=>$message]);
    }

    /** 设置取消状态
     * auth smallzz
     * @param array $param
     * @return array
     */
    public function SetCancelStatus(array $param){
        $uid = intval($param['uid']);
        if(empty($uid)) return $this->ajaxError(201);
        if(empty($param['order_sn'])) return $this->ajaxError(218);
        //关闭微信订单
        $wx = new \WxPayCloseOrder();
        $wx->SetOut_trade_no($param['order_sn']);
        $result = \WxPayApi::closeOrder($wx);
        if (($result['result_code'] != 'SUCCESS') || ($result['return_code'] != 'SUCCESS')) {
            return $this->ajaxError(104, [], '微信订单关闭失败');
        }
        try{
            $order = new Order();
            $order->save(['status'=>-1],['order_sn'=>$param['order_sn']]);
        }catch (Exception $exception){
            return $this->ajaxError(220);
        }
        return $this->ajaxSuccess(219);
    }
    /**学历认证
     * auth smallzz
     * @param array $param
     * @return array
     */
    public function Diplomacertification(array $param){
        try{
            #文件操作
            $uid = intval($param['uid']);
            $usercerinfo = new UserCerInfo();
            $count = $this->usercer->where(['uid'=>$uid,'type'=>2,'status'=>2])->count();
            #if(!$usercerinfo->CheckUid($uid)) return $this->ajaxError(213);
            if($count) return $this->ajaxError(213);
            $imageName = date("YmdHis",time())."_".rand(100000,999999).'.png';
            $filename = ROOT_PATH ."public/aliocr/".$imageName;
            if (!move_uploaded_file($_FILES['file']['tmp_name'],$filename)) {
                return $this->ajaxError(210);
            }
            $txyt = new Txyt();
            $result = $txyt->generalocr(ROOT_PATH.'public/aliocr/'.$imageName);
            $usercer = new UserCer();
            if(!$usercer->where(['uid'=>$uid,'type'=>2])->count()){
                $res = $usercer->save(['uid'=>$uid,'type'=>2]);
            }else{
                $res = $usercer->save(['addtime'=>date('Y-m-d H:i:s'),'status'=>0],['uid'=>$uid,'type'=>2]);
            }
            //图片保存至oss
            $a = Utils::picOssUp($imageName,$filename,'aliocr');
            if (empty($a)) {
                return $this->ajaxError(101,[],'上传失败');
            }else{
                $userimage = new UserImage();
                $userimage->delImage(['uid'=>$uid]); //删除原有照片
                $userimage->save(['uid'=>$uid,'url'=>$a]);
            }
            //认证状态
            $auth = $this->user->finds($param['uid'],'wechat_status');
            $card = $usercer->getOne(['uid'=>$uid,'type'=>1,'status'=>1],'id');
            if ($auth['wechat_status'] != 1 && empty($card)) {
                $this->user->save(['certime'=>0],['id'=>$uid]);
            }
            if(!$result) {
                $one_info = $usercerinfo->getOne(['uid'=>$uid],'uid');
                if (!empty($one_info)) {
                    $usercerinfo->save(['professional'=>$param['professional']],['uid'=>$uid]);
                }else {
                    $usercerinfo->save(['uid'=>$uid,'professional'=>$param['professional']]);
                }
                return $this->ajaxSuccess(209,[]);
            }
            $datainfo = [
                'uid'=>$uid,
                "name"=> $result['name'],
                "idcard"=> $result['idcard'],
                "school"=> $result['school'],
                "diploma"=> $result['diploma'],
                "professional"=> $param['professional'],
                "addschool"=> $result['addschool'],
                "validatime"=> $result['validatime'],
            ];
            $one_info = $usercerinfo->getOne(['uid'=>$uid],'uid');
            if (!empty($one_info)) {
                $usercerinfo->save($datainfo,['uid'=>$uid]);
            }else {
                $usercerinfo->save($datainfo);
            }
            if(!$res) return $this->ajaxError(116);
        }catch (Exception $exception){
            return $this->ajaxError(210,[],'报错啦');
        }
        return $this->ajaxSuccess(209,[]);
    }

    /** 我的资料
     * auth smallzz
     * @param array $param
     * @return array
     */
    public function MyDetail(array $param){
        $usercer = new UserCer();
        try{
            $uid = intval($param['uid']);
            $info = $this->user->finds($uid,'*');
            if(empty($info)) return $this->ajaxError(201);
            #检查验证状态

            $userinfo = [];
            $userinfo['sex'] = $info['sex'];
            $userinfo['portrait'] = $info['portrait'];
            $userinfo['phone'] = $info['phone'];
            $userinfo['name'] = $info['body_name'] ? $info['body_name'] : '未填写';
            $userinfo['idcard'] = $info['idcard'] ? $info['idcard'] : '未填写';
            $userinfo['wechat'] = $info['wechat_status'] == 0 ? $info['wechat'] : $info['wechat'];
            $userinfo['wechat_status'] = $info['wechat_status'] > 1 ? 2 : (empty($info['wechat']) ? -1 :$info['wechat_status']);

            $userinfo['school'] = '未填写';
            $userinfo['diploma'] = '';
            $userinfo['professional'] = '未填写';
            switch ($info['role']){
                case 1:
                    $where = ['uid'=>$uid,'type'=>1];  #家长只有身份认证
                    $cerinfo = $usercer->where($where)->find();
                    if(empty($cerinfo)){  #未认证
                        $userinfo['phone_status'] = -1;
                        $userinfo['name_status'] = -1;
                        $userinfo['id_status'] = -1;
                    }else{
                        $userinfo['phone_status'] = $cerinfo['status'];
                        $userinfo['name_status'] = $cerinfo['status'];
                        $userinfo['id_status'] = $cerinfo['status'];
                    }
                    break;
                case 2:
                    $cerinfosf = $usercer->where(['uid'=>$uid,'type'=>1])->find();
                    if(empty($cerinfosf)){  #未认证
                        $userinfo['id_status'] = -1;
                    }else{
                        $userinfo['id_status'] = $cerinfosf['status'];
                    }
                    $cerinfoxl = $usercer->where(['uid'=>$uid,'type'=>2])->find();
                    if(empty($cerinfoxl)){  #未认证
                        $userinfo['diploma_status'] = -1;
                    }elseif($cerinfoxl['status'] == 1){
                        $userinfo['diploma_status'] = 1;
                    }elseif($cerinfoxl['status'] == 0){  #审核中
                        $ucinfo = new UserCerInfo();
                        $ucdate = $ucinfo->where(['uid'=>$uid])->find();
                        $userinfo['school'] = $ucdate['school'] ?? '';
                        $userinfo['diploma'] = $ucdate['diploma'] ?? '';
                        $userinfo['professional'] = $ucdate['professional'] ?? '';
                        $userinfo['diploma_status'] = 0;
                    }else{
                        $ucinfo = new UserCerInfo();
                        $ucdate = $ucinfo->where(['uid'=>$uid])->find();
                        $userinfo['school'] = $ucdate['school'] ?? '';
                        $userinfo['diploma'] = $ucdate['diploma'] ?? '';
                        $userinfo['professional'] = $ucdate['professional'] ?? '';
                        $userinfo['diploma_status'] = 2;
                    }
                    break;
                default:
                    return $this->ajaxError(211);
            }
        }catch (Exception $exception){
            return $this->ajaxError(114);
        }
        return $this->ajaxSuccess(104,['data'=>$userinfo]);
    }

    /** 选择角色
     * auth smallzz
     * @param array $param
     * @return array
     */
    public function ChooseRole(array $param){
        try{
            $role = intval($param['role']);
            $uid = intval($param['uid']);
            $this->user->save(['role'=>$role],['id'=>$uid]);
            $geo = new Geohash(); //我的住址
            $geo_hash = $geo->encode($param['lat'],$param['lng']);
            if ($role == 1) { //家长
                $learn = new Learn();
                $learn->save(['uid'=>$param['uid'],'lng' => $param['lng'], 'lat' => $param['lat'], 'geo_hash' => $geo_hash, 'city'=>$param['city']]);
            }elseif ($role == 2) {
                $tutor = new Tutor();
                $tutor->save(['uid'=>$param['uid'],'lng' => $param['lng'], 'lat' => $param['lat'], 'geo_hash' => $geo_hash, 'city'=>$param['city']]);
            }
            $position = new Position();
            $position->save(['uid'=>$param['uid'],'lng' => $param['lng'], 'lat' => $param['lat'], 'geo_hash' => $geo_hash, 'city'=>$param['city'],'status'=>1]);

            //统计注册
            $province = Utils::getProvinceByCity($param['city']); //获取省份
            $statistic = new Statistic();
            $statistic->add(['type'=>1,'uid'=>$param['uid'],'role'=>$role,'sex'=>$param['sex'],'province'=>$province['province'],'city'=>$province['city'],'addtime'=>time(),'add_date'=>date('Y-m-d')]);
        }catch (Exception $exception){
            $this->ajaxError(111);
        }
        return $this->ajaxSuccess(101);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-03
     *
     * @description 新版检验走到哪一步
     * @param array $param
     * @return array
     */
    public function CheckProcess(array $param){

        #检查个人资料
        Files::CreateLog('test.txt',$_SERVER['HTTP_USER_AGENT']);
        $replay = $this->user->finds(intval($param['uid']),'*');
        $device = '';
        if (strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')) {
            $device = 'iphone';
        }elseif (strpos($_SERVER['HTTP_USER_AGENT'],'Android')) {
            $device = 'android';
        }
        $p = ['device'=>$device,'logintime'=>date('Y-m-d H:i:s')]; //添加设备
        $this->user->save($p,['id'=>$param['uid']]);
        switch ($replay['role']){  #1家长，2家教
            case 0:
                $data = [
                    'role'=>0,
                    'info'=>'USER_NOT_ROLE',
                    'desc'=>'未选择角色',
                ];
                return $this->ajaxError(211,['data'=>$data]);
                break;
            case 1:
                #检查手机号，微信号
                if(empty($replay['phone'])){
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_NOT_PHONE',
                        'desc'=>'您尚未绑定手机号',
                    ];
                    return $this->ajaxError(214,['data'=>$data]);
                }
                if(empty($replay['qrcode'])){
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_NOT_WECHAT',
                        'desc'=>'您尚未上传微信二维码',
                    ];
                    return $this->ajaxError(214,['data'=>$data]);
                }
                #检查认证环节  身份认证
                $idauth = $this->usercer->where(['uid'=>$param['uid'],'type'=>1])->value('status');

                if(empty($idauth)){
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_NOT_IDAUTH',
                        'desc'=>'您尚未进行实名认证',
                    ];
                    return $this->ajaxError(215,['data'=>$data]);
                }else{
                    if($idauth !== 2){
                        $data = [
                            'role'=>$replay['role'],
                            'info'=>'USER_NOT_IDAUTH',
                            'desc'=>'实名认证不成功',
                        ];
                        return $this->ajaxError(215,['data'=>$data]);
                    }
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_GOTO_INDEX',
                        'desc'=>'认证完毕',
                    ];
                    return $this->ajaxError(221,['data'=>$data]);
                }
                break;

            case 2:
                #检查手机号，微信号
                if(empty($replay['phone'])){
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_NOT_PHONE',
                        'desc'=>'您尚未绑定手机号',
                    ];
                    return $this->ajaxError(214,['data'=>$data]);
                }
                if(empty($replay['qrcode'])){
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_NOT_WECHAT',
                        'desc'=>'您尚未上传微信二维码',
                    ];
                    return $this->ajaxError(214,['data'=>$data]);
                }
                #检查认证环节  身份认证
                $idauth = $this->usercer->where(['uid'=>$param['uid'],'type'=>1])->value('status');
                if(empty($idauth)){
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_NOT_IDAUTH',
                        'desc'=>'您尚未进行实名认证',
                    ];
                    return $this->ajaxError(215,['data'=>$data]);
                }else{
                    if($idauth !== 2){
                        $data = [
                            'role'=>$replay['role'],
                            'info'=>'USER_NOT_IDAUTH',
                            'desc'=>'实名认证不成功',
                        ];
                        return $this->ajaxError(215,['data'=>$data]);
                    }
                }
                #检查认证环节  学历认证
                $idauth = $this->usercer->where(['uid'=>$param['uid'],'type'=>2,'status'=>2])->count();
                if(empty($idauth)){
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_NOT_SCHOOL',
                        'desc'=>'您尚未进行学历认证',
                    ];
                    return $this->ajaxError(215,['data'=>$data]);
                }else{
                    $data = [
                        'role'=>$replay['role'],
                        'info'=>'USER_GOTO_INDEX',
                        'desc'=>'认证完毕',
                    ];
                    return $this->ajaxError(221,['data'=>$data]);
                }
                break;
            default:
                break;
                #return $this->ajaxSuccess(104);
        }
        return $this->ajaxSuccess(104);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-28
     *
     * @description 修改预约状态
     * @param array $param
     * @return array
     */
    public function editOrderStatus(array $param)
    {
        $one = $this->user->finds($param['uid'],'role');
        if ($one['role'] == 1) {
            $learn = new Learn();
            $find = $learn->getOne($param['uid']);
            if ($find) {
                $learn->editLearn(['is_order'=>$param['is_order']],$param['uid']);
            }else{
                $learn->addLearn(['is_order'=>$param['is_order'],'uid'=>$param['uid']]);
            }
        }elseif ($one['role'] == 2) {
            $tutor = new Tutor();
            $find = $tutor->getOne($param['uid']);
            if ($find) {
                $tutor->editTutor(['is_order'=>$param['is_order']],$param['uid']);
            }else{
                $tutor->addTutor(['is_order'=>$param['is_order'],'uid'=>$param['uid']]);
            }
        }
        return $this->ajaxSuccess(104,[],'预约设置成功');
    }

    /** 购买谁
     * auth smallzz
     */
    public function BuyWho(array $param){ //停用
        $uid = intval($param['uid']);
        $buyid = intval($param['buyid']);
        $openid = $param['openid'] ?? '';
        $count = Db::table('tut_contacts')->where(['uid'=>$uid])->count(); #判断是不是首次支付
        $fee = $this->getAmount($uid);
        try{
            if(empty($uid) || empty($buyid) || empty($openid)){
                return $this->ajaxError(222);
            }
            #首要任务当然是给他搞个订单号啊
            #创建支付订单
            $dataInfo = [
                'uid'=>$uid,
                'openid'=>$openid,
                'buyid'=>$buyid,
                'type'=>2,
                'amount'=>$fee,
                'order_desc'=>'自然消费',
            ];
            $orderid = $this->wxpay->createPayOrder($dataInfo);  #TODO
            if(!$orderid) return $this->ajaxError(10003);
            #获取订单号
            $ordersn = (new Order())->getOrderSn($orderid);
            if(!$ordersn) return $this->ajaxError(1015);

            $payInfo = [
                'order_desc'=>'自然消费',
                'openid'=>$openid,
                'order_sn'=>$ordersn,
                'amount'=>$fee,
                'check_type'=>1,
            ];
            $wxpay = new WxpayLogic();
            $res = $wxpay->createPay($payInfo);

        }catch (Exception $exception){
            return false;
        }
        return $res;
    }
    private function getAmount(int $uid){
        $redis = new RedisService();
        $replay = $redis->get('config_param_value') ?? [];
        $info = json_decode($replay,true);
        $role = $this->user->where(['id'=>$uid])->value('role');
        $count = Db::table('tut_contacts')->where(['uid'=>$uid])->count(); #判断是不是首次支付
        switch ($role){
            case 1: #家长
                if($count > 0){
                    if(empty($info['JJ_STABLE_AMOUNT'])){
                        $amount = $this->getVal('JJ_STABLE_AMOUNT');
                    }else{
                        $amount = $info['JJ_STABLE_AMOUNT'];
                    }
                    return $amount;
                }else{
                    if(empty($info['JJ_FIRST_AMOUNT'])){
                        $amount = $this->getVal('JJ_FIRST_AMOUNT');
                    }else{
                        $amount = $info['JJ_FIRST_AMOUNT'];
                    }
                    return $amount;
                }
            case 2: #家教
                if($count > 0){
                    if(empty($info['JZ_STABLE_AMOUNT'])){
                        $amount = $this->getVal('JZ_STABLE_AMOUNT');
                    }else{
                        $amount = $info['JZ_STABLE_AMOUNT'];
                    }
                    return $amount;
                }else{
                    if(empty($info['JZ_FIRST_AMOUNT'])){
                        $amount = $this->getVal('JZ_FIRST_AMOUNT');
                    }else{
                        $amount = $info['JZ_FIRST_AMOUNT'];
                    }
                    return $amount;
                }
        }
    }

    private function getVal($name){
        $config = new Config();
        return $config->where(['nameen'=>$name])->value('value');
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 芝麻分认证
     * @param array $param
     * @return array
     */
    public function zhimaAuth(array $param)
    {
//        $aop = new \AopClient();
//        $aop->gatewayUrl = config('gatewayUrl');
//        $aop->appId = config('appid');
//        $aop->rsaPrivateKey = config('rsaPrivateKey');
//        $aop->alipayrsaPublicKey = config('alipayrsaPublicKey');
//        $aop->apiVersion = config('apiVersion');
//        $aop->signType = config('signType');
//        $aop->postCharset = config('postCharset');
//        $aop->format= config('format');
//        $request = new \ZhimaCreditScoreBriefGetRequest();
//        $transcation_id = date('Ymd').time().rand(1000,9999);
//        $request->setBizContent("{" .
//            "\"transaction_id\":".$transcation_id."," .
//            "\"product_code\":\"w1010100000000002733\"," .
//            "\"cert_type\":\"IDENTITY_CARD\"," .
//            "\"cert_no\":".$param['idcard']."," .
//            "\"name\":".$param['name']."," .
//            "\"admittance_score\":500," .
//            "  }");
//        $result = $aop->execute($request);
//        //return $result;
//
//        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
//        $resultCode = $result->$responseNode->code;
//        if(!empty($resultCode)&&$resultCode == 10000){
//            $cer = new UserCer();
//            $cer->add(['uid'=>$param['id'],'type'=>1,'status'=>2,'audittime'=>date('Y-m-d H:i:s'),'addtime'=>date('Y-m-d H:i:s')]);
//            return $this->ajaxSuccess(104);
//        } else {
//            $cer = new UserCer();
//            $cer->add(['uid'=>$param['id'],'type'=>1,'status'=>1,'addtime'=>date('Y-m-d H:i:s')]);
//            return $this->ajaxError(114);
//        }
        if (checkdate(substr($param['idcard'],10,2),substr($param['idcard'],12,2),substr($param['idcard'],6,4)) == false) {
            return $this->ajaxError(111,[],'身份证格式不正确');
        }
        //暂时只验证格式
        $this->user->save(['head_name'=>mb_substr($param['name'],0,1,'utf8'),'body_name'=>$param['name'],'idcard'=>$param['idcard']],['id'=>$param['uid']]);
        $cer = new UserCer();
        $cer->add(['uid'=>$param['uid'],'type'=>1,'status'=>2,'audittime'=>date('Y-m-d H:i:s'),'addtime'=>date('Y-m-d H:i:s')]);
        return $this->ajaxSuccess(101,[],'认证成功');
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 上传二维码
     * @param array $param
     * @return array
     */
    public function uploadQrcode(array $param)
    {
        $wechat = $this->user->finds($param['uid'],'wechat,role');
        if ($wechat['wechat'] != $param['wechat']) {
            $cer = new UserCer();
            $count = $cer->getCount(['uid'=>$param['uid'],'status'=>1],'id');
            if ($count > 0) {
                $time = 1;
            }else{
                $time = 0;
            }
            $this->user->save(['wechat' => $param['wechat'], 'wechat_status' => 0,'certime'=>$time], ['id' => $param['uid']]);
        }
        if (!empty($_FILES)) {
            $a = Utils::uploadPic($_FILES,'qrcode');
            if (!empty($a)) {
                $this->user->save(['qrcode'=>$a,'qrcode_status'=>0],['id'=>$param['uid']]);

                return $this->ajaxSuccess(101,['list'=>$a]);
            }
            return $this->ajaxError(111);
        }
        return $this->ajaxSuccess(101);
    }

    /** 修改头像
     * auth smallzz
     * @param array $param
     * @return array
     */
    public function upHead(array $param){
        #文件操作
//        $uid = intval($param['uid']);
//        $imageName = date("YmdHis",time())."_".rand(100000,999999).'.png';
//        $filename = ROOT_PATH ."public/image/".$imageName;
//        if (!move_uploaded_file($_FILES['file']['tmp_name'],$filename)) {
//            return $this->ajaxError(210);
//        }
//        $usercer = new UserCer();
//        $res = $usercer->save(['uid'=>$uid,'type'=>2]);
//        $this->user->save(['portrait'=>'https://'.$_SERVER['HTTP_HOST'].'/image/'.$imageName],['id'=>$uid]);
//        if(!$res) return $this->ajaxError(116);
        if (!empty($_FILES)) {
            $a = Utils::uploadPic($_FILES,'portrait');
            if (!empty($a)) {
                $this->user->save(['portrait'=>config('oss.outer_host').$a],['id'=>$param['uid']]);

                return $this->ajaxSuccess(101,['list'=>$a]);
            }
            return $this->ajaxError(111);
        }
        return $this->ajaxSuccess(102);
    }

    /** 修改微信号
     * auth smallzz
     * @param array $param
     * @return array
     */
    public function upWechat(array $param){
        try{
            $uid = intval($param['uid']);
            //todo
            $this->user->save(['wechat'=>$param['wechat'],'wechat_status'=>0],['id'=>$uid]);
        }catch (Exception $exception){
            return $this->ajaxError(112);
        }
        return $this->ajaxSuccess(102);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-12
     *
     * @description 获取哪一步审核失败
     * @param array $param
     * @return array
     */
    public function getFailStep(array $param){
        $cer = new UserCer();
        $identify = $cer->getOne(['uid'=>$param['uid'],'type'=>1],'status');
        if (!empty($identify['status']) && $identify['status'] == 1) {
            return $this->ajaxSuccess(104,['type'=>1],'您的身份审核失败,请重新提交认证'); //身份审核失败
        }else{
            $user = new User();
            $role = $user->finds($param['uid'],'role,wechat_status');
            if ($role['role'] == 1) { //家长
                if ($role['wechat_status'] == 1) {
                    return $this->ajaxSuccess(104,['type'=>3],'您的微信审核失败,请重新提交认证');
                }
                return $this->ajaxError(114);
            }elseif ($role['role'] == 2) {
                $edu = $cer->getOne(['uid'=>$param['uid'],'type'=>2],'status');
                if (!empty($edu['status']) && $edu['status'] == 1) {
                    return $this->ajaxSuccess(104,['type'=>2],'您的学历审核失败,请重新提交认证'); //学历
                }elseif ($role['wechat_status'] == 1) {
                    return $this->ajaxSuccess(104,['type'=>3],'您的微信审核失败,请重新提交认证'); //微信
                }
                return $this->ajaxError(114);
            }
        }
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-08
     *
     * @description 保存投诉
     * @param array $param
     * @return array
     */
    public function saveComplain(array $param)
    {
        $imageName = '';
        if (!empty($_FILES)) {
            $imageName = date("YmdHis", time()) . "_" . rand(100000, 999999) . '.png';
            $filename = ROOT_PATH . "public/complain/" . $imageName;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $filename)) {
                return $this->ajaxError(210);
            }
        }
        $info = [
            'uid' => $param['uid'],
            'to_uid' =>$param['to_uid'],
            'type' => $param['type'],
            'remark' => $param['remark'] ?? '',
            'img' => !empty($_FILES) ? 'complain/'.$imageName : '',
        ];
        $complain = new Complain();
        $result = $complain->saveComplain($info);
        if ($result == 1) {
            return $this->ajaxSuccess(101);
        }
        return $this->ajaxError(111);
    }

    /** 获取投诉列表
     * auth smallzz
     * @return array
     */
    public function getComplaintsList(){
        $config = new Config();
        $list = $config->where(['type'=>3])->field('id,namecn')->select();
        return $this->ajaxSuccess(104,$list);
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-02-05
     *
     * @description 版本信息
     * @param array $params
     * @return array
     */
    public function versionInfo(array $params)
    {
        try{
            if(empty($params)) {
                return $this->ajaxError(201, [], '版本信息不能为空');
            }
            unset($params['token']);
            $info = json_encode($params);
            $rsa = new Rsa();
            $versionHash = $rsa->pubencrypt($info);
            $result = $this->ajaxSuccess(200, ['version_hash' => $versionHash], '获取成功');
        }catch (Exception $exception){

            $result = $this->ajaxError(201, [], '系统异常');
        }

        return $result;
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-02-05
     *
     * @description 版本信息解密
     * @param array $params
     * @return array
     */
    public function deVersionInfo(array $params)
    {
        try{
            $rsa = new Rsa();
            $versionHash = $rsa->pridecrypt($params['hash']);
            $result = ['version_hash' => json_decode($versionHash,true)];
        }catch (Exception $exception){

            $result = $this->ajaxError(201, [], '系统异常');
        }

        return $result;
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 分享
     * @param array $param
     * @return array
     */
    public function getShare(array $param)
    {
        $user = new User();
        $one = $user->finds($param['uid'],'last_share,rest_chance,role,sex');
        //统计分享
        if ($one['role'] == 1) {
            $learn = new Learn();
            $city = $learn->getValue($param['uid'],'city');
        }else{
            $tutor = new Tutor();
            $city = $tutor->getValue($param['uid'],'city');
        }
        $province = Utils::getProvinceByCity($city); //获取省份
        $statistic = new Statistic();
        $statistic->add(['type'=>5,'uid'=>$param['uid'],'role'=>$one['role'],'sex'=>$one['sex'],'province'=>$province['province'],'city'=>$province['city'],'addtime'=>time(),'add_date'=>date('Y-m-d')]);

        if ($one['last_share'] < date('Y-m-d')) {
            if ($one['rest_chance'] >= 5) {
                $user->save(['last_share'=>date('Y-m-d H:i:s'),'rest_chance'=>10],['id'=>$param['uid']]); //更新最近分享时间与机会
            }else{
                $c = $one['rest_chance'] + 5;
                $user->save(['last_share'=>date('Y-m-d H:i:s'),'rest_chance'=>$c],['id'=>$param['uid']]); //更新最近分享时间与机会
            }
            return $this->ajaxSuccess(102,[],'分享成功');
        }
        return $this->ajaxError(112,[],'分享成功');
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-04
     *
     * @description 添加解锁关系
     * @param array $param
     * @return array
     */
    public function addContact(array $param)
    {
        try {
            $user = new User();
            $contact = new Contacts();
            $exist = $contact->getTotal2(['uid'=>$param['uid'],'to_uid'=>$param['to_uid']]);
            if (!empty($exist)) {
                return $this->ajaxSuccess(20002,[],'您已解锁此用户');
            }
            $exist2 = $contact->getTotal2(['uid'=>$param['to_uid'],'to_uid'=>$param['uid']]);
            if (!empty($exist2)) {
                return $this->ajaxSuccess(20002,[],'您已被此用户解锁');
            }
            $one = $user->finds($param['uid'], 'rest_chance,role,sex,last_share');
            if ($one['rest_chance'] < 1) {
                if ($one['last_share'] > date('Y-m-d')) {
                    return $this->ajaxError(20013);
                }
                return $this->ajaxError(20012);
            }
            $two = $user->finds($param['to_uid'], 'rest_chance,role,sex');
            $count = $contact->getTotal2(['create_at'=>['gt',date('Y-m-d')],'to_uid'=>$param['to_uid']]);
            if ($count >= 10) {
                $role = $two['role'] == 1 ? '此家长' : '此家教';
                return $this->ajaxError(20014,[],$role.'目前太火爆，今日被联系次数已达限额，为避免打扰他人，请明天再联系。');
            }
            Db::startTrans();
            $a = $contact->save(['uid' => $param['uid'], 'to_uid' => $param['to_uid'], 'chance' => 1, 'rest_chance' => $one['rest_chance'] - 1]); //绑定解锁关系
            $b = $user->save(['rest_chance' => ['exp', $one['rest_chance'] - 1]],['id'=>$param['uid']]); //修改剩余机会
            //统计解锁
            $learn = new Learn();
            $tutor = new Tutor();
            if ($one['role'] == 1) {
                $city = $learn->getValue($param['uid'],'city');
                $city2 = $tutor->getValue($param['to_uid'],'city');
            }else{
                $city = $tutor->getValue($param['uid'],'city');
                $city2 = $learn->getValue($param['to_uid'],'city');
            }
            $province = Utils::getProvinceByCity($city); //获取省份
            $province2 = Utils::getProvinceByCity($city2); //获取被解锁省份
            $statistic = new Statistic();
            $all = [
                ['type'=>3,'uid'=>$param['uid'],'role'=>$one['role'],'sex'=>$one['sex'],'province'=>$province['province'],'city'=>$province['city'],'addtime'=>time(),'add_date'=>date('Y-m-d')],
                ['type'=>4,'uid'=>$param['to_uid'],'role'=>$two['role'],'sex'=>$two['sex'],'province'=>$province2['province'],'city'=>$province2['city'],'addtime'=>time(),'add_date'=>date('Y-m-d')],
            ];
            $c = $statistic->addAll($all);
            
            if ($a && $b && $c) {
                Db::commit();
                return $this->ajaxSuccess(20001,[],'解锁成功');
            }else{
                Db::rollback();
                return $this->ajaxError(20011,[],'解锁失败');
            }
        }catch (Exception $exception){
            Db::rollback();
            return $this->ajaxError(20011,[],'解锁失败');
        }
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-09
     *
     * @description 解锁成功后信息页
     * @param array $param
     * @return array
     */
    public function afterContact(array $param)
    {
        $user = new User();
        $learn = new Learn();
        $tutor = new Tutor();
        if ($param['role'] == 1) {
            $myInfo = $learn->getOne($param['uid']); //获取用户经纬度
            $userInfo = $user->finds($param['to_uid'], 'portrait,head_name,sex,wechat,qrcode'); //家教头像名称等
            $info = $tutor->getOne($param['to_uid']); //家教需求
            $userInfo['head_name'] = $userInfo['head_name'] . '老师';
            $teach = config('teach');
            $range = explode(',', $info['teach_range']);
            array_pop($range);
            foreach ($range as $k => $v) {
                $new_range[$k] = $teach['range'][$v];
            }
            $subject = explode(',', $info['teach_subject']);
            array_pop($subject);
            foreach ($subject as $k => $v) {
                $new_subject[$k] = $teach['subject'][$v];
            }
            $userInfo['range'] = $new_range ?? [];
            $userInfo['subject'] = $new_subject ?? [];
        }else{
            $myInfo = $tutor->getOne($param['uid']); //获取用户经纬度
            $userInfo = $user->finds($param['to_uid'], 'portrait,head_name,sex,wechat,qrcode'); //家长头像名称等
            $info = $learn->getOne($param['to_uid']); //家长需求
            $userInfo['head_name'] = $userInfo['sex'] == 1 ? '先生' : '女士';
            $teach = config('teach');
            $userInfo['range'] = $teach['range'][$info['learn_range']] ?? '';
            $userInfo['subject'] = $teach['subject'][$info['learn_subject']] ?? '';
        }
        $userInfo['qrcode'] = !empty($userInfo['qrcode']) ? config('oss.outer_host').$userInfo['qrcode'] : '';

        $geo = new Geohash();
        $dis = $geo->getDistance($myInfo['lat'],$myInfo['lng'],$info['lat'],$info['lng']); //保留米
        if ($dis < 1000) {
            $userInfo['distance'] = '<1km';
        }elseif ($dis > 1000) {
            $userInfo['distance'] = intval($dis/1000).'km';
        }
        return $this->ajaxSuccess(104,['list'=>$userInfo]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-08
     *
     * @description 各环节认证状态
     * @param array $param
     * @return array
     */
    public function getUserAuth(array $param)
    {
        $user = new User();
        $id = $user->finds($param['uid'],'body_name,idcard,wechat,wechat_status,qrcode,qrcode_status,role');
        $auth = [
            'wechat_status'=>($id['wechat_status'] >1) ? 2 : $id['wechat_status'],
            'qrcode_status'=>($id['qrcode_status'] > 1) ? 2 : $id['qrcode_status'] ,
            'idcard_status'=>-1,
            'edu_status'=>-1,
            'wechat'=>$id['wechat'],
            'qrcode'=>!empty($id['qrcode']) ? config('oss.outer_host').$id['qrcode'] : '',
            'body_name'=>$id['body_name'],
            'idcard'=>$id['idcard'],
            'xl_url'=>'',
            'professional'=>'',
        ];
        $cer = new UserCer();
        $iden = $cer->getOne(['uid'=>$param['uid'],'type'=>1],'status');
        if (!empty($iden)) {
            $auth['idcard_status'] = $iden['status'];
        }
        if ($id['role'] == 2) { //家教学历
            $iden2 = $cer->getOne(['uid'=>$param['uid'],'type'=>2],'status');
            if (!empty($iden2)) {
                $auth['edu_status'] = $iden2['status'];
                $userInfo = new UserCerInfo();
                $edu = $userInfo->getOne(['uid'=>$param['uid']],'professional');
                $image = new UserImage();
                $m = $image->getOne($param['uid'],'url');
                $auth['xl_url'] = config('oss.outer_host').$m['url'];
                $auth['professional'] = $edu['professional'] ?? '';
            }
        }
        if ($auth['wechat_status'] == 0 && empty($id['wechat'])) {
            $auth['wechat_status'] = -1; //未认证特殊情况
        }
        if ($auth['qrcode_status'] == 0 && empty($id['qrcode'])) {
            $auth['qrcode_status'] = -1; //未认证特殊情况
        }
        return $this->ajaxSuccess(104,['list'=>$auth]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-10
     *
     * @description 助教资料
     * @param array $param
     * @return array
     */
    public function assistantDetail(array $param)
    {
        $user = new User();
        $id = $user->finds($param['uid'],'assistant,role');
        if (empty($id)) {
            return $this->ajaxError(114);
        }
        if ($id['role'] == 1) {
            $learn = new Learn();
            $city = $learn->getValue($param['uid'],'city');
        }elseif ($id['role'] == 2) {
            $tutor = new Tutor();
            $city = $tutor->getValue($param['uid'],'city');
        }
        $ass = new Assistant();
        $info = $ass->getOne(['id'=>$id['assistant']],'id,name,phone,city,assistant,qrcode');
        $info['city'] = $city ?? $info['city'];
        $info['company'] = '家教汪';
        $info['portrait'] = config('oss.outer_host').$info['assistant'];
        $info['qrcode'] = config('oss.outer_host').$info['qrcode'];
        return $this->ajaxSuccess(104,['list'=>$info]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-17
     *
     * @description 根据手机号获取用户id
     * @param array $param
     * @return array
     */
    public function getOpenidByMobile(array $param)
    {
        $user = new User();
        $openid = $user->getOne(['phone'=>$param['phone']],'openid,id');
        if (empty($openid)) {
            return $this->ajaxError(114,[],'请输入有效的手机号');
        }
        return $this->ajaxSuccess(104,['openid'=>$openid['openid'],'uid'=>$openid['id']]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 上传二维码(公众号)
     * @param array $param
     * @return array
     */
    public function gzhuploadQrcode(array $param)
    {
        if (empty($param['uid'])) {
            return $this->ajaxError(111,[],'id不能为空');
        }
        if (!empty($_FILES)) {
            $a = Utils::uploadPic($_FILES,'qrcode');
            if (!empty($a)) {
                $this->user->save(['qrcode'=>$a,'qrcode_status'=>0],['id'=>$param['uid']]);

                return $this->ajaxSuccess(101,['list'=>$a]);
            }
            return $this->ajaxError(111);
        }
        return $this->ajaxSuccess(101);
    }

}