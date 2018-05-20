<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/23
 * Time: 下午1:43
 * @introduce
 */
namespace app\api\model;

use app\common\model\BaseModel;
use traits\model\SoftDelete;

class Favorite extends BaseModel
{
    use SoftDelete;
    protected $table = 'favorite';

    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = 'create_at';
    protected $deleteTime = 'delete_at';
    protected $updateTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-03-23
     *
     * @description 获取个数
     * @param string $uid
     * @return int|string
     */
    public function getCount(string $uid)
    {
        return $this->where(['uid'=>$uid])->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-23
     *
     * @description 获取个数
     * @param string $uid
     * @return int|string
     */
    public function getCount2(string $uid)
    {
        return $this->where(['to_uid'=>$uid])->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 获取分页收藏列表
     * @param array $where
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPageList(array $where, int $page,int $size)
    {
        return $this->where($where)->page($page,$size)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 删除收藏列表
     * @param array $where
     * @return int
     */
    public function delFavorites(array $where)
    {
        return Favorite::destroy($where);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 判断是否存在
     * @param array $where
     * @return int|string
     */
    public function getExist(array $where)
    {
        return $this->where($where)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 新增收藏
     * @param array $where
     * @return int|string
     */
    public function addFavorites(array $where)
    {
        return $this->allowField(true)->save($where);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-30
     *
     * @description 获取列表
     * @param int $uid
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getList(int $uid, string $field = '*')
    {
        return $this->where(['uid'=>$uid])->field($field)->select();
    }
}