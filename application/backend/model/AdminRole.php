<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/21
 * Time: 上午9:53
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;

class AdminRole extends BaseModel
{
    protected $table = 'backend_admin_role';

    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-05-21
     *
     * @description 获取单条
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getOne(array $where, string $field)
    {
        return $this->where($where)->field($field)->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-21
     *
     * @description 获取单条
     * @param array $where
     * @param string $field
     * @return mixed
     */
    public function getValue(array $where, string $field)
    {
        return $this->where($where)->value($field);
    }
}