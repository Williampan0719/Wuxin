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
     * @apiParam {string} uuid 警员id
     * @apiParam {float} work 上班
     * @apiParam {float} overtime 加班
     * @apiParam {float} late_early 迟到早退
     * @apiParam {float} honor 光荣榜
     * @apiParam {float} ask_leave 上班请假
     * @apiParam {float} absent 旷工
     * @apiParam {float} zbqj 值班请假
     * @apiParam {float} zlh 早列会
     * @apiParam {float} qshyxd 全所会议行动
     * @apiParam {float} sbzqt 上班做其他
     * @apiParam {float} jrfj 警容风纪
     * @apiParam {float} txbc 通讯不畅
     * @apiParam {float} nwws 内务卫生
     * @apiParam {float} dmsb 灯门设备没关
     * @apiParam {float} qzts 群众投诉
     * @apiParam {float} dmqz 打骂群众
     * @apiParam {float} ndzy 虐待在押人员
     * @apiParam {float} fxqxy 服刑区吸烟
     * @apiParam {float} aqjc 安全检查
     * @apiParam {float} shys 损坏遗失
     * @apiParam {float} kjsth 跨监室谈话
     * @apiParam {float} dctb 督查通报
     * @apiParam {float} yl 演练
     * @apiParam {float} xxcl 学习材料
     * @apiParam {float} wx 外宣
     * @apiParam {float} xxlr 信息录入
     * @apiParam {float} else 其他
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
        $field = 'uuid,SUM(work) as work,SUM(overtime) as overtime,SUM(late_early) as late_early,SUM(honor) as honor,
        SUM(ask_leave) as ask_leave,SUM(absent) as absent,SUM(zbqj) as zbqj,SUM(zlh) as zlh,SUM(qshyxd) as qshyxd,SUM(sbzqt) as sbzqt,
        SUM(jrfj) as jrfj,SUM(txbc) as txbc,SUM(nwws) as nwws,SUM(dmsb) as dmsb,SUM(qzts) as qzts,SUM(dmqz) as dmqz,SUM(ndzy) as ndzy,
        SUM(fxqxy) as fxqxy,SUM(aqjc) as aqjc,SUM(shys) as shys,SUM(kjsth) as kjsth,SUM(dctb) as dctb,SUM(yl) as yl,SUM(xxcl) as xxcl,
        SUM(xxcl) as xxcl,SUM(wx) as wx,SUM(`else`) as `else`';
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