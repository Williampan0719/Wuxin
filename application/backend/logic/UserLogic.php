<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/30
 * Time: 上午9:50
 * @introduce
 */
namespace app\backend\logic;

use app\api\model\Config;
use app\api\model\Contacts;
use app\api\model\Favorite;
use app\api\model\Learn;
use app\api\model\Order;
use app\api\model\Position;
use app\api\model\Refund;
use app\api\model\Tutor;
use app\api\model\UserCer;
use app\api\model\UserCerInfo;
use app\api\model\UserImage;
use app\backend\model\Assistant;
use app\backend\model\Complain;
use app\backend\model\Resource;
use app\backend\model\User;
use app\common\logic\BaseLogic;
use extend\helper\Utils;
use extend\service\WechatService;
use think\Db;
use think\Exception;

//Loader::import('thirdpart.wxpay.WxPayPubHelper.WxPayPubHelper');
//Loader::import('thirdpart.wxpay.lib.WxPay');
//require_once __DIR__.'/../../../extend/thirdpart/wxpay/lib/WxPay.php';

class UserLogic extends BaseLogic
{
    /**
     * @Author panhao
     * @DateTime 2018-04-10
     *
     * @description 搜索家教列表
     * @param array $param
     * @return array
     */
    public function searchTutorList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = [];
        if (!empty($param['head_name'])) {
            $where['u.head_name'] = $param['head_name'];
        }
        if (!empty($param['phone'])) {
            $where['u.phone'] = $param['phone'];
        }
        if (!empty($param['city'])) {
            $where['t.city'] = ['like','%'.$param['city'].'%'];
        }
        if (isset($param['is_order']) && is_numeric($param['is_order'])) {
            $where['t.is_order'] = $param['is_order'];
        }
        if (isset($param['certime']) && is_numeric($param['certime'])) {
            if ($param['certime'] == 2) {
                $where['u.certime'] = ['gt',1];
            }else{
                $where['u.certime'] = $param['certime'];
            }
        }
        $field = 'u.id,u.portrait,u.wechat,u.openid,u.body_name,u.phone,t.city,u.certime,c.school,t.is_order';
        $user = new User();
        $list = $user->searchTutorList($where,$page,$size,$field);
        $total = 0;
        if (!empty($list)) {
            $total = $user->searchTutorCount($where);
            foreach ($list as $key => $value) {
                $list[$key]['certime'] = $value['certime'] > 1 ? '认证成功' : ($value['certime'] == 0 ? '未认证' : '认证失败');
            }
        }
        return $this->ajaxSuccess(104,['total'=>$total,'list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-10
     *
     * @description 搜索家长列表
     * @param array $param
     * @return array
     */
    public function searchLearnList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = [];
        if (!empty($param['head_name'])) {
            $where['u.head_name'] = $param['head_name'];
        }
        if (!empty($param['phone'])) {
            $where['u.phone'] = $param['phone'];
        }
        if (!empty($param['city'])) {
            $where['l.city'] = ['like','%'.$param['city'].'%'];
        }
        if (isset($param['is_order']) && is_numeric($param['is_order'])) {
            $where['l.is_order'] = $param['is_order'];
        }
        if (isset($param['certime']) && is_numeric($param['certime'])) {
            if ($param['certime'] == 2) {
                $where['u.certime'] = ['gt',1];
            }else{
                $where['u.certime'] = $param['certime'];
            }
        }
        $field = 'u.id,u.portrait,u.wechat,u.openid,u.body_name,u.phone,u.certime,l.is_order,l.city';
        $user = new User();
        $list = $user->searchLearnList($where,$page,$size,$field);
        $total = 0;
        if (!empty($list)) {
            $total = $user->searchLearnCount($where);
            foreach ($list as $key => $value) {
                $list[$key]['certime'] = $value['certime'] > 1 ? '认证成功' : ($value['certime'] == 0 ? '未认证' : '认证失败');
            }
        }
        return $this->ajaxSuccess(104,['total'=>$total,'list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-10
     *
     * @description 搜索家教家长混合列表
     * @param array $param
     * @return array
     */
    public function searchUserList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = 'u.role >= 0 ';
        if (!empty($param['role'])) {
            $where = ' u.role = '.$param['role'];
        }
        if (!empty($param['body_name'])) {
            $where .= ' and u.body_name like "%'.$param['body_name'].'%" ';
        }
        if (!empty($param['nickname'])) {
            $where .= ' and u.nickname like "%'.$param['nickname'].'%" ';
        }
        if (!empty($param['phone'])) {
            $where .= ' and u.phone = '.$param['phone'];
        }
        if (isset($param['is_remark']) && is_numeric($param['is_remark'])) {
            if ($param['is_remark'] == 1) {
                $where .= ' and u.remark != "" ';
            }else{
                $where .= ' and u.remark = "" ';
            }
        }
        if (!empty($param['resource_id'])) {
            $where .= ' and u.resource = '.$param['resource_id'];
        }
        if (!empty($param['city'])) {
            $where .= ' and (l.city like "'.$param['city'].'%" or t.city like "'.$param['city'].'%" ) ';
        }
        if (isset($param['is_order']) && is_numeric($param['is_order'])) {
            $where .= ' and (l.is_order = '.$param['is_order'].' or t.is_order = '.$param['is_order'].')';
        }
        if (isset($param['certime']) && is_numeric($param['certime'])) {
            if ($param['certime'] == 2) {
                $where .= ' and u.certime > 1';
            }elseif ($param['certime'] == 1){
                $where .= ' and u.certime = 1';
            }elseif ($param['certime'] == 0){
                $where .= ' and ((role = 1 and certime = 0 and body_name != "" and wechat != "") or (role = 2 and certime = 0 and body_name != "" and wechat != "" and c.type = 2)) ';
            }elseif ($param['certime'] == -1){
                $where .= ' and ((role = 1 and certime = 0 and (body_name = "" or wechat = "")) or (role = 2 and certime = 0 and (body_name = "" or wechat = "" or c.status = null))) ';
            }
        }
        $field = 'c.type as ctype,u.id,u.role,u.body_name,u.phone,u.wechat,u.certime,l.city as lcity,t.city as tcity,l.is_order as lorder,t.is_order as torder,u.assistant,u.resource,u.remark,u.quick_remark,u.nickname,u.level';
        $user = new User();
        $list = $user->searchUserList($where,$page,$size,$field);
        $total = 0;
        if (!empty($list)) {
            $ass = new Assistant();
            $re = new Resource();
            $cer = new UserCer();
            $total = $user->searchUserCount($where);
            foreach ($list as $key => $value) {
                if (!empty($value['assistant'])) {
                    $list[$key]['assistant'] = $ass->getValue(['id'=>$value['assistant']],'name');
                }else{
                    $list[$key]['assistant'] = '';
                }
                if (!empty($value['resource'])) {
                    $list[$key]['resource'] = $re->getValue(['id'=>$value['resource']],'name');
                }else{
                    $list[$key]['resource'] = '';
                }
                $list[$key]['city'] = ($value['role'] == 1 ? $value['lcity'] : $value['tcity']);
                $list[$key]['is_order'] = ($value['role'] == 1 ? $value['lorder'] : $value['torder']);
                if ($value['certime'] == 0) {
                    $list[$key]['certime'] = '审核中';
                    if ($value['role'] == 1 && empty($value['wechat']) && empty($value['body_name'])) {
                        $list[$key]['certime'] = '未认证';
                    }
                    if ($value['role'] == 2 && empty($value['wechat']) && empty($value['body_name'])) {
                        $a = $cer->getOne(['uid'=>$value['id'],'type'=>2],'id');
                        if (empty($a)) {
                            $list[$key]['certime'] = '未认证';
                        }
                    }
                    if ($value['role'] == 0) {
                        $list[$key]['certime'] = '未认证';
                    }
                }else {
                    $list[$key]['certime'] = $value['certime'] > 1 ? '认证成功' : ($value['certime'] == 0 ? '未认证' : '认证失败');
                }
            }
        }
        return $this->ajaxSuccess(104,['total'=>$total,'list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 家教面板
     * @param array $param
     * @return array
     */
    public function tutorPanel(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        //用户基本信息
        $user = new User();
        $ass = new Assistant();
        $re = new Resource();
        $userInfo = $user->finds($param['uid'],'id,role,portrait,openid,wechat,qrcode,phone,sex,body_name,addtime,wechat_status,idcard,nickname,assistant,resource,remark,logintime,device,level');
        if (!empty($userInfo['assistant'])) {
            $userInfo['assistant'] = $ass->getValue(['id'=>$userInfo['assistant']],'name');
        }else{
            $userInfo['assistant'] = '';
        }
        if (!empty($userInfo['resource'])) {
            $userInfo['resource'] = $re->getValue(['id'=>$userInfo['resource']],'name');
        }else{
            $userInfo['resource'] = '';
        }
        $userInfo['long_qrcode'] = !empty($userInfo['qrcode']) ? config('oss.outer_host').$userInfo['qrcode'] : '';
        $userInfo['age'] = '未知';
        $userInfo['birthday'] = '';
        if (!empty($userInfo['idcard'])) {
            $userInfo['sex'] = Utils::getSexByID($userInfo['idcard']);
            $userInfo['age'] = Utils::getAgeByID($userInfo['idcard']);
            $userInfo['birthday'] = substr($userInfo['idcard'],10,2).'-'.substr($userInfo['idcard'],12,2);
        }
        //家教需求
        $tutor = new Tutor();
        $tutorInfo = $tutor->getOne($param['uid'],'teach_range,teach_subject,is_order,city');
        if (!empty($tutorInfo)) {
            $userInfo['city'] = $tutorInfo['city'];
            $teach  = config('teach');
            $aa =[];$bb=[];
            $a = explode(',', $tutorInfo['teach_range']);
            $b = explode(',', $tutorInfo['teach_subject']);
            array_pop($a);
            array_pop($b);
            if (!empty($a)) {
                foreach ($a as $k => $v) {
                    $aa[] = $teach['range'][$v];
                }
            }
            if (!empty($b)) {
                foreach ($b as $k => $v) {
                    $bb[] = $teach['subject'][$v];
                }
            }
            $userInfo['teach_range'] = $aa;
            $userInfo['teach_subject'] = $bb;
            $userInfo['teach_range_id'] = !empty($tutorInfo['teach_range']) ? rtrim($tutorInfo['teach_range'],','): '';
            $userInfo['teach_subject_id'] = !empty($tutorInfo['teach_subject']) ? rtrim($tutorInfo['teach_subject'],',') : '';
            $userInfo['is_order'] = $tutorInfo['is_order'];
        }
        //认证信息
        $cer = new UserCer();
        $userCer = $cer->getGroupOne(['uid'=>$param['uid']],'type,status,audittime');
        if (!empty($userCer)) {
            foreach ($userCer as $key => $value) {
                $auth[$value['type']] = $value;
            }
            $userInfo['identity'] = !empty($auth[1]) ? ($auth[1]['status'] == 2 ? '认证通过' : ($auth[1]['status'] == 1 ? '认证失败' : '认证待审核')) : '未认证';
            $userInfo['identity_time'] = $userInfo['identity'] == '认证通过' ? $auth[1]['audittime'] : '';

            $userInfo['education'] = !empty($auth[2]) ? ($auth[2]['status'] == 2 ? '认证通过' : ($auth[2]['status'] == 1 ? '认证失败' : '认证待审核')) : '未认证';
            $userInfo['education_time'] = $userInfo['education'] == '认证通过' ? $auth[1]['audittime'] : '';

            $userInfo['wechat_auth'] = $userInfo['wechat_status'] > 1 ? '认证通过' : ($userInfo['wechat_status'] == 1 ? '认证失败' : '未认证');
            $userInfo['wechat_auth_time'] = $userInfo['wechat_status'] > 1 ? date('Y-m-d H:i:s',$userInfo['wechat_status']) : '';
        }
        //学历
        $cerInfo = new UserCerInfo();
        $userCerInfo = $cerInfo->getOne(['uid'=>$param['uid']],'school,diploma,professional');
        $userInfo['school'] = $userCerInfo['school'] ?? '';
        $userInfo['diploma'] = $userCerInfo['diploma'] ?? '';
        $userInfo['professional'] = $userCerInfo['professional'] ?? '';
        if (!empty($userCerInfo)) {
            $image = new UserImage();
            $url = $image->getOne($param['uid'],'url');
            $userInfo['xl_url'] = config('oss.outer_host').$url['url'];
        }

        //地址管理列表
        $position = new Position();
        $address = $position->getPositionList($param['uid'],'uid,geo_name,status,create_at');
        $userInfo['address'] = $address;

        //购买列表
        $contacts = new Contacts();
        if (!empty($param['class']) && $param['class'] == 2) {
            $where = 'uid = '.$param['uid'];
        }elseif (!empty($param['class']) && $param['class'] == 3) {
            $where = 'to_uid = '.$param['uid'];
        }else{
            $where = 'uid = '.$param['uid'].' or to_uid = '.$param['uid'];
        }
        $count = $contacts->getTotal($where);
        $list = $contacts->getPageList($where,$page,$size);
        $sum = 0;
        if (!empty($list)) {
            $uids = [];
            foreach ($list as $k => $v) {
                if ($v['uid'] == $param['uid']) {
                    $uids[] = $v['to_uid'];
                }else{
                    $uids[] = $v['uid'];
                }
            }
            $user = new User();
            $userInfo2 = $user->getInfoByIds(['id' => ['in', $uids]], 'portrait,id,head_name,phone,sex,role');
            $new = [];
            foreach ($userInfo2 as $key => $value) {
                $new[$value['id']] = $value;
            }
            foreach ($list as $key => $value) {
                if ($value['uid'] == $param['uid']) { //购买
                    $list[$key]['portrait'] = $new[$value['to_uid']]['portrait'];
                    $list[$key]['phone'] = $new[$value['to_uid']]['phone'];
                    $list[$key]['buy'] = 1;
                    $list[$key]['head_name'] = $new[$value['to_uid']]['head_name'].($new[$value['to_uid']]['role'] == 2?'老师':($new[$value['to_uid']]['sex'] == 1 ?'先生':'女士'));
                }elseif($value['to_uid'] == $param['uid']){ // 被购买
                    $list[$key]['portrait'] = $new[$value['uid']]['portrait'];
                    $list[$key]['phone'] = $new[$value['uid']]['phone'];
                    $list[$key]['buy'] = 0;
                    $list[$key]['head_name'] = $new[$value['uid']]['head_name'].($new[$value['uid']]['role'] == 2?'老师':($new[$value['uid']]['sex'] == 1 ?'先生':'女士'));
                }
            }
            $sum = $contacts->getSum($where,'money');
        }
        $userInfo['contact'] = [
            'list'=>$list,
            'total'=>$count,
            'sum'=>$sum,
        ];
        //收藏
        $fav = new Favorite();
        $favList = $fav->getList($param['uid']);
        if (!empty($favList)) {
            foreach ($favList as $key => $value) {
                $a = $user->finds($value['uid'],'body_name,phone');
                $favList[$key]['name'] = $a['body_name'];
                $favList[$key]['phone'] = $a['phone'];
                $b = $user->finds($value['to_uid'],'body_name');
                $favList[$key]['to_name'] = $b['body_name'];
            }
        }
        $userInfo['favorite'] = $favList;
        return $this->ajaxSuccess(104,['list'=>$userInfo]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 家长面板
     * @param array $param
     * @return array
     */
    public function learnPanel(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        //用户基本信息
        $user = new User();
        $ass = new Assistant();
        $re = new Resource();
        $userInfo = $user->finds($param['uid'],'id,role,portrait,openid,wechat,sex,qrcode,phone,body_name,addtime,wechat_status,idcard,nickname,assistant,resource,remark,logintime,device,level');
        if (!empty($userInfo['assistant'])) {
            $userInfo['assistant'] = $ass->getValue(['id'=>$userInfo['assistant']],'name');
        }else{
            $userInfo['assistant'] = '';
        }
        if (!empty($userInfo['resource'])) {
            $userInfo['resource'] = $re->getValue(['id'=>$userInfo['resource']],'name');
        }else{
            $userInfo['resource'] = '';
        }
        $userInfo['long_qrcode'] = !empty($userInfo['qrcode']) ? config('oss.outer_host').$userInfo['qrcode'] : '';
        $userInfo['age'] = '未知';
        $userInfo['birthday'] = '';
        if (!empty($userInfo['idcard'])) {
            $userInfo['sex'] = Utils::getSexByID($userInfo['idcard']);
            $userInfo['age'] = Utils::getAgeByID($userInfo['idcard']);
            $userInfo['birthday'] = substr($userInfo['idcard'],10,2).'-'.substr($userInfo['idcard'],12,2);
        }
        //家长需求
        $learn = new Learn();
        $learnInfo = $learn->getOne($param['uid'],'learn_range,learn_subject,is_order,city');
        $teach = config('teach');
        $userInfo['city'] = $learnInfo['city'];
        $userInfo['learn_range'] = is_numeric($learnInfo['learn_range']) ? $teach['range'][$learnInfo['learn_range']] : '';
        $userInfo['learn_subject'] = is_numeric($learnInfo['learn_subject']) ? $teach['subject'][$learnInfo['learn_subject']] : '';
        $userInfo['learn_range_id'] = $learnInfo['learn_range'];
        $userInfo['learn_subject_id'] = $learnInfo['learn_subject'];
        $userInfo['is_order'] = $learnInfo['is_order'];
        //认证信息
        $cer = new UserCer();
        $userCer = $cer->getGroupOne(['uid'=>$param['uid']],'type,status,audittime');
        if (!empty($userCer)) {
            foreach ($userCer as $key => $value) {
                $auth[$value['type']] = $value;
            }
            $userInfo['identity'] = !empty($auth[1]) ? ($auth[1]['status'] == 2 ? '认证通过' : ($auth[1]['status'] == 1 ? '认证失败' : '认证待审核')) : '未认证';
            $userInfo['identity_time'] = $userInfo['identity'] == '认证通过' ? $auth[1]['audittime'] : '';

            $userInfo['wechat_auth'] = $userInfo['wechat_status'] > 1 ? '认证通过' : ($userInfo['wechat_status'] == 1 ? '认证失败' : '未认证');
            $userInfo['wechat_auth_time'] = $userInfo['wechat_status'] > 1 ? date('Y-m-d H:i:s',$userInfo['wechat_status']) : '';
        }

        $position = new Position();
        $address = $position->getPositionList($param['uid'],'uid,geo_name,status,create_at');
        $userInfo['address'] = $address;

        $contacts = new Contacts();
        if (!empty($param['class']) && $param['class'] == 2) {
            $where = 'uid = '.$param['uid'];
        }elseif (!empty($param['class']) && $param['class'] == 3) {
            $where = 'to_uid = '.$param['uid'];
        }else{
            $where = 'uid = '.$param['uid'].' or to_uid = '.$param['uid'];
        }
        $count = $contacts->getTotal($where);
        $list = $contacts->getPageList($where,$page,$size);
        $sum = 0;
        if (!empty($list)) {

            $uids = [];
            foreach ($list as $k => $v) {
                if ($v['uid'] == $param['uid']) {
                    $uids[] = $v['to_uid'];
                }else{
                    $uids[] = $v['uid'];
                }
            }
            $user = new User();
            $userInfo2 = $user->getInfoByIds(['id' => ['in', $uids]], 'portrait,id,head_name,phone,sex,role');
            $new = [];
            foreach ($userInfo2 as $key => $value) {
                $new[$value['id']] = $value;
            }
            foreach ($list as $key => $value) {
                if ($value['uid'] == $param['uid']) { //购买
                    $list[$key]['portrait'] = $new[$value['to_uid']]['portrait'];
                    $list[$key]['phone'] = $new[$value['to_uid']]['phone'];
                    $list[$key]['buy'] = 1;
                    $list[$key]['head_name'] = $new[$value['to_uid']]['head_name'].($new[$value['to_uid']]['role'] == 2?'老师':($new[$value['to_uid']]['sex'] == 1 ?'先生':'女士'));
                }elseif($value['to_uid'] == $param['uid']){ // 被购买
                    $list[$key]['portrait'] = $new[$value['uid']]['portrait'];
                    $list[$key]['phone'] = $new[$value['uid']]['phone'];
                    $list[$key]['buy'] = 0;
                    $list[$key]['head_name'] = $new[$value['uid']]['head_name'].($new[$value['uid']]['role'] == 2?'老师':($new[$value['uid']]['sex'] == 1 ?'先生':'女士'));
                }
            }
            $sum = $contacts->getSum($where,'money');
        }
        $userInfo['contact'] = [
            'list'=>$list,
            'total'=>$count,
            'sum'=>$sum,
        ];
        //收藏
        $fav = new Favorite();
        $favList = $fav->getList($param['uid']);
        if (!empty($favList)) {
            foreach ($favList as $key => $value) {
                $a = $user->finds($value['uid'],'body_name,phone');
                $favList[$key]['name'] = $a['body_name'];
                $favList[$key]['phone'] = $a['phone'];
                $b = $user->finds($value['to_uid'],'body_name');
                $favList[$key]['to_name'] = $b['body_name'];
            }
        }
        $userInfo['favorite'] = $favList;

        return $this->ajaxSuccess(104,['list'=>$userInfo]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-26
     *
     * @description 获取需求配置
     * @return array
     */
    public function getNeedConfig()
    {
        $info = [
            'teach_range' => config('teach.range'),
            'teach_subject' => config('teach.subject'),
            'tags' => config('teach.tags'),
        ];
        return $this->ajaxSuccess(104,['list'=>$info]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-15
     *
     * @description 渠道列表
     * @param array $param
     * @return array
     */
    public function resourceList(array $param)
    {
        $where = '';
        if (!empty($param['resource'])) {
            $where = 'name like "%' . $param['resource'] . '%"';
        }
        $resource = new Resource();
        $list = $resource->getList($where,'id,name');
        return $this->ajaxSuccess(104,['list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 201-04-13
     *
     * @description 后台退款接口
     * @param array $param
     * @return array
     */
    public function contactRefund(array $param)
    {
        $order = new Order();
        $where = ['uid'=>$param['uid'],'buyid'=>$param['to_uid'],'type'=>2,'status'=>1,'is_refund'=>0];
        $info = $order->getOne($where,'order_sn,amount,uid,id');
        if (empty($info)) {
            return $this->ajaxError(20003,[],'缺少有效付款订单');
        }
        $wx['out_trade_no'] = $info['order_sn'];
        $wx['out_refund_no'] = $info['order_sn'];
        $wx['total_fee'] = $info['amount']*100;
        $wx['refund_fee'] = $info['amount']*100;
        $wx['op_user_id'] = $param['uid'];
        $response = \WxPayApi::refund($wx);

        if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
            $order->editOneRefund($info['id']); //修改order
            $contact = new Contacts();
            $contact->delContacts(['uid'=>$param['uid'],'to_uid'=>$param['to_uid']]);
            $refund = new Refund();
            $refund->save(['uid'=>$param['uid'],'trade_no'=>$response['refund_id'],'order_sn'=>$info['order_sn'],'money'=>$info['amount'],'editor'=>$param['editor'],'create_at'=>date('Y-m-d H:i:s')]);
            return $this->ajaxSuccess(20002, [], '退款成功');
        }
        return $this->ajaxError(20003);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-09
     *
     * @description 获取投诉列表
     * @param array $param
     * @return array
     */
    public function getComplainList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = [];

        if (!empty($param['start_time']) && !empty($param['end_time'])) {
            $where['create_at'] = ['between', [$param['start_time'],$param['end_time']]];
        }elseif (!empty($param['start_time'])) {
            $where['create_at'] = ['gt',$param['start_time']];
        }elseif (!empty($param['end_time'])) {
            $where['create_at'] = ['lt',$param['end_time']];
        }
        if (!empty($param['type'])) {
            $where['type'] = $param['type'];
        }

        $complain = new Complain();
        $list = $complain->getComplainList($where,$page,$size);
        if (!empty($list)) {
            $user = new User();
            $config = new Config();
            foreach ($list as $key => $value) {
                $one = $user->finds($value['uid'],'portrait,nickname,wechat,phone');
                $list[$key]['portrait'] = $one['portrait'];
                $list[$key]['nickname'] = $one['nickname'];
                $list[$key]['wechat'] = $one['wechat'];
                $list[$key]['phone'] = $one['phone'];
                $list[$key]['img'] = $value['img'] ? config('wechat.app_url').$value['img'] : '';
                $list[$key]['type'] = $config->getValue(['id'=>$value['type']],'namecn');
                $be = $user->finds($value['to_uid'],'wechat,phone,portrait,nickanme');
                $list[$key]['to_wechat'] = $be['wechat'];
                $list[$key]['to_phone'] = $be['phone'];
                $list[$key]['to_portrait'] = $be['portrait'];
                $list[$key]['to_nickname'] = $be['nickname'];
            }
        }
        return $this->ajaxSuccess(104,['list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 处理投诉
     * @param array $param
     * @return array
     */
    public function editComplain(array $param)
    {
        $com = new Complain();
        $a = $com->allowField('status')->save(['status'=>1],['id'=>$param['id']]);
        if ($a == 0) {
            return $this->ajaxError(112);
        }
        return $this->ajaxSuccess(102);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-19
     *
     * @description 切换角色
     * @param array $param
     * @return array
     */
    public function editRole(array $param)
    {
        $tutor = new Tutor();
        $learn = new Learn();
        $user = new User();
        if ($param['role'] == 1) {//家教变家长
            $detail = $tutor->getOne($param['uid']);
            $info = [
                'uid'=>$detail['uid'],
                'learn_range'=>'',
                'learn_subject'=>'',
                'tags'=>$detail['tags'],
                'remark'=>$detail['remark'],
                'is_order'=>0, // 若微信和身份通过则改为1
                'lng'=>$detail['lng'],
                'lat'=>$detail['lat'],
                'geo_hash'=>$detail['geo_hash'],
                'city'=>$detail['city'],
            ];
            $cer = new UserCer();
            $one = $user->getValue($param['uid'],'wechat_status');
            $school = $cer->getOne(['uid'=>$param['uid'],'type'=>2],'id'); //删除学历
            if (!empty($school)) {
                $cer->del($school['id']);
            }
            $cerInfo = new UserCerInfo();
            $cerInfo->del($param['uid']);
            $image = new UserImage();
            $image->where('uid',$param['uid'])->delete();
            $cerOne = $cer->getOne(['uid'=>$param['uid'],'type'=>1],'status');
            if ($one > 1 && !empty($cerOne) && $cerOne['status'] == 2) { //微信和身份通过
                $info['is_order'] = 1;
                $c = $user->save(['role'=>$param['role'],'certime'=>time()],['id'=>$param['uid']]);
            }elseif ($one == 1 || (!empty($cerOne) && $cerOne['status'] == 1)) { //不通过
                $c = $user->save(['role'=>$param['role'],'certime'=>1],['id'=>$param['uid']]);
            }else{
                $c = $user->save(['role'=>$param['role'],'certime'=>0],['id'=>$param['uid']]);
            }
            $a = $learn->save($info);
            $b = $tutor->delTutor(['uid'=>$param['uid']]);
        }else{ //家长变家教
            $detail = $learn->getOne($param['uid']);
            $info = [
                'uid'=>$detail['uid'],
                'teach_range'=> '',
                'teach_subject'=> '',
                'tags'=>$detail['tags'],
                'remark'=>$detail['remark'],
                'is_order'=>0,
                'lng'=>$detail['lng'],
                'lat'=>$detail['lat'],
                'geo_hash'=>$detail['geo_hash'],
                'city'=>$detail['city'],
            ];
            $a = $tutor->save($info);
            $b = $learn->delLearn(['uid'=>$param['uid']]);
            $one = $user->getValue($param['uid'],'certime');
            if ($one > 1){
                $c = $user->save(['role'=>$param['role'],'certime'=>0],['id'=>$param['uid']]); //还需要学历认证
            }else{
                $c = $user->save(['role'=>$param['role']],['id'=>$param['uid']]);
            }
        }
        if ($a && $b && $c) {
            return $this->ajaxSuccess(102);
        }
        return $this->ajaxError(112);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-12
     *
     * @description 获取哪几步审核失败
     * @param array $param
     * @return array
     */
    public function getFailStep(array $param){
        $list = [];
        $cer = new UserCer();
        $identify = $cer->getOne(['uid'=>$param['uid'],'type'=>1],'status'); //身份
        if (!empty($identify['status'])) {
            $list['identify'] = $identify['status'] == 2 ? '审核通过' : ($identify['status'] == 1 ? '审核不通过' : '未审核'); //身份
        }else {
            $list['identify'] = '未审核';
        }
        $user = new User();
        $role = $user->finds($param['uid'],'role,wechat_status');
        $list['wechat'] = $role['wechat_status'] > 1 ? '审核通过' : ($role['wechat_status'] == 1 ? '审核不通过' : '未审核'); //微信

        if ($role['role'] == 2) {
            $edu = $cer->getOne(['uid'=>$param['uid'],'type'=>2],'status');
            if (!empty($edu['status'])) {
                $list['education'] = $edu['status'] == 2 ? '审核通过' : ($edu['status'] == 1 ? '审核不通过' : '未审核'); //学历
            }else {
                $list['education'] = '未审核';
            }
        }
        return $list;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-26
     *
     * @description 上传图片
     * @param array $param
     * @return array
     */
    public function uploadPic(array $param)
    {
        $a = Utils::ossUpload64([$param['pic']],$param['type']);
        return $this->ajaxSuccess(101,['list'=>$a[0]]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-27
     *
     * @description 编辑用户信息
     * @param array $param
     * @return array
     */
    public function editUserInfo(array $param)
    {
        try{
            Db::startTrans();
            $user = new User();
            $role = $user->finds($param['uid'],'role,wechat,idcard,body_name,qrcode'); //判断原本角色
            if ($role['role'] != $param['role']) {
                $this->editRole(['uid'=>$param['uid'],'role'=>$param['role']]); //先修改角色
            }
            $c = new UserCer();
            //认证状态判断与修改
            if ($param['wechat'] != $role['wechat']) {
                $user->save(['wechat_status'=>time()],['id'=>$param['uid']]); //通过微信
            }
            if ($param['qrcode'] != $role['qrcode']) {
                $user->save(['qrcode_status'=>time()],['id'=>$param['uid']]); //通过二维码
            }
            if ($param['body_name'] != $role['body_name'] && ($param['idcard'] != $role['idcard'])) {
                $c->save(['status'=>2,'audittime'=>date('Y-m-d H:i:s')],['uid'=>$param['uid'],'type'=>1]); //通过身份
            }

            if ($param['role'] == 1) {
                $info = [
                    'learn_range'=>$param['range'],
                    'learn_subject'=>$param['subject'],
                ];
                $learn = new Learn();
                $learn->editLearn($info,$param['uid']); //修改需求

                //认证
                $role_new = $user->finds($param['uid'],'wechat_status');
                $auth = $c->getOne(['uid'=>$param['uid'],'type'=>1],'status');
                if ($role_new['wechat_status'] > 1 && !empty($auth) && $auth['status'] == 2) {
                    $certime = time();
                }elseif ($role_new['wechat_status'] == 1 || (!empty($auth) && $auth['status'] == 1)) {
                    $certime = 1;
                }else{
                    $certime = 0;
                }
            }else{
                $info = [
                    'teach_range' =>!empty($param['range']) ? $param['range'].',' : '',
                    'teach_subject' => !empty($param['subject']) ? $param['subject'].',' : '',
                ];
                $tutor = new Tutor();
                $tutor->editTutor($info,$param['uid']);
                //识别学历图
                if (!empty($_FILES)) {
                    $api_user = new \app\api\logic\UserLogic();
                    $api_user->Diplomacertification($param);
                    $c->save(['status'=>2,'audittime'=>date('Y-m-d H:i:s')],['uid'=>$param['uid'],'type'=>2]); //通过学历
                }
                $cer = new UserCerInfo();
                $cerOne = $cer->getOne(['uid'=>$param['uid']],'id');
                //修改专业信息
                if (!empty($cerOne)) {
                    $cer->save(['school'=>$param['school'],'diploma'=>$param['diploma'],'professional'=>$param['professional']],['uid'=>$param['uid']]);
                }else{
                    $cer->save(['school'=>$param['school'],'diploma'=>$param['diploma'],'professional'=>$param['professional'],'uid'=>$param['uid']]);
                }

                $role_new = $user->finds($param['uid'],'wechat_status');
                $auth = $c->getOne(['uid'=>$param['uid'],'type'=>1],'status');
                $auth2 = $c->getOne(['uid'=>$param['uid'],'type'=>2],'status');
                if ($role_new['wechat_status'] > 1 && !empty($auth) && $auth['status'] == 2 && !empty($auth2) && $auth2['status'] == 2) {
                    $certime = time();
                }elseif ($role_new['wechat_status'] == 1 || (!empty($auth) && $auth['status'] == 1) || (!empty($auth2) && $auth2['status'] == 1)) {
                    $certime = 1;
                }else{
                    $certime = 0;
                }
            }
            $userInfo = [
                'body_name' =>$param['body_name'],
                'head_name'=>mb_substr($param['body_name'],0,1,'utf8'),
                'idcard'=>$param['idcard'],
                'phone'=>$param['phone'],
                'qrcode'=>$param['qrcode'],
                'level' => $param['level'],
                'wechat' => $param['wechat'],
                'certime' => $certime,
            ];
            $user->save($userInfo,['id'=>$param['uid']]);
            Db::commit();
            return $this->ajaxSuccess(102);
        }catch (Exception $exception){
            Db::rollback();
            return $this->ajaxError(112,[],'修改信息失败');
        }
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-14
     *
     * @description 编辑备注
     * @param array $param
     * @return array
     */
    public function editRemark(array $param)
    {
        $user = new User();
        $user->save(['remark'=>$param['remark'],'quick_remark'=>$param['quick_remark']],['id'=>$param['uid']]);
        return $this->ajaxSuccess(102);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-10
     *
     * @description 创建二维码
     * @param array $param
     * @return array
     */
    public function createQrcode(array $param)
    {
        $wechat = new WechatService();
        $a = $wechat->getQrCode($param['scene'],$param['page'],430,0);
        return $this->ajaxSuccess(101,['list'=>$a]);
    }

//    public function loadUserInfo(array $params)
//    {
//        $userModel = new User();
//        $wxOpenid = $userModel->getValue($params['uid'],'openid');
//        if (!is_null($wxOpenid)) {
//            $wx = new WechatService();
//            $wxUserInfo = $wx->loadUserInfo($wxOpenid);
//            if ($wxUserInfo === false) {
//                $result = $this->ajaxError(205, [], '微信用户信息获取失败');
//            } else {
//                $data['wx_openid'] = $wxUserInfo['openid'];
//                $data['wx_nick_name'] = $wxUserInfo['nickname'];
//                $data['wx_avatar'] = $wxUserInfo['headimgurl'];
//                $bool = $userModel->save($data,['openid'=>$wxOpenid]);
//                if ($bool !== false) {
//                    $result = $this->ajaxSuccess(202, ['wx_userInfo' => $data]);
//                } else {
//                    $result = $this->ajaxError(205, [], '微信用户信息获取失败');
//                }
//            }
//        } else {
//            $result = $this->ajaxError(205, [], '不是微信注册用户');
//        }
//        return $result;
//    }
}