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
        $field = 'uuid,SUM(ask_leave) as ask_leave,SUM(absent) as absent,SUM(cdzt) as cdzt,SUM(swjb) as swjb,
        SUM(zwjb) as zwjb,SUM(xwjb) as xwjb,SUM(wsjb) as wsjb,SUM(txjb) as txjb,SUM(zssb) as zssb,
        SUM(business_trip) as business_trip,SUM(train) as train,SUM(dute) as dute,SUM(rest) as rest';
        $list = $this->attendance->searchList($where,$field,$page,$size);
        if (!empty($list)) {
            $user = new User();
            foreach ($list as $k => $v) {
                $list[$k]['name'] = $user->searchValue(['uuid'=>$v['uuid']],'name');
            }
        }
        if (!empty($param['expor'])) {
            foreach ($list as $key => $value) {
                $new[$key]['uuid'] = $value['uuid'];
                $new[$key]['name'] = $value['name'];
                $new[$key]['ask_leave'] = $value['ask_leave'];
                $new[$key]['absent'] = $value['absent'];
                $new[$key]['cdzt'] = $value['cdzt'];
                $new[$key]['swjb'] = $value['swjb'];
                $new[$key]['zwjb'] = $value['zwjb'];
                $new[$key]['xwjb'] = $value['xwjb'];
                $new[$key]['wsjb'] = $value['wsjb'];
                $new[$key]['txjb'] = $value['txjb'];
                $new[$key]['zssb'] = $value['zssb'];
                $new[$key]['business_trip'] = $value['business_trip'];
                $new[$key]['train'] = $value['train'];
                $new[$key]['dute'] = $value['dute'];
                $new[$key]['rest'] = $value['rest'];
            }
            $expor = new ExcelLogic();
            return $expor->export(date('YmdHis'),$new,['ID','姓名','请假次数','旷工次数','迟到早退次数','上午加班','中午加班','下午加班','晚上加班','通宵加班','在所上班','出差','培训','值班','休息']);
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