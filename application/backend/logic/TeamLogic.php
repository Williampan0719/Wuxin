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
use app\backend\model\User;
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
        $field = 'uuid,SUM(work) as work,SUM(overtime) as overtime,SUM(late_early) as late_early,SUM(honor) as honor,
        SUM(ask_leave) as ask_leave,SUM(absent) as absent,SUM(zbqj) as zbqj,SUM(zlh) as zlh,SUM(qshyxd) as qshyxd,SUM(sbzqt) as sbzqt,
        SUM(jrfj) as jrfj,SUM(txbc) as txbc,SUM(nwws) as nwws,SUM(dmsb) as dmsb,SUM(qzts) as qzts,SUM(dmqz) as dmqz,SUM(ndzy) as ndzy,
        SUM(fxqxy) as fxqxy,SUM(aqjc) as aqjc,SUM(shys) as shys,SUM(kjsth) as kjsth,SUM(dctb) as dctb,SUM(yl) as yl,SUM(xxcl) as xxcl,
        SUM(wx) as wx,SUM(xxlr) as xxlr,SUM(`else`) as `else`';
        $list = $this->team->searchList($where,$field,$page,$size);
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
                $new[$key]['work'] = $value['work'];
                $new[$key]['overtime'] = $value['overtime'];
                $new[$key]['late_early'] = $value['late_early'];
                $new[$key]['honor'] = $value['honor'];
                $new[$key]['ask_leave'] = $value['ask_leave'];
                $new[$key]['absent'] = $value['absent'];
                $new[$key]['zbqj'] = $value['zbqj'];
                $new[$key]['zlh'] = $value['zlh'];
                $new[$key]['qshyxd'] = $value['qshyxd'];
                $new[$key]['sbzqt'] = $value['sbzqt'];
                $new[$key]['jrfj'] = $value['jrfj'];
                $new[$key]['txbc'] = $value['txbc'];
                $new[$key]['nwws'] = $value['nwws'];
                $new[$key]['dmsb'] = $value['dmsb'];
                $new[$key]['qzts'] = $value['qzts'];
                $new[$key]['dmqz'] = $value['dmqz'];
                $new[$key]['ndzy'] = $value['ndzy'];
                $new[$key]['fxqxy'] = $value['fxqxy'];
                $new[$key]['aqjc'] = $value['aqjc'];
                $new[$key]['shys'] = $value['shys'];
                $new[$key]['kjsth'] = $value['kjsth'];
                $new[$key]['dctb'] = $value['dctb'];
                $new[$key]['yl'] = $value['yl'];
                $new[$key]['xxcl'] = $value['xxcl'];
                $new[$key]['wx'] = $value['wx'];
                $new[$key]['xxlr'] = $value['xxlr'];
                $new[$key]['else'] = $value['else'];
            }
            $expor = new ExcelLogic();
            return $expor->export(date('YmdHis'),$new,['ID','姓名','上班','加班','迟到早退','光荣榜','上班请假','旷工','值班请假','早列会','全所会议行动','上班做其他','警容风纪','通讯不畅','内务卫生','灯门设备','群众投诉','打骂群众','虐待在押人员','服刑区吸烟','安全检查','损坏遗失','跨监室谈话','督查通报','演练','学习材料','外宣','信息录入','其他']);
        }
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