<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/4/13
 * Time: 下午1:30
 * @introduce
 */
namespace app\api\model;


use app\common\model\BaseModel;

class Refund extends BaseModel
{
    protected $table = 'refund';
    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = 'create_at';
    protected $updateTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-04-13
     *
     * @description 退款记录
     * @param array $param
     * @param string $field
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRefundLog(array $param, string $field, int $page, int $size)
    {
        return $this->alias('r')->join('user u','u.id=r.uid')->where($param)->field($field)->page($page,$size)->order('r.id desc')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-13
     *
     * @description 总数
     * @param array $param
     * @return int|string
     */
    public function getTotal(array $param)
    {
        return $this->alias('r')->where($param)->count();
    }
}