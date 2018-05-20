<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/22
 * Time: 下午2:55
 */

namespace app\api\model;


use app\common\model\BaseModel;
use think\Model;

class UserCer extends BaseModel
{
    protected $table = 'user_cer';

    /** 添加
     * auth smallzz
     * @param array $data
     * @return false|int
     */
    public function add(array $data){
        return $this->save($data);
    }

    /** 编辑
     * auth smallzz
     * @param int $id
     * @param array $data
     * @return false|int
     */
    public function edit(int $id,array $data){
        return $this->where(['id'=>$id])->save($data);
    }

    /** 删除
     * auth smallzz
     * @param int $id
     * @return int
     */
    public function del(int $id){
        return $this->where(['id'=>$id])->delete();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 获取单条认证
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOne(array $where, string $field)
    {
        return $this->where($where)->field($field)->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 分组获取认证数据
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string|Model
     */
    public function getGroupOne(array $where, string $field)
    {
        return $this->where($where)->field($field)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-16
     *
     * @description 总数
     * @param array $where
     * @param string $field
     * @return int|string
     */
    public function getCount(array $where,string $field)
    {
        return $this->where($where)->field($field)->count();
    }

}