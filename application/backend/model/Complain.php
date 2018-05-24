<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/4/8
 * Time: 下午5:35
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;

class Complain extends BaseModel
{
    protected $table = 'complain';
    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = 'create_at';
    protected $updateTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-04-08
     *
     * @description 获取投诉列表
     * @param array $param
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getComplainList(array $param, int $page, int $size)
    {
        return $this->where($param)->page($page,$size)->order('id desc')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-08
     *
     * @description 获取投诉列表
     * @param array $param
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getList(array $param, string $field = '*')
    {
        return $this->where($param)->field($field)->order('id desc')->select();
    }
}