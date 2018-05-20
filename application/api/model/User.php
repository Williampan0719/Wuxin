<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/22
 * Time: 上午10:14
 */

namespace app\api\model;


use app\common\model\BaseModel;
use think\Model;

class User extends BaseModel
{
    protected $table = 'user';

    /**用户信息添加
     * auth smallzz
     * @param array $data
     * @return false|int
     */
    public function add(array $data){
        return $this->save($data);
    }

    /**用户信息编辑
     * auth smallzz
     * @param int $id
     * @param array $data
     * @return false|int
     */
    public function edit(int $id,array $data){
        return $this->where(['id'=>$id])->save($data);
    }

    /**用户信息删除
     * auth smallzz
     * @param int $id
     * @return int
     */
    public function del(int $id){
        return $this->where(['id'=>$id])->delete();
    }
    /**用户信息单查询
     * auth smallzz
     * @param int $id
     * @param string $field
     * @return array|false|\PDOStatement|string|Model
     */
    public function finds(int $id,string $field){
        return $this->where(['id'=>$id])->field($field)->find();
    }

    /**用户信息多查询
     * auth smallzz
     * @param string $where
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function selects(array $where,string $field='*'){
        return $this->where($where)->field($field)->select();
    }

    /** 检查openid的信息是否存在
     * auth smallzz
     * @param string $openid
     * @return int|string
     */
    public function checkOpenid(string $openid){
        return $this->where(['openid'=>$openid])->count();
    }

    /** 获取用户详情 指定字段
     * auth smallzz
     * @param string $openid
     * @param $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function userDetailAll(string $openid,string $field){
        return $this->where(['openid'=>$openid])->field($field)->find();
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
    public function getInfoByIds(array $where,string $field, string $order = 'addtime desc')
    {
        return $this->where($where)->field($field)->order($order)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 带分页家长列表
     * @param array $where
     * @param int $page
     * @param int $size
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPageList(array $where,int $page, int $size,string $order = 'id desc')
    {
        return $this->alias('u')->join('tutor t','u.id=t.uid')->where($where)->page($page,$size)->order($order)->field('u.*')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 带分页家长列表
     * @param array $where
     * @param int $page
     * @param int $size
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPageList2(array $where,int $page, int $size,string $order = 'id desc')
    {
        return $this->alias('u')->join('learn l ','u.id=l.uid')->where($where)->page($page,$size)->order($order)->field('u.*')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 获取总条数
     * @param array $param
     * @return int|string
     */
    public function getTotal(array $param)
    {
        return $this->alias('u')->join('tutor t','u.id=t.uid')->where($param)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 获取总条数
     * @param array $param
     * @return int|string
     */
    public function getTotal2(array $param)
    {
        return $this->alias('u')->join('learn l ','u.id=l.uid')->where($param)->count();
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
     * @DateTime 2018-04-28
     *
     * @description 搜索单条
     * @param array $where
     * @param string $value
     * @return mixed
     */
    public function searchValue(array $where, string $value){
        return $this->where($where)->value($value);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-16
     *
     * @description 单条
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOne(array $where,string $field){
        return $this->where($where)->field($field)->find();
    }
}