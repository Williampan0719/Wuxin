<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/2
 * Time: 上午10:15
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;
use traits\model\SoftDelete;

class Assistant extends BaseModel
{
    use SoftDelete;
    protected $table = 'assistant';

    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';
    protected $deleteTime = 'delete_at';

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 添加单条
     * @param array $param
     * @return false|int
     */
    public function addOne(array $param)
    {
        return $this->allowField(true)->save($param);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 编辑单条
     * @param array $where
     * @param int $id
     * @return false|int
     */
    public function editOne(array $where, int $id)
    {
        return $this->allowField(true)->save($where,['id'=>$id]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 删除单条
     * @param int $id
     * @return int
     */
    public function delOne(int $id)
    {
        return $this->where('id',$id)->delete();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 搜索列表
     * @param string $where
     * @param string $field
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function searchList(string $where, string $field = '*',int $page, int $size)
    {
        return $this->where($where)->field($field)->page($page,$size)->order('id desc')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 总计
     * @param string $where
     * @return int|string
     */
    public function searchCount(string $where)
    {
        return $this->where($where)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-07
     *
     * @description 单条
     * @param array $param
     * @param string $field
     * @param string $order
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getOne(array $param, string $field = '*',string $order = 'id desc')
    {
        return $this->where($param)->field($field)->order($order)->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-14
     *
     * @description 获取字段
     * @param array $where
     * @param string $field
     * @return mixed
     */
    public function getValue(array $where,string $field)
    {
        return $this->where($where)->value($field);
    }
}