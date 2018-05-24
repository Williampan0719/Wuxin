<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/4
 * Time: 上午11:59
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;

class Statistic extends BaseModel
{
    protected $table = 'statistic';

    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-05-04
     *
     * @description 统计首页
     * @param array $where
     * @param string $field
     * @param string $group
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function searchIndex(array $where, string $field = '*',string $group,string $order = 'id desc')
    {
        return $this->where($where)->field($field)->group($group)->order($order)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-07
     *
     * @description 图表
     * @param array $where
     * @param string $group
     * @return int|string
     */
    public function searchChart(array $where, string $group)
    {
        return $this->where($where)->group($group)->count();
    }
}