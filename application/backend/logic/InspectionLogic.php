<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/6/1
 * Time: 上午8:51
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Inspection;
use app\backend\model\User;
use app\common\logic\BaseLogic;

class InspectionLogic extends BaseLogic
{
    protected $inspection = null;

    public function __construct()
    {
        parent::__construct();
        $this->inspection = new Inspection();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-30
     *
     * @description 新增
     * @param array $param
     * @return array
     */
    public function addInspection(array $param)
    {
        $result = $this->inspection->addOne($param);
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
    public function searchInspection(array $param)
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
        $field = 'uuid,SUM(wgkf) as wgkf,SUM(blwg) as blwg,SUM(jqfx) as jqfx,SUM(yjsb) as yjsb,
        SUM(dmjb) as dmjb,SUM(hbdj) as hbdj,SUM(jjjl) as jjjl,SUM(xyjl) as xyjl,SUM(xsjc) as xsjc,
        SUM(xcff) as xcff,SUM(zbtg) as zbtg,SUM(ksqk) as ksqk,SUM(fxyh) as fxyh,SUM(dtjl) as dtjl,
        SUM(zbxx) as zbxx,SUM(dbjd) as dbjd,SUM(`else`) as `else`';
        $list = $this->inspection->searchList($where, $field, $page, $size);
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
                $new[$key]['wgkf'] = $value['wgkf'];
                $new[$key]['blwg'] = $value['blwg'];
                $new[$key]['jqfx'] = $value['jqfx'];
                $new[$key]['yjsb'] = $value['yjsb'];
                $new[$key]['dmjb'] = $value['dmjb'];
                $new[$key]['hbdj'] = $value['hbdj'];
                $new[$key]['jjjl'] = $value['jjjl'];
                $new[$key]['xyjl'] = $value['xyjl'];
                $new[$key]['xsjc'] = $value['xsjc'];
                $new[$key]['xcff'] = $value['xcff'];
                $new[$key]['zbtg'] = $value['zbtg'];
                $new[$key]['ksqk'] = $value['ksqk'];
                $new[$key]['fxyh'] = $value['fxyh'];
                $new[$key]['dtjl'] = $value['dtjl'];
                $new[$key]['zbxx'] = $value['zbxx'];
                $new[$key]['dbjd'] = $value['dbjd'];
                $new[$key]['else'] = $value['else'];
            }
            $expor = new ExcelLogic();
            return $expor->export(date('YmdHis'),$new,['ID','姓名','违规扣分','八类违规','警情分析','硬件设备','点名交班','患病登记','进监记录','巡医记录','巡视检查','巡查放风','值班脱岗','考试情况','发现隐患','动态记录','值班休息','带班监督','其他']);
        }
        $count = $this->inspection->searchCount($where);
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
            $this->inspection->delList($where);
        }
        return $this->ajaxSuccess(103);

    }
}