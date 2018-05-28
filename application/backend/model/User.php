<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/4/10
 * Time: 上午9:27
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;

class User extends BaseModel
{
    protected $table = 'user';
    protected $autoWriteTimestamp = 'datetime';

    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';
    protected $deleteTime = 'delete_at';

    /**
     * @Author panhao
     * @DateTime 2018-04-10
     *
     * @description 获取单条
     * @param int $id
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function finds(int $id,string $field){
        return $this->where(['id'=>$id])->field($field)->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 获取单条
     * @param int $id
     * @param string $value
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getValue(int $id, string $value){
        return $this->where(['id'=>$id])->value($value);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 多个id查询列表
     * @param array $where
     * @param string $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getInfoByIds(array $where,string $field, string $order = 'id asc')
    {
        return $this->where($where)->field($field)->order($order)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-28
     *
     * @description 单条
     * @param array $param
     * @return false|int
     */
    public function addOne(array $param)
    {
        return $this->allowField(true)->save($param);
    }
}