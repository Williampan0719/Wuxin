<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/30
 * Time: 下午8:25
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Attendance;
use app\backend\model\User;
use app\common\logic\BaseLogic;

class AttendanceLogic extends BaseLogic
{
    protected $attendance = null;

    public function __construct()
    {
        parent::__construct();
        $this->attendance = new Attendance();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-30
     *
     * @description 新增
     * @param array $param
     * @return array
     */
    public function addAttendance(array $param)
    {
        $result = $this->attendance->addOne($param);
        if ($result == 0) {
            return $this->ajaxError(111);
        }
        return $this->ajaxSuccess(101);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-30
     *
     * @description 搜索
     * @param array $param
     * @return array
     */
    public function searchAttendance(array $param)
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
        $field = 'uuid,SUM(ask_leave) as ask_leave,SUM(absent) as absent,SUM(cdzt) as cdzt,SUM(swjb) as swjb,SUM(zwjb) as zwjb,SUM(xwjb) as xwjb,SUM(wsjb) as wsjb,SUM(txjb) as txjb,SUM(zssb) as zssb,SUM(business_trip) as business_trip,SUM(train) as train,SUM(dute) as dute,SUM(rest) as rest';
        $list = $this->attendance->searchList($where,$field,$page,$size);
        if (!empty($list)) {
            $user = new User();
            foreach ($list as $k => $v) {
                $list[$k]['name'] = $user->searchValue(['uuid'=>$v['uuid']],'name');
            }
        }
        $count = $this->attendance->searchCount($where);
        return $this->ajaxSuccess(104,['list'=>$list,'total'=>$count]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-31
     *
     * @description 删除
     * @param array $param
     * @return array
     */
    public function deleteList(array $param)
    {
        $where = [];
        if (!empty($param['start_time']) && !empty($param['end_time'])) {
            $where['create_at'] = ['between', [$param['start_time'],$param['end_time']]];
        }elseif (!empty($param['start_time'])) {
            $where['create_at'] = ['gt',$param['start_time']];
        }elseif (!empty($param['end_time'])) {
            $where['create_at'] = ['lt',$param['end_time']];
        }
        if (!empty($where)) {
            $this->attendance->delList($where);
        }
        return $this->ajaxSuccess(103);

    }
}