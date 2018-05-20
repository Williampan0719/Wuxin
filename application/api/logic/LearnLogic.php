<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/26
 * Time: 上午9:49
 * @introduce
 */
namespace app\api\logic;


use app\api\model\Contacts;
use app\api\model\Favorite;
use app\api\model\Learn;
use app\api\model\Position;
use app\api\model\Tutor;
use app\api\model\User;
use app\backend\model\Assistant;
use app\common\logic\BaseLogic;
use extend\service\Geohash;

class LearnLogic extends BaseLogic
{
    /**
     * @Author panhao
     * @DateTime 2018-03-26
     *
     * @description 我的(家长)首页
     * @param string $openid
     * @return array
     */
    public function getMyPage(string $openid)
    {
        $user = new User();
        $userInfo = $user->userDetailAll($openid,'portrait,id,body_name,wechat,idcard,sex,certime,rest_chance,last_share,nickname');
        if (empty($userInfo)) {
            return $this->ajaxError(114);
        }
        if ($userInfo['certime'] == 0) { //未完善或审核中
            if (empty($userInfo['body_name']) || empty($userInfo['wechat']) || empty($userInfo['idcard'])) {
                $userInfo['certime'] = -1; // 未完善
            }
        }
        $favorite = new Favorite();
        $contacts = new Contacts();
        $learn = new Learn();
        $info  = $learn->getOne($userInfo['id'],'learn_range,learn_subject');
        $userInfo['need'] = (empty($info['learn_range']) && empty($info['learn_subject'])) ? 0 : 1;
        $userInfo['last_share'] = ($userInfo['last_share'] > date('Y-m-d')) ? 1 : 0; //最近分享
        $userInfo['body_name'] = $userInfo['body_name'] ?: $userInfo['nickname'];
        $userInfo['is_order'] = $learn->getValue($userInfo['id'],'is_order');
        $userInfo['favorite_count'] = $favorite->getCount($userInfo['id']);
        $a = $contacts->getCount($userInfo['id']);
        $b = $contacts->getCount2($userInfo['id']);
        $userInfo['contacts_count'] = $a+$b;
        return $this->ajaxSuccess(104,['list'=>$userInfo]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-26
     *
     * @description 获取我的需求
     * @param int $uid
     * @return array
     */
    public function getMyNeeds(int $uid)
    {
        $info = [
            'learn_range' => config('teach.range'),
            'learn_subject' => config('teach.subject'),
            //'tags' => config('teach.tags'),
            'user' => [],
        ];
        $learn = new Learn();
        $user = $learn->getOne($uid);
        if (!empty($user)) {
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
     * @DateTime 2018-03-26
     *
     * @description 保存家长-我的需求
     * @param array $param
     * @return array
     */
    public function saveMyNeeds(array $param)
    {
        $info = [
            'uid' => $param['uid'],
            'learn_range' => is_numeric($param['learn_range']) ? $param['learn_range'] : '',
            'learn_subject' => is_numeric($param['learn_subject']) ? $param['learn_subject'] : '',
            'city'=>$param['city'],
            'lng' => $param['lng'],
            'lat' => $param['lat'],
            'geo_hash' => '',
        ];
        $geo = new Geohash();
        if ($info['lng'] && $info['lat']) {
            $info['geo_hash'] = $geo->encode($param['lat'], $param['lng']);
        }
        $learn = new Learn();
        $a = $learn->editLearn($info,$param['uid']);
        $position = new Position();
        $list = ['lng'=>$param['lng'],'lat'=>$param['lat'],'geo_name'=>$param['geo_name'],'geo_hash'=>$info['geo_hash']];
        $b = $position->editPosition($list,['uid'=>$param['uid'],'status'=>1]);
        return $this->ajaxSuccess(102);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 家长列表
     * @param array $param
     * @return array
     */
    public function learnList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 100;
        $user = new User();
        $tutor = new Tutor();
        $learn = new Learn();
        $teach = config('teach');
        $userInfo = $tutor->getOne($param['uid']);
        $role = $user->getValue($param['uid'],'role');
        if ($role == 1) { //不匹配
            return $this->ajaxSuccess(104,['info'=>0,'list'=>[],'total'=>0,'role'=>1]);
        }
        $where = 'learn_range != "" and learn_subject != "" and is_order = 1 and u.certime > 1 and l.city = "'.$userInfo['city'].'"';
        $where2 = $where;

        //有需求就匹配，无需求就随机
        if ($userInfo['teach_range'] != '') {
            $userInfo['teach_range'] = substr($userInfo['teach_range'],0,strlen($userInfo['teach_range'])-1);
            $where .= ' and learn_range in ('.$userInfo['teach_range'].')';
        }
        if ($userInfo['teach_subject'] != '') {
            $userInfo['teach_subject'] = substr($userInfo['teach_subject'],0,strlen($userInfo['teach_subject'])-1);
            $where .= ' and learn_subject in ('.$userInfo['teach_subject'].')';
        }
        if ($userInfo['geo_hash'] != '') {
            $where .= ' and geo_hash like "'.substr($userInfo['geo_hash'],0,2).'%"';
            $where2 .= ' and geo_hash like "'.substr($userInfo['geo_hash'],0,2).'%"';
        }

        $contact = new Contacts(); //排除已购买的
        $ids = $contact->getUserList($param['uid'],'to_uid');
        if (!empty($ids)) {
            $ids = array_column($ids,'to_uid');
            $where .= ' and uid not in ('.implode(',',$ids).')';
            $where2 .= ' and uid not in ('.implode(',',$ids).')';
        }
        $field = 'u.*,l.id as lid,uid,learn_range,learn_subject,is_order,lng,lat,geo_hash,l.city as lcity';

        $field = $field.',(round(6367000 * 2 * asin(sqrt(pow(sin(((lat * pi()) / 180 - ('.$userInfo['lat'].' * pi()) / 180) / 2), 2) + cos(('.$userInfo['lat'].' * pi()) / 180) * cos((lat * pi()) / 180) * pow(sin(((lng * pi()) / 180 - ('.$userInfo['lng'].' * pi()) / 180) / 2), 2))))) AS distance';
        $field2 = $field;
        $count = $learn->getTotal($where,$field,$where2);
        $list = $learn->getPageList($where,$page,$size,$field,$field2,$where2);
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
            $list[$key]['is_fav'] = (in_array($value['uid'],$fav_ids) ? 1 : 0);
            $list[$key]['learn_range'] = $teach['range'][$value['learn_range']] ?? '';
            $list[$key]['learn_subject'] = $teach['subject'][$value['learn_subject']] ?? '';
            $list[$key]['block'] = array_values(array_diff([$list[$key]['learn_range'],$list[$key]['learn_subject']],['']));
            $list[$key]['portrait'] = $new[$value['uid']]['portrait'];
            $list[$key]['head_name'] = ($new[$value['uid']]['head_name'] ?: '?').($new[$value['uid']]['sex'] == 1 ? '先生':'女士');
            $list[$key]['sex'] = $new[$value['uid']]['sex'];
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
     * @description 家长详情页
     * @param array $param
     * @return array
     */
    public function getLearnDetail(array $param)
    {
        $user = new User();
        $learn = new Learn();
        $tutor = new Tutor();
        $myInfo = $tutor->getOne($param['from_uid']); //获取用户经纬度
        $my = $user->finds($param['from_uid'],'certime');
        $userInfo = $user->finds($param['uid'],'portrait,head_name,sex'); //家长头像名称等
        $info = $learn->getOne($param['uid']); //家长需求
        $teach = config('teach');
        $userInfo['my_certime'] = $my['certime'] > 1 ? 2 : $my['certime']; //1未通过 2通过
        $userInfo['head_name'] = ($userInfo['head_name'] ?: '?').($userInfo['sex'] == 1 ? '先生': '女士');
        $userInfo['range'] = $teach['range'][$info['learn_range']];
        $userInfo['subject'] = $teach['subject'][$info['learn_subject']];
        //$userInfo['tags'] = $info['tags'] ? array_filter(explode(',',$info['tags'])) : [];
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

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 联系人列表
     * @param array $param
     * @return array
     */
    public function getContactsList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 100;
        $contacts = new Contacts();
        $where = 'uid = '.$param['uid'];
        $where2 = 'to_uid = '.$param['uid'];
        $list = $contacts->getPageList($where,$page,$size);
        $list2 = $contacts->getPageList($where2,$page,$size);
        if (!empty($list)) {
            $uids = array_column($list,'to_uid');
            $user = new User();
            $userInfo = $user->getInfoByIds(['id' => ['in', $uids]], 'portrait,id,head_name,phone,sex,role,wechat');
            $new = [];
            foreach ($userInfo as $k => $v) {
                $new[$v['id']] = $v;
            }
            foreach ($list as $key => $value) {
                $list[$key]['portrait'] = $new[$value['to_uid']]['portrait'];
                $list[$key]['wechat'] = $new[$value['to_uid']]['wechat'];
                $list[$key]['phone'] = $new[$value['to_uid']]['phone'];
                $list[$key]['buy'] = 1;
                $list[$key]['head_name'] = $new[$value['to_uid']]['head_name'].($new[$value['to_uid']]['role'] == 2?'老师':($new[$value['to_uid']]['sex'] == 1 ?'先生':'女士'));
            }
        }
        if (!empty($list2)) {
            $uids = array_column($list2,'uid');
            $user = new User();
            $userInfo = $user->getInfoByIds(['id' => ['in', $uids]], 'portrait,id,head_name,phone,sex,role,wechat');
            $new2 = [];
            foreach ($userInfo as $k => $v) {
                $new2[$v['id']] = $v;
            }
            foreach ($list2 as $key2 => $value2) {
                $list2[$key2]['portrait'] = $new2[$value2['uid']]['portrait'];
                $list2[$key2]['wechat'] = $new2[$value2['uid']]['wechat'];
                $list2[$key2]['phone'] = $new2[$value2['uid']]['phone'];
                $list2[$key2]['buy'] = 1;
                $list2[$key2]['head_name'] = $new2[$value2['uid']]['head_name'].($new2[$value2['uid']]['role'] == 2?'老师':($new2[$value2['uid']]['sex'] == 1 ?'先生':'女士'));
            }
        }
        return $this->ajaxSuccess(104,['list'=>$list,'list2'=>$list2]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 收藏列表
     * @param array $param
     * @return array
     */
    public function getFavoritesList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $favorite = new Favorite();
        $where = ['uid'=>$param['uid']];
        $count = $favorite->getCount($param['uid']);
        $list = $favorite->getPageList($where,$page,$size);
        if (!empty($list)) {
            $uids = array_column($list, 'to_uid');
            $user = new User();
            $userInfo = $user->getInfoByIds(['id' => ['in', $uids]], 'portrait,id,head_name,sex,role');
            $new = [];
            foreach ($userInfo as $key => $value) {
                $new[$value['id']] = $value;
            }
            foreach ($list as $key => $value) {
                $list[$key]['portrait'] = $new[$value['to_uid']]['portrait'];
                $list[$key]['head_name'] = $new[$value['to_uid']]['head_name'].($new[$value['to_uid']]['role'] == 2?'老师':($new[$value['to_uid']]['sex'] == 1 ?'先生':'女士'));
            }
        }
        return $this->ajaxSuccess(104,['list'=>$list,'total'=>$count]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 删除联系人
     * @param array $param
     * @return array
     */
    public function delContacts(array $param)
    {
        $contacts = new Contacts();
        $result = $contacts->delContacts(['id'=>$param['id']]);
        if ($result == 0) {
            return $this->ajaxError(113);
        }
        return $this->ajaxSuccess(103);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 删除收藏
     * @param array $param
     * @return array
     */
    public function delFavorites(array $param)
    {
        $favorites = new Favorite();
        $result = $favorites->delFavorites(['uid'=>$param['uid'],'to_uid'=>$param['to_uid']]);
        if ($result == 0) {
            return $this->ajaxError(113);
        }
        return $this->ajaxSuccess(103);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 添加收藏
     * @param array $param
     * @return array
     */
    public function addFavorites(array $param)
    {
        $favorites = new Favorite();
        $where = ['uid'=>$param['uid'],'to_uid'=>$param['to_uid']];
        $find = $favorites->getExist($where);
        if ($find > 0) {
            return $this->ajaxError(111,[],'您已收藏此用户');
        }
        $result = $favorites->addFavorites($where);
        if ($result == 0) {
            return $this->ajaxError(111);
        }
        return $this->ajaxSuccess(101);
    }
}