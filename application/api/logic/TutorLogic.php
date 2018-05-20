<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/23
 * Time: 上午11:56
 * @introduce
 */
namespace app\api\logic;


use app\api\model\Contacts;
use app\api\model\Favorite;
use app\api\model\Learn;
use app\api\model\Position;
use app\api\model\Tutor;
use app\api\model\User;
use app\api\model\UserCer;
use app\api\model\UserCerInfo;
use app\backend\model\Assistant;
use app\common\logic\BaseLogic;
use extend\service\Geohash;

class TutorLogic extends BaseLogic
{
    /**
     * @Author panhao
     * @DateTime
     *
     * @description 我的(家教)首页
     * @param array $param
     * @return array
     */
    public function getMyPage(array $param)
    {
        $user = new User();
        $userInfo = $user->userDetailAll($param['openid'],'portrait,id,body_name,wechat,idcard,sex,certime,rest_chance,last_share,nickname');
        if (empty($userInfo)) {
            return $this->ajaxError(114);
        }
        $cer = new UserCerInfo();
        $cerInfo = $cer->getOne(['uid'=>$userInfo['id']],'school,professional');
        $c = new UserCer();
        $cc = $c->getOne(['uid'=>$userInfo['id'],'type'=>2],'uid');
        if ($userInfo['certime'] == 0) { //未完善或审核中
            if (empty($userInfo['body_name']) || empty($userInfo['wechat']) || empty($userInfo['idcard']) || empty($cc)) {
                $userInfo['certime'] = -1; // 未完善
            }
        }
        $tutor = new Tutor();
        $favorite = new Favorite();
        $contacts = new Contacts();
        $userInfo['last_share'] = ($userInfo['last_share'] > date('Y-m-d')) ? 1 : 0; //最近分享
        $userInfo['body_name'] = $userInfo['body_name'] ?: $userInfo['nickname'];
        $userInfo['school'] = $cerInfo['school'];
        $userInfo['professional'] = $cerInfo['professional'];
        $userInfo['is_order'] = $tutor->getValue($userInfo['id'],'is_order');
        $info  = $tutor->getOne($userInfo['id'],'teach_range,teach_subject');
        $userInfo['need'] = (empty($info['teach_range']) && empty($info['teach_subject'])) ? 0 : 1;
        $userInfo['favorite_count'] = $favorite->getCount($userInfo['id']);
        $userInfo['contacts_count'] = $contacts->getCount($userInfo['id']);
        $userInfo['education'] = array_values(array_diff([$userInfo['school'], $userInfo['professional'], $userInfo['sex'] == 1 ? '男' : '女'], ['']));
        return $this->ajaxSuccess(104,['list'=>$userInfo]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 获取我的教学
     * @param int $uid
     * @return array
     */
    public function getMyNeeds(int $uid)
    {
        $info = [
            'teach_range' => config('teach.range'),
            'teach_subject' => config('teach.subject'),
            //'tags' => config('teach.tags'),
            'user' => [],
        ];
        $tutor = new Tutor();
        $user = $tutor->getOne($uid);
        if (!empty($user)) {
            $a = explode(',',$user['teach_range']);
            $b = explode(',',$user['teach_subject']);
            array_pop($a);array_pop($b);
            if (!empty($a)) {
                foreach ($a as $k => $v) {
                    $aa[$k] = intval($v);
                }
            }
            if (!empty($b)) {
                foreach ($b as $k => $v) {
                    $bb[$k] = intval($v);
                }
            }
            $user['teach_range'] = $aa ?? [];
            $user['teach_subject'] = $bb ?? [];
            $position = new Position();
            $name = $position->getOne(['uid'=>$uid,'status'=>1],'geo_name,lng,lat');
            $user['geo_name'] = $name['geo_name'] ?? '';
            $user['lng'] = $name['lng'] ?? '';
            $user['lat'] = $name['lat'] ?? '';
            $info['user'] = $user;
        }
        return $this->ajaxSuccess(104,['list'=>$info]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 保存家教-我的需求
     * @param array $param
     * @return array
     */
    public function saveMyNeeds(array $param)
    {
        $info = [
            'uid' => $param['uid'],
            'teach_range' => is_numeric($param['teach_range']) || !empty($param['teach_range']) ? $param['teach_range'].',' : '',
            'teach_subject' => is_numeric($param['teach_subject']) || !empty($param['teach_subject']) ? $param['teach_subject'].',' :'',
            'city' => $param['city'] ?? '',
            'lng' => $param['lng'],
            'lat' => $param['lat'],
            'geo_hash' => '',
        ];
        $geo = new Geohash();
        if ($info['lng'] && $info['lat']) {
            $info['geo_hash'] = $geo->encode($param['lat'], $param['lng']);
        }
        $tutor = new Tutor();
        $a = $tutor->editTutor($info,$param['uid']);
        $position = new Position();
        $list = ['lng'=>$param['lng'],'lat'=>$param['lat'],'geo_name'=>$param['geo_name'],'geo_hash'=>$info['geo_hash']];
        $b = $position->editPosition($list,['uid'=>$param['uid'],'status'=>1]);
        return $this->ajaxSuccess(102);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 家教列表
     * @param array $param
     * @return array
     */
    public function tutorList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 100;
        $user = new User();
        $tutor = new Tutor();
        $learn = new Learn();
        $teach = config('teach');
        $role = $user->getValue($param['uid'],'role');
        if ($role == 2) { //不匹配
            return $this->ajaxSuccess(104,['info'=>0,'list'=>[],'total'=>0,'role'=>2]);
        }
        $userInfo = $learn->getOne($param['uid']); //获取家长用户信息
        $cerInfo = new UserCerInfo();

        $where = 'teach_range != "" and teach_subject != "" and is_order = 1 and u.certime > 1 and t.city = "'.$userInfo['city'].'"';
        $where2 = $where;
        if ($userInfo['learn_range'] != '') {
            $where .= ' and teach_range like "%'.$userInfo['learn_range'].',%"'; //需考虑10，11
        }
        if ($userInfo['learn_subject'] != '') {
            $where .= ' and teach_subject like "%'.$userInfo['learn_subject'].',%"';
        }
        if ($userInfo['geo_hash'] != '') { //取方圆60公里
            $where .= ' and geo_hash like "%'.substr($userInfo['geo_hash'],0,2).'%"';
            $where2 .= ' and geo_hash like "%'.substr($userInfo['geo_hash'],0,2).'%"';
        }

        $contact = new Contacts();
        $ids = $contact->getUserList($param['uid'],'to_uid');
        if (!empty($ids)) {
            $ids = array_column($ids,'to_uid');
            $where .= ' and uid not in ('.implode(',',$ids).')';
            $where2 .= ' and uid not in ('.implode(',',$ids).')';
        }
        $field = 'u.*,t.id as tid,uid,teach_range,teach_subject,is_order,lng,lat,geo_hash,t.city as tcity';

        $field = $field.',(round(6367000 * 2 * asin(sqrt(pow(sin(((lat * pi()) / 180 - ('.$userInfo['lat'].' * pi()) / 180) / 2), 2) + cos(('.$userInfo['lat'].' * pi()) / 180) * cos((lat * pi()) / 180) * pow(sin(((lng * pi()) / 180 - ('.$userInfo['lng'].' * pi()) / 180) / 2), 2))))) AS distance';
        $field2 = $field;
        $count = $tutor->getTotal($where,$field,$where2);
        $list = $tutor->getPageList($where,$page,$size,$field,$field2,$where2);
        $favorites = new Favorite();
        $fav = $favorites->getList($param['uid'],'to_uid');
        $fav_ids = array_column($fav,'to_uid'); //收藏用户列表

        $ids = array_column($list,'uid');
        $portrait = $user->getInfoByIds(['id'=>['in',$ids]],'portrait,id,head_name,sex');
        foreach ($portrait as $k => $v) {
            $new[$v['id']]['portrait'] = $v['portrait'];
            $new[$v['id']]['head_name'] = $v['head_name'];
            $new[$v['id']]['sex'] = $v['sex'];
        }
        foreach ($list as $key => $value) {
            $cer = $cerInfo->getOne(['uid'=>$value['uid']],'school,professional'); //获取学历与专业
            $list[$key]['school'] = $cer['school'];
            $list[$key]['professional'] = $cer['professional'];

            $list[$key]['is_fav'] = (in_array($value['uid'],$fav_ids) ? 1 : 0);

            $range = explode(',',$value['teach_range']);

            $subject = explode(',',$value['teach_subject']);

            $list[$key]['teach_range'] = (!empty($userInfo['learn_range']) && in_array($userInfo['learn_range'],$range)) ? $teach['range'][$userInfo['learn_range']] : $teach['range'][current($range)];
            $list[$key]['teach_subject'] = (!empty($userInfo['learn_subject']) && in_array($userInfo['learn_subject'],$subject)) ? $teach['subject'][$userInfo['learn_subject']] : $teach['subject'][current($subject)];
            $list[$key]['block'] = array_values(array_diff([$list[$key]['teach_range'],$list[$key]['teach_subject']],['']));

            $list[$key]['portrait'] = $new[$value['uid']]['portrait'];
            $list[$key]['head_name'] = $new[$value['uid']]['head_name'].'老师';
            $list[$key]['sex'] = $new[$value['uid']]['sex'];
            $list[$key]['education'] = array_values(array_diff([$list[$key]['school'], $list[$key]['professional'], $list[$key]['sex'] == 1 ? '男': '女'], ['']));
            if ($value['distance'] < 1000) {
                $dis = '<1km';
            }else {
                $dis = intval($value['distance']/1000).'km';
            }
            $list[$key]['distance'] = $dis;
        }
//            //根据距离排序
//            if (!empty($userInfo['lat']) && !empty($userInfo['lng'])) {
//                foreach ($list as $key => $value) {
//                    $dis = $geo_hash->getDistance($userInfo['lat'],$userInfo['lng'],$value['lat'],$value['lng']);
//                    if ($dis < 1000) {
//                        $dis = '<1km';
//                    }elseif ($dis > 1000) {
//                        $dis = intval($dis/1000).'km';
//                    }
//                    $list[$key]['distance'] = $dis;
//                    $sort[$key] = $dis;
//                }
//                array_multisort($sort,SORT_ASC,$list);
//            }
        $ass = new Assistant();
        $assistant = $ass->getOne(['status'=>1],'qrcode');
        return $this->ajaxSuccess(104,['list'=>$list,'total'=>$count,'role'=>$role,'qrcode'=>config('oss.outer_host').$assistant['qrcode']]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-26
     *
     * @description 家教详情页
     * @param array $param
     * @return array
     */
    public function getTutorDetail(array $param)
    {
        $user = new User();
        $learn = new Learn();
        $tutor = new Tutor();
        $myInfo = $learn->getOne($param['from_uid']); //获取用户经纬度
        $my = $user->finds($param['from_uid'],'certime');
        $userInfo = $user->finds($param['uid'],'portrait,head_name,sex'); //家教头像名称等
        $info = $tutor->getOne($param['uid']); //家教需求
        $cerInfo = new UserCerInfo();
        $cer = $cerInfo->getOne(['uid'=>$param['uid']],'school,professional'); //获取学历与专业
        $userInfo['my_certime'] = $my['certime'] > 1 ? 2 : $my['certime']; //1未通过 2通过
        $userInfo['school'] = $cer['school'];
        $userInfo['professional'] = $cer['professional'];
        $userInfo['education'] = array_values(array_diff([$userInfo['school'], $userInfo['professional'], $userInfo['sex'] == 1 ? '男' : '女'], ['']));
        $userInfo['head_name'] = $userInfo['head_name'].'老师';
        $teach = config('teach');
        $range = explode(',',$info['teach_range']);
        array_pop($range);
        foreach ($range as $k => $v) {
            $new_range[$k] = $teach['range'][$v];
        }
        $subject = explode(',',$info['teach_subject']);
        array_pop($subject);
        foreach ($subject as $k => $v) {
            $new_subject[$k] = $teach['subject'][$v];
        }
        $userInfo['range'] = $new_range ?? [];
        $userInfo['subject'] = $new_subject ?? [];
        //$userInfo['tags'] = $info['tags'] ? array_filter(explode(',',$info['tags'])) : '';
        //$userInfo['remark'] = $info['remark'];
        $contact = new Contacts();
        $buy = $contact->getExist(['uid'=>$param['from_uid'],'to_uid'=>$param['uid']],'id');
        $userInfo['buy'] = (!empty($buy) ? 1 : 0); //1已购买
        $fav = new Favorite();
        $favorite = $fav->getExist(['uid'=>$param['from_uid'],'to_uid'=>$param['uid']]);
        $userInfo['is_fav'] = (!empty($favorite) ? 1 : 0); //1已关注

        $geo = new Geohash();
        $dis = $geo->getDistance($myInfo['lat'],$myInfo['lng'],$info['lat'],$info['lng']); //保留米
        if ($dis < 1000) {
            $userInfo['distance'] = '<1km';
        }elseif ($dis > 1000) {
            $userInfo['distance'] = intval($dis/1000).'km';
        }
        return $this->ajaxSuccess(104,['list'=>$userInfo]);
    }
}