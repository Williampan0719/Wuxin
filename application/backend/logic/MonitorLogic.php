<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/6/1
 * Time: 上午8:51
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Monitor;
use app\backend\model\User;
use app\common\logic\BaseLogic;

class MonitorLogic extends BaseLogic
{
    protected $monitor = null;

    public function __construct()
    {
        parent::__construct();
        $this->monitor = new Monitor();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-30
     *
     * @description 新增
     * @param array $param
     * @return array
     */
    public function addMonitor(array $param)
    {
        $result = $this->monitor->addOne($param);
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
    public function searchMonitor(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = [];
        if (!empty($param['start_time']) && !empty($param['end_time'])) {
            $where['create_at'] = ['between', [$param['start_time'], $param['end_time']]];
        } elseif (!empty($param['start_time'])) {
            $where['create_at'] = ['gt', $param['start_time']];
        } elseif (!empty($param['end_time'])) {
            $where['create_at'] = ['lt', $param['end_time']];
        }
        $field = 'room_id,SUM(sdzx) as sdzx,SUM(xwgf) as xwgf';
        $list = $this->monitor->searchList($where, $field, $page, $size);
        if (!empty($param['expor'])) {
            foreach ($list as $key => $value) {
                $new[$key]['room_id'] = $value['room_id'];
                $new[$key]['sdzx'] = $value['sdzx'];
                $new[$key]['xwgf'] = $value['xwgf'];
            }
            $expor = new ExcelLogic();
            return $expor->export(date('YmdHis'),$new,['房间号','时段秩序','行为规范']);
        }
        $count = $this->monitor->searchCount($where);
        return $this->ajaxSuccess(104, ['list' => $list, 'total' => $count]);
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
            $where['create_at'] = ['between', [$param['start_time'], $param['end_time']]];
        } elseif (!empty($param['start_time'])) {
            $where['create_at'] = ['gt', $param['start_time']];
        } elseif (!empty($param['end_time'])) {
            $where['create_at'] = ['lt', $param['end_time']];
        }
        if (!empty($where)) {
            $this->monitor->delList($where);
        }
        return $this->ajaxSuccess(103);

    }
}