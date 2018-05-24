<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/14
 * Time: 上午9:13
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;
use think\Db;

class Resource extends BaseModel
{
    protected $table = 'resource';

    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-05-14
     *
     * @description 获取字段
     * @param array $where
     * @param string $field
     * @return mixed
     */
    public function getValue(array $where, string $field)
    {
        return $this->where($where)->value($field);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-15
     *
     * @description 获取列表
     * @param array $param
     * @param string $field
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPageList(array $param, string $field,int $page,int $size)
    {
        return $this->where($param)->field($field)->page($page,$size)->order('id desc')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-15
     *
     * @description 获取列表
     * @param array $param
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getTotal(array $param)
    {
        return $this->where($param)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-15
     *
     * @description 获取列表
     * @param string $param
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getList(string $param, string $field)
    {
        return $this->where($param)->field($field)->order('id desc')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-17
     *
     * @description 新增
     * @param array $param
     * @return int|string
     */
    public function createResource(array $param)
    {
        return Db::table('tut_resource')->insertGetId($param);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-17
     *
     * @description 编辑
     * @param array $param
     * @param int $id
     * @return int|string
     */
    public function editResource(array $param,$id)
    {
        return $this->allowField(true)->save($param,['id'=>$id]);
    }
}