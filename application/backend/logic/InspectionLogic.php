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
     * @apiParam {string} uuid 警员id
     * @apiParam {float} wgkf 违规扣分
     * @apiParam {float} blwg 八类违规
     * @apiParam {float} jqfx 警情分析
     * @apiParam {float} yjsb 硬件设备
     * @apiParam {float} dmjb 点名交班
     * @apiParam {float} hbdj 患病登记
     * @apiParam {float} jjjl 进监记录
     * @apiParam {float} xyjl 寻医记录
     * @apiParam {float} xsjc 巡视检查
     * @apiParam {float} xcff 巡查放风
     * @apiParam {float} zbtg 值班脱岗
     * @apiParam {float} ksqk 考试情况
     * @apiParam {float} fxyh 发现隐患
     * @apiParam {float} dtjl 动态记录
     * @apiParam {float} zbxx 值班休息
     * @apiParam {float} dbjd 带班监督
     * @apiParam {float} else 其他
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
        $field = 'uuid,SUM(wgkf) as wgkf,SUM(blwg) as blwg,SUM(jqfx) as jqfx,SUM(yjsb) as yjsb,SUM(dmjb) as dmjb,SUM(hbdj) as hbdj,SUM(jjjl) as jjjl,SUM(xyjl) as xyjl,SUM(xsjc) as xsjc,SUM(xcff) as xcff,SUM(zbtg) as zbtg,SUM(ksqk) as ksqk,SUM(fxyh) as fxyh,SUM(dtjl) as dtjl,SUM(zbxx) as zbxx,SUM(dbjd) as dbjd,SUM(`else`) as `else`';
        $list = $this->inspection->searchList($where, $field, $page, $size);
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