<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/4
 * Time: 上午11:58
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Statistic;
use app\common\logic\BaseLogic;
use think\Request;

class StatisticLogic extends BaseLogic
{
    protected $statistic = null;

    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->statistic = new Statistic();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-04
     *
     * @description 统计首页
     * @param array $param
     * @return array
     */
    public function searchIndex(array $param)
    {
        $where = [];
        $where2 = ['addtime'=>['gt',strtotime(date('Y-m-d'))]]; //今日
        if (!empty($param['start_time']) && !empty($param['end_time'])) {
            $where['addtime'] = ['between', [$param['start_time'],$param['end_time']]];
        }elseif (!empty($param['start_time'])) {
            $where['addtime'] = ['gt',$param['start_time']];
        }elseif (!empty($param['end_time'])) {
            $where['addtime'] = ['lt',$param['end_time']];
        }
        if (!empty($param['province'])) {
            $where['province'] = $param['province'];
            $where2['province'] = $param['province'];
        }
        if (!empty($param['city'])) {
            $where['city'] = $param['city'];
            $where2['city'] = $param['city'];
        }
        $list_all = $this->statistic->searchIndex($where,'count(*) as count,type,role,sex','type,role');
        $all = [];
        if (!empty($list_all)) { //处理总数据
            foreach ($list_all as $key => $value) {
                $all[$value['type']][$value['role']] = $value['count'];
            }
        }
        $list_today = $this->statistic->searchIndex($where2,'count(*) as count,type,role,sex','type,role');
        $today = [];
        if (!empty($list_today)) { //处理今日数据
            foreach ($list_today as $key => $value) {
                $today[$value['type']][$value['role']] = $value['count'];
            }
        }
        $list = [
            'regist_today_tutor' => $today[1][2] ?? 0, //今日注册家教
            'regist_today_learn' => $today[1][1] ?? 0, //今日注册家长
            'regist_today_sum'   => ($today[1][2] ?? 0) + ($today[1][1] ?? 0), //今日注册总
            'regist_all_tutor' => $all[1][2] ?? 0, //累计注册家教
            'regist_all_learn' => $all[1][1] ?? 0, //累计注册家长
            'regist_all_sum'   => ($all[1][2] ?? 0) + ($all[1][1] ?? 0), //累计注册总
            'auth_today_tutor' => $today[2][2] ?? 0, //今日认证家教
            'auth_today_learn' => $today[2][1] ?? 0, //今日认证家长
            'auth_today_sum'   => ($today[2][2] ?? 0) + ($today[2][1] ?? 0), //今日认证总
            'auth_all_tutor' => $all[2][2] ?? 0, //累计认证家教
            'auth_all_learn' => $all[2][1] ?? 0, //累计认证家长
            'auth_all_sum'   => ($all[2][2] ?? 0) + ($all[2][1] ?? 0), //累计认证总
            'contact_today_tutor' => $today[3][2] ?? 0, //今日解锁家教
            'contact_today_learn' => $today[3][1] ?? 0, //今日解锁家长
            'contact_today_sum'   => ($today[3][2] ?? 0) + ($today[3][1] ?? 0), //今日解锁总
            'contact_all_tutor' => $all[3][2] ?? 0, //累计解锁家教
            'contact_all_learn' => $all[3][1] ?? 0, //累计解锁家长
            'contact_all_sum'   => ($all[3][2] ?? 0) + ($all[3][1] ?? 0), //累计解锁总
            'share_today_tutor' => $today[4][2] ?? 0, //今日分享家教
            'share_today_learn' => $today[4][1] ?? 0, //今日分享家长
            'share_today_sum'   => ($today[4][2] ?? 0) + ($today[4][1] ?? 0), //今日分享总
            'share_all_tutor' => $all[4][2] ?? 0, //累计分享家教
            'share_all_learn' => $all[4][1] ?? 0, //累计分享家长
            'share_all_sum'   => ($all[4][2] ?? 0) + ($all[4][1] ?? 0), //累计分享总
        ];
        $list['auth_today_tutor_ratio'] = !empty($list['regist_today_tutor']) ? sprintf("%01.1f",$list['auth_today_tutor']*100/$list['regist_today_tutor']).'%' : '0%';
        $list['auth_today_learn_ratio'] = !empty($list['regist_today_learn']) ? sprintf("%01.1f",$list['auth_today_learn']*100/$list['regist_today_learn']).'%' : '0%';
        $list['auth_today_sum_ratio'] = !empty($list['regist_today_sum']) ? sprintf("%01.1f",$list['auth_today_sum']*100/$list['regist_today_sum']).'%' : '0%';
        $list['auth_all_tutor_ratio'] = !empty($list['regist_all_tutor']) ? sprintf("%01.1f",$list['auth_all_tutor']*100/$list['regist_all_tutor']).'%' : '0%';
        $list['auth_all_learn_ratio'] = !empty($list['regist_all_learn']) ? sprintf("%01.1f",$list['auth_all_learn']*100/$list['regist_all_learn']).'%' : '0%';
        $list['auth_all_sum_ratio'] = !empty($list['regist_all_sum']) ? sprintf("%01.1f",$list['auth_all_sum']*100/$list['regist_all_sum']).'%' : '0%';
        return $this->ajaxSuccess(104,['list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-07
     *
     * @description 统计详情
     * @param array $param
     * @return array
     */
    public function searchDetail(array $param)
    {
        $where = [];
        if (!empty($param['start_time']) && !empty($param['end_time'])) {
            $where['addtime'] = ['between', [strtotime($param['start_time']),strtotime($param['end_time'])]];
        }elseif (!empty($param['start_time'])) {
            $where['addtime'] = ['gt',strtotime($param['start_time'])];
        }elseif (!empty($param['end_time'])) {
            $where['addtime'] = ['lt',strtotime($param['end_time'])];
        }
        $where['type'] = $param['type'] ?? 1;

        //1.注册 2.认证 3.解锁 5.分享
        $list_all = $this->statistic->searchIndex($where,'count(*) as count,role,sex','sex,role');
        $all = [];
        if (!empty($list_all)) { //处理总数据
            foreach ($list_all as $key => $value) {
                $all[$value['role']][$value['sex']] = $value['count'];
            }
        }
        $list = [
            'tutor_male' => $all[2][1] ?? 0, //家教男
            'tutor_female' => $all[2][2] ?? 0, //家教女
            'tutor_sum'   => ($all[2][1] ?? 0) + ($all[2][2] ?? 0), //家教总
            'learn_male' => $all[1][1] ?? 0, //家长男
            'learn_female' => $all[1][2] ?? 0, //家长女
            'learn_sum'   => ($all[1][1] ?? 0) + ($all[1][2] ?? 0), //家长总
            'sum'   => ($all[1][1] ?? 0) + ($all[1][2] ?? 0) + ($all[2][1] ?? 0) + ($all[2][2] ?? 0), //总
        ];
        $list['learn_tutor'] = $list['tutor_sum'].':'.$list['learn_sum'];
        $list['learn_male_female'] = $list['learn_male'].':'.$list['learn_female'];
        $list['tutor_male_female'] = $list['tutor_male'].':'.$list['tutor_female'];
        $date = [];
        for ($i = strtotime($param['start_time']);$i <= strtotime($param['end_time']);$i += 86400)
        {
            $date[] = date('Y-m-d',$i);
        }

        $chart['sum'] = $this->handleData($where,$date);
        $chart['tutor'] = $this->handleData($where + ['role'=>2],$date);
        $chart['learn'] = $this->handleData($where + ['role'=>1],$date);
        $chart['tutor_male'] = $this->handleData($where + ['role'=>2,'sex'=>1],$date);
        $chart['tutor_female'] = $this->handleData($where + ['role'=>2,'sex'=>2],$date);
        $chart['learn_male'] = $this->handleData($where + ['role'=>1,'sex'=>1],$date);
        $chart['learn_female'] = $this->handleData($where + ['role'=>1,'sex'=>2],$date);
        if ($where['type'] == 2) { //认证
            $where['type'] = 1;
            $list_all = $this->statistic->searchIndex($where,'count(*) as count,role,sex','sex,role');
            $all = [];
            if (!empty($list_all)) { //处理总数据
                foreach ($list_all as $key => $value) {
                    $all[$value['role']][$value['sex']] = $value['count'];
                }
            }
            $regist = [
                'tutor_male' => $all[2][1] ?? 0, //家教男
                'tutor_female' => $all[2][2] ?? 0, //家教女
                'tutor_sum'   => ($all[2][1] ?? 0) + ($all[2][2] ?? 0), //家教总
                'learn_male' => $all[1][1] ?? 0, //家长男
                'learn_female' => $all[1][2] ?? 0, //家长女
                'learn_sum'   => ($all[1][1] ?? 0) + ($all[1][2] ?? 0), //家长总
                'sum'   => ($all[1][1] ?? 0) + ($all[1][2] ?? 0) + ($all[2][1] ?? 0) + ($all[2][2] ?? 0), //总
            ];
            $list['tutor_male_per'] = $regist['tutor_male'] != 0 ? sprintf("%01.1f",($list['tutor_male']*100/$regist['tutor_male'])).'%' : '0%';
            $list['tutor_female_per'] = $regist['tutor_female'] != 0 ? sprintf("%01.1f",($list['tutor_female']*100/$regist['tutor_female'])).'%' : '0%';
            $list['tutor_sum_per'] = $regist['tutor_sum'] != 0 ? sprintf("%01.1f",($list['tutor_sum']*100/$regist['tutor_sum'])).'%' : '0%';
            $list['learn_male_per'] = $regist['learn_male'] != 0 ? sprintf("%01.1f",($list['learn_male']*100/$regist['learn_male'])).'%' : '0%';
            $list['learn_female_per'] = $regist['learn_female'] != 0 ? sprintf("%01.1f",($list['learn_female']*100/$regist['learn_female'])).'%' : '0%';
            $list['learn_sum_per'] = $regist['learn_sum'] != 0 ? sprintf("%01.1f",($list['learn_sum']*100/$regist['learn_sum'])).'%' : '0%';
            $list['sum_per'] = $regist['sum'] != 0 ? sprintf("%01.1f",($list['sum']*100/$regist['sum'])).'%' : '0%';
        }

        return $this->ajaxSuccess(104,['list'=>$list,'chart'=>$chart]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-07
     *
     * @description 处理日期数据
     * @param array $param
     * @param array $date
     * @return array
     */
    private function handleData(array $param,array $date)
    {
        $chart = $this->statistic->searchIndex($param,'count(*) as count,add_date','add_date','id asc'); //总
        $chart_new = [];$new = [];
        foreach ($chart as $key => $value) {
            $chart_new[$value['add_date']] = $value['count'];
        }
        foreach ($date as $k => $v) {
            $new[$k]['date'] = $v;
            $new[$k]['count'] = $chart_new[$v] ?? 0;
        }
        return $new;
    }
}