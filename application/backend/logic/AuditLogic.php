<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/9
 * Time: 上午10:58
 */

namespace app\backend\logic;


use app\api\logic\FormIdLogic;
use app\api\model\Learn;
use app\api\model\Statistic;
use app\api\model\Tutor;
use app\api\model\UserCer;
use app\api\model\UserImage;
use app\backend\model\User;
use app\common\logic\BaseLogic;
use extend\helper\Utils;
use extend\service\WechatService;
use think\Exception;
use think\Request;

class AuditLogic extends BaseLogic
{
    protected $order = null;
    protected $orderqy = null;
    protected $user = null;
    protected $usercer = null;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->user = new User();
        $this->usercer = new UserCer();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-25
     *
     * @description 家长审核列表(停用)
     * @param $param
     * @return array
     */
    public function parentsAudit($param){
        #序号 头像 openid 微信号 手机号 姓名 实名认证状态 地址
        switch ($param['type'] ?? 0){ //0待审核  1不通过  2已通过
            case 0:
                $certime = ' and u.certime = 0';break;
            case 1:
                $certime = ' and u.certime = 1';break;
            case 2:
                $certime = ' and u.certime > 1';break;

        }
        $where = 'u.role = 1 '.$certime.' and uc.type = 1 ';
        if(!empty($param['start_time'])){
            $where .= ' and u.addtime >= "'.$param['start_time'].'"';
        }
        if(!empty($param['end_time'])){
            $where .= ' and u.addtime <= "'.$param['end_time'].'"';
        }
        if(!empty($param['name'])) {
            $where .= ' and u.body_name = "' . $param['name'] . '"';
        }
        try{
            $list = $this->user->alias('u')
                ->join('tut_user_cer uc','uc.uid = u.id','left')
                ->join('tut_learn l','l.uid = u.id','left')
                ->where($where)
                ->order('u.id desc')
                ->field('u.id,u.portrait,u.openid,u.wechat,u.phone,u.body_name,CASE uc.status WHEN 1 THEN "认证失败" WHEN 2 THEN "认证成功" ELSE "认证待审核" END as status,l.city')
                ->select();
            if (!empty($list)) {
                $user = new UserLogic();
                foreach ($list as $key => $value) {
                    $list[$key]['auth'] = $user->getFailStep(['uid'=>$value['id']]);
                }
            }
        }catch (Exception $exception){
            return $this->ajaxError(114);
        }
        return $this->ajaxSuccess(104,$list);
        #return $list;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description  家长审核
     * @param int $uid
     * @param int $status
     * @param int $type 3微信号 4二维码
     * @return array
     */
    public function setStatusPar(int $uid,int $status, int $type){
        try{
            $cer_status = $this->usercer->where(['uid'=>$uid,'type'=>1])->value('status');

            if($status == 1){   //通过
                if ($type == 3) {
                    if($cer_status == 2){ //身份通过
                        $this->user->save(['wechat_status'=>time(),'certime'=>time()],['id'=>$uid]);
                        //统计认证
                        $one = $this->user->finds($uid,'role,sex');
                        $learn = new Learn();
                        $city = $learn->getValue($uid,'city');
                        $learn->editLearn(['is_order'=>1],$uid);
                        $province = Utils::getProvinceByCity($city); //获取省份
                        $statistic = new Statistic();
                        $statistic->add(['type'=>2,'uid'=>$uid,'role'=>$one['role'],'sex'=>$one['sex'],'province'=>$province['province'],'city'=>$province['city'],'addtime'=>time(),'add_date'=>date('Y-m-d')]);
                    }else{
                        $this->user->save(['wechat_status'=>time()],['id'=>$uid]);
                    }
                }
                if ($type == 4) {
                    $this->user->save(['qrcode_status'=>time()],['id'=>$uid]);
                }
            }else{    //拒绝
                if ($type == 3) {
                    $this->user->save(['wechat_status'=>1,'certime'=>1],['id'=>$uid]);

                    $openid = $this->user->finds($uid,'openid,body_name');
                    $formIdSer = new FormIdLogic();
                    $modelPush = new WechatService();
                    $pusharr = [
                        'type'=>'wechat_fail',
                        'openid'=>$openid['openid'],
                        'page'=>'pages/parent/info_det/info_det?uid='.$uid,
                        'form_id'=>$formIdSer->getFormId($uid),
                        'key1'=> $openid['body_name'],
                        'key2'=>date('Y年m月d日 H时'),
                        'key3'=> '微信号认证失败',
                    ];
                    $rs = $modelPush->tplSend($pusharr);
                }
                if ($type == 4) {
                    $this->user->save(['qrcode_status'=>1],['id'=>$uid]);
                }
            }
        }catch (Exception $exception){
            return $this->ajaxError(112);
        }
        return $this->ajaxSuccess(102);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 家教审核
     * @param int $uid
     * @param int $status
     * @param int $type 2学历 3微信 4二维码
     * @return array
     */
    public function setStatusTut(int $uid,int $status,int $type){
        try{
            $cer_status = $this->usercer->where(['uid'=>$uid])->field('status,type')->select(); //身份认证
            if($status == 1){ #通过
                $biaox = 0; //学历
                $biaos = 0; //身份
                if (!empty($cer_status)) {
                    foreach ($cer_status as $k => $v) {
                        switch ($v['type']) {
                            case 1: //身份认证
                                if ($v['status'] == 2) {
                                    $biaos = 1;
                                }
                                break;
                            case 2: //学历
                                if ($v['status'] == 2) {
                                    $biaox = 1;
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
                if($type == 3){
                    if($biaox == 1 && $biaos == 1){
                        $this->user->save(['wechat_status'=>time(),'certime'=>time()],['id'=>$uid]);   //微信通过 全部通过
                        //统计认证
                        $one = $this->user->finds($uid,'role,sex');
                        $tutor = new Tutor();
                        $city = $tutor->getValue($uid,'city');
                        $tutor->editTutor(['is_order'=>1],$uid);
                        $province = Utils::getProvinceByCity($city); //获取省份
                        $statistic = new Statistic();
                        $statistic->add(['type'=>2,'uid'=>$uid,'role'=>$one['role'],'sex'=>$one['sex'],'province'=>$province['province'],'city'=>$province['city'],'addtime'=>time(),'add_date'=>date('Y-m-d')]);
                    }else{
                        $this->user->save(['wechat_status'=>time()],['id'=>$uid]);   #微信通过
                    }
                }elseif ($type == 2){
                    $a = $this->user->getValue($uid,'wechat_status');
                    if($biaos == 1 && ($a > 1)){
                        $this->usercer->save(['status'=>2,'audittime'=>date('Y-m-d H:i:s',time())],['uid'=>$uid,'type'=>2]);   #学历通过 全部通过
                        $this->user->save(['certime' => time()],['id'=>$uid]);
                        //统计认证
                        $one = $this->user->finds($uid,'role,sex');
                        $tutor = new Tutor();
                        $city = $tutor->getValue($uid,'city');
                        $tutor->editTutor(['is_order'=>1],$uid);
                        $province = Utils::getProvinceByCity($city); //获取省份
                        $statistic = new Statistic();
                        $statistic->add(['type'=>2,'uid'=>$uid,'role'=>$one['role'],'sex'=>$one['sex'],'province'=>$province['province'],'city'=>$province['city'],'addtime'=>time(),'add_date'=>date('Y-m-d')]);
                    }else{
                        $this->usercer->save(['status'=>2,'audittime'=>date('Y-m-d H:i:s',time())],['uid'=>$uid,'type'=>2]);  #学历通过
                    }
                }elseif ($type == 4) {
                    $this->user->save(['qrcode_status'=>time()],['id'=>$uid]);   #微信二维码通过
                }
            }else{
                if($type == 3){
                    $this->user->save(['wechat_status'=>1,'certime'=>1],['id'=>$uid]);   #微信拒绝
                    $openid = $this->user->finds($uid,'openid,body_name,role');
                    $formIdSer = new FormIdLogic();
                    $modelPush = new WechatService();
                    if ($openid['role'] == 1) {
                        $page = 'pages/learn/info/info';
                    }else{
                        $page = 'pages/tutor/info/info';
                    }
                    $pusharr = [
                        'type'=>'wechat_fail',
                        'openid'=>$openid['openid'],
                        'page'=>$page,
                        'form_id'=>$formIdSer->getFormId($uid),
                        'key1'=> $openid['body_name'],
                        'key2'=>date('Y年m月d日 H时'),
                        'key3'=> '微信号认证失败',
                    ];
                    $rs = $modelPush->tplSend($pusharr);
                }elseif ($type == 2){
                    $this->usercer->save(['status'=>1],['uid'=>$uid,'type'=>2]);   #学历拒绝
                    $this->user->save(['certime'=>1],['id'=>$uid]);
                }elseif ($type == 4) {
                    $this->user->save(['qrcode_status'=>1],['id'=>$uid]);   #微信通过
                }
            }
        }catch (Exception $exception){
            return $this->ajaxError(112);
        }
        return $this->ajaxSuccess(102);
    }

    /** 家教审核列表 (停用)
     * auth smallzz
     * @param $param
     * @return array
     */
    public function tutorAudit($param){
        switch ($param['type'] ?? 0){
            case 0:
                $certime = ' and u.certime = 0';break;
            case 1:
                $certime = ' and u.certime = 1';break;
            case 2:
                $certime = ' and u.certime > 1';break;
            default :
                $certime = ' and u.certime = 0';break;
        }
        $where = 'u.role = 2 '.$certime.' and uc.type = 1';

        if(!empty($param['start_time'])){
            $where .= ' and u.addtime >= "'.$param['start_time'].'"';
        }
        if(!empty($param['end_time'])){
            $where .= ' and u.addtime <= "'.$param['end_time'].'"';
        }
        if(!empty($param['name'])){
            $where .= ' and u.body_name = "'.$param['name'].'"';
        }
        try{
            $list = $this->user->alias('u')
                ->join('tut_user_cer uc','uc.uid = u.id','inner')
                ->join('tut_tutor t','t.uid = u.id','left')
                ->join('tut_user_cer_info e','e.uid = u.id','left')
                ->join('tut_user_image m','m.uid = u.id','left')
                ->where($where)
                ->order('u.id desc')
                ->field('u.id,u.portrait,u.openid,u.wechat,u.phone,u.body_name,CASE uc.status WHEN 1 THEN "认证失败" WHEN 2 THEN "认证成功" ELSE "认证待审核" END as status,t.city,e.name as edu_name,e.school as edu_school,e.diploma as edu_diploma,e.addschool as edu_addschool,e.professional as edu_professional,m.url as edu_url')
                ->select();
            if (!empty($list)) {
                $user = new UserLogic();
                foreach ($list as $key => $value) {
                    $list[$key]['edu_url'] = !empty($value['edu_url']) ? config('wechat.app_url').$value['edu_url'] : '';
                    $list[$key]['auth'] = $user->getFailStep(['uid'=>$value['id']]);
                }
            }
        }catch (Exception $exception){
            return $this->ajaxError(114);
        }
        return $this->ajaxSuccess(104,$list);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 微信审核列表
     * @param array $param
     * @return array
     */
    public function wechatAudit(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = 'role > 0 and wechat != "" ';

        switch ($param['type'] ?? 0){ //0待审核  1不通过  2已通过
            case 0:
                $where .= ' and wechat_status = 0';break;
            case 1:
                $where .= ' and wechat_status = 1';break;
            case 2:
                $where .= ' and wechat_status > 1';break;
            default:
                $where .= ' and wechat_status = 0';break;
        }
        if(!empty($param['start_time'])){
            $where .= ' and addtime >= "'.$param['start_time'].'"';
        }
        if(!empty($param['end_time'])){
            $where .= ' and addtime <= "'.$param['end_time'].'"';
        }
        if(!empty($param['name'])) {
            $where .= ' and body_name like "%'.$param['name'].'%" ';
        }
        $info = $this->user->getWechatAudit($where,$page,$size);
        $count = $this->user->getWechatAuditCount($where);
        if (!empty($info)) {
            foreach ($info as $key => $value) {
                $info[$key]['qrcode'] = !empty($value['qrcode']) ? config('oss.outer_host').$value['qrcode'] : '';
                $info[$key]['uid'] = $value['id'];
            }
        }
        return $this->ajaxSuccess(104,['total'=>$count,'list'=>$info]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 学历审核列表
     * @param array $param
     * @return array
     */
    public function educationAudit(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = 'c.type = 2 and u.role = 2 ';

        switch ($param['type'] ?? 0){ //0待审核  1不通过  2已通过
            case 0:
                $where .= ' and c.status = 0';break;
            case 1:
                $where .= ' and c.status = 1';break;
            case 2:
                $where .= ' and c.status = 2';break;
            default:
                $where .= ' and c.status = 0';break;
        }

        if(!empty($param['start_time'])){
            $where .= ' and u.addtime >= "'.$param['start_time'].'"';
        }
        if(!empty($param['end_time'])){
            $where .= ' and u.addtime <= "'.$param['end_time'].'"';
        }
        if(!empty($param['name'])) {
            $where .= ' and u.body_name like "%'.$param['name'].'%" ';
        }
        $field = 'u.id as uid,u.body_name,u.phone,i.school as edu_school,i.diploma as edu_diploma,i.addschool as edu_addschool,i.professional as edu_professional,i.name as edu_name,m.url as edu_url';

        $info = $this->user->getEducationAudit($where,$page,$size,$field);
        $count = $this->user->getEducationAuditCount($where,$field);
        if (!empty($info)) {
            foreach ($info as $key => $value) {
                $info[$key]['edu_url'] = !empty($value['edu_url']) ? config('oss.outer_host').$value['edu_url'] : '';
            }
        }
        return $this->ajaxSuccess(104,['total'=>$count,'list'=>$info]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 微信二维码审核列表
     * @param array $param
     * @return array
     */
    public function qrcodeAudit(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = 'role > 0 and wechat != "" ';

        switch ($param['type'] ?? 0){ //0待审核  1不通过  2已通过
            case 0:
                $where .= ' and qrcode_status = 0';break;
            case 1:
                $where .= ' and qrcode_status = 1';break;
            case 2:
                $where .= ' and qrcode_status > 1';break;
            default:
                $where .= ' and qrcode_status = 0';break;
        }
        if(!empty($param['start_time'])){
            $where .= ' and addtime >= "'.$param['start_time'].'"';
        }
        if(!empty($param['end_time'])){
            $where .= ' and addtime <= "'.$param['end_time'].'"';
        }
        if(!empty($param['name'])) {
            $where .= ' and body_name like "%'.$param['name'].'%" ';
        }
        $info = $this->user->getWechatAudit($where,$page,$size);
        $count = $this->user->getWechatAuditCount($where);
        if (!empty($info)) {
            foreach ($info as $key => $value) {
                $info[$key]['qrcode'] = !empty($value['qrcode']) ? config('oss.outer_host').$value['qrcode'] : '';
                $info[$key]['uid'] = $value['id'];
            }
        }
        return $this->ajaxSuccess(104,['total'=>$count,'list'=>$info]);
    }
}