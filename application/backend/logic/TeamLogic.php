<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/6/1
 * Time: 下午8:25
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Team;
use app\common\logic\BaseLogic;

class TeamLogic extends BaseLogic
{
    protected $team = null;

    public function __construct()
    {
        parent::__construct();
        $this->team = new Team();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-30
     *
     * @description 新增
     * @param array $param
     * @return array
     */
    public function addTeam(array $param)
    {
        $result = $this->team->addOne($param);
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
    public function searchTeam(array $param)
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
        $list = $this->team->searchList($where,$field,$page,$size);
        $count = $this->team->searchCount($where);
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
            $this->team->delList($where);
        }
        return $this->ajaxSuccess(103);

    }
}