<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/28
 * Time: 下午5:02
 * @introduce
 */
namespace app\api\logic;

use app\api\model\Learn;
use app\api\model\Position;
use app\api\model\Tutor;
use app\api\model\User;
use app\common\logic\BaseLogic;
use extend\service\Geohash;

class PositionLogic extends BaseLogic
{
    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 获取地址列表
     * @param array $param
     * @return array
     */
    public function getPositionList(array $param)
    {
        $user = new User();
        $info = $user->finds($param['uid'],'body_name,phone');
        $position = new Position();
        $result = $position->getPositionList($param['uid']);
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $result[$key]['geo_name'] = $value['geo_name'] ?? '暂无';
                $result[$key]['name'] = $info['body_name'];
                $result[$key]['phone'] = $info['phone'];
            }
        }
        return $this->ajaxSuccess(104,['list'=>$result]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-28
     *
     * @description 添加新地址
     * @param array $param
     * @return array
     */
    public function addPosition(array $param)
    {
        $position = new Position();
        $geo = new Geohash();
        $param['geo_hash'] = $geo->encode($param['lat'],$param['lng']);

        $p = $position->getOne(['uid'=>$param['uid'],'geo_name'=>['neq','']]); //判断是不是第一条
        if (empty($p)) {
            $param['status'] = 1; //第一条新增为默认
            $user = new User();
            $one = $user->finds($param['uid'],'role');
            $a = 0;
            if ($one['role'] == 1) {
                $learn = new Learn();
                $a = $learn->editLearn(['lng'=>$param['lng'],'lat'=>$param['lat'],'geo_hash'=>$param['geo_hash'],'city'=>$param['city']],$param['uid']);
            }elseif ($one['role'] == 2) {
                $tutor = new Tutor();
                $a = $tutor->editTutor(['lng'=>$param['lng'],'lat'=>$param['lat'],'geo_hash'=>$param['geo_hash'],'city'=>$param['city']],$param['uid']);
            }
            if ($a == 0) {
                return $this->ajaxError(112);
            }
            $p2 = $position->getOne(['uid'=>$param['uid']]);
            if ($p2) {
                $position->delPosition($p2['id']);
            }
        }

        $result = $position->addPosition($param);
        if ($result == 1) {
            return $this->ajaxSuccess(101);
        }
        return $this->ajaxError(111);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-28
     *
     * @description 编辑与修改默认
     * @param array $param
     * @return array
     */
    public function editPosition(array $param)
    {
        $position = new Position();
        $geo = new Geohash();
        $param['geo_hash'] = $geo->encode($param['lat'],$param['lng']);
        $result = $position->editPosition($param,['id'=>$param['id']]);
        $a = $position->getOne(['id'=>$param['id']],'status');
        if ($a['status'] == 1) { //修改默认地址内容
            $user = new User();
            $one = $user->finds($param['uid'],'role');
            $a = 0;
            if ($one['role'] == 1) {
                $learn = new Learn();
                $a = $learn->editLearn(['lng'=>$param['lng'],'lat'=>$param['lat'],'geo_hash'=>$param['geo_hash'],'city'=>$param['city']],$param['uid']);
            }elseif ($one['role'] == 2) {
                $tutor = new Tutor();
                $a = $tutor->editTutor(['lng'=>$param['lng'],'lat'=>$param['lat'],'geo_hash'=>$param['geo_hash'],'city'=>$param['city']],$param['uid']);
            }
            if ($a == 0) {
                return $this->ajaxError(112);
            }
        }
        if ($result == 1) {
            return $this->ajaxSuccess(102);
        }
        return $this->ajaxError(112);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 删除地址
     * @param array $param
     * @return array
     */
    public function delPosition(array $param)
    {
        $position = new Position();
        $one = $position->getOne(['id'=>$param['id']],'status');
        if ($one['status'] == 1) {
            $first = $position->getOne(['uid'=>$param['uid']],'id,lng,lat,geo_hash,city','id asc');
            if (!empty($first)) {
                $position->editPosition(['status'=>1],['id'=>$first['id']]); //默认第一条为默认地址
            }
            $user = new User();
            $one = $user->finds($param['uid'],'role'); //获取角色
            $where = !empty($first) ? ['lng'=>$first['lng'],'lat'=>$first['lat'],'geo_hash'=>$first['geo_hash'],'city'=>$first['city']] : ['lng'=>0,'lat'=>0,'geo_hash'=>'','city'=>''];
            if ($one['role'] == 1) {
                $learn = new Learn();
                $a = $learn->editLearn($where,$param['uid']);
            }elseif ($one['role'] == 2) {
                $tutor = new Tutor();
                $a = $tutor->editTutor($where,$param['uid']);
            }
        }
        $result = $position->delPosition($param['id']);
        if ($result == 1) {
            return $this->ajaxSuccess(103);
        }
        return $this->ajaxError(113);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 设为默认
     * @param array $param
     * @return array
     */
    public function editPositionStatus(array $param)
    {
        $position = new Position();
        $p = $position->getOne(['id'=>$param['id']],'lng,lat,geo_hash,city');
        $position->editPositionStatus(['status'=>0],['uid'=>$param['uid']]); //全部取消默认
        $position->editPositionStatus(['status'=>1],['id'=>$param['id']]); //设为默认
        $user = new User();
        $one = $user->finds($param['uid'],'role');
        $a = 0;
        if ($one['role'] == 1) {
            $learn = new Learn();
            $a = $learn->editLearn(['lng'=>$p['lng'],'lat'=>$p['lat'],'geo_hash'=>$p['geo_hash'],'city'=>$p['city']],$param['uid']);
        }elseif ($one['role'] == 2) {
            $tutor = new Tutor();
            $a = $tutor->editTutor(['lng'=>$p['lng'],'lat'=>$p['lat'],'geo_hash'=>$p['geo_hash'],'city'=>$p['city']],$param['uid']);
        }
        if ($a == 1) {
            return $this->ajaxSuccess(102);
        }
        return $this->ajaxError(112);
    }
}