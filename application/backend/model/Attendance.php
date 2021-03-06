<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/30
 * Time: 下午8:25
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;
use traits\model\SoftDelete;

class Attendance extends BaseModel
{
    use SoftDelete;

    protected $autoWriteTimestamp = 'datetime';
    protected $table = 'attendance';

    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';
    protected $deleteTime = 'delete_at';

    /**
     * @Author panhao
     * @DateTime 2018-05-30
     *
     * @description 新增
     * @param array $param
     * @return false|int
     */
    public function addOne(array $param)
    {
        return $this->allowField(true)->save($param);
    }
    /**
     * @Author panhao
     * @DateTime 2018-05-30
     *
     * @description 搜索列表
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function searchList(array $where, string $field,int $page, int $size)
    {
        return $this->where($where)->field($field)->page($page,$size)->group('uuid')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-31
     *
     * @description 总数
     * @param array $where
     * @return int|string
     */
    public function searchCount(array $where)
    {
        return $this->where($where)->group('uuid')->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-31
     *
     * @description 删除
     * @param array $where
     * @return int
     */
    public function delList(array $where)
    {
        return Attendance::destroy($where);
    }
}