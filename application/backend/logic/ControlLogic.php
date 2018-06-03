<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/6/1
 * Time: 上午8:51
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Control;
use app\backend\model\User;
use app\common\logic\BaseLogic;

class ControlLogic extends BaseLogic
{
    protected $control = null;

    public function __construct()
    {
        parent::__construct();
        $this->control = new Control();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-30
     *
     * @description 新增
     * @param array $param
     * @return array
     */
    public function addControl(array $param)
    {
        $result = $this->control->addOne($param);
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
    public function searchControl(array $param)
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
        $field = 'uuid,SUM(swxc) as swxc,SUM(swjjs) as swjjs,SUM(swsdw) as swsdw,SUM(xwxc) as xwxc,
        SUM(xwjjs) as xwjjs,SUM(xwsdw) as xwsdw,SUM(laq) as laq';
        $list = $this->control->searchList($where, $field, $page, $size);
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
                $new[$key]['swxc'] = $value['swxc'];
                $new[$key]['swjjs'] = $value['swjjs'];
                $new[$key]['swsdw'] = $value['swsdw'];
                $new[$key]['xwxc'] = $value['xwxc'];
                $new[$key]['xwjjs'] = $value['xwjjs'];
                $new[$key]['xwsdw'] = $value['xwsdw'];
                $new[$key]['laq'] = $value['laq'];
            }
            $expor = new ExcelLogic();
            return $expor->export(date('YmdHis'),$new,['ID','姓名','上午巡查','上午进监室','上午三定位','下午巡查','下午进监室','下午三定位','六安全']);
        }
        $count = $this->control->searchCount($where);
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
            $this->control->delList($where);
        }
        return $this->ajaxSuccess(103);

    }
}