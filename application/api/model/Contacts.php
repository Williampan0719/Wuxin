<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/23
 * Time: 下午1:57
 * @introduce
 */
namespace app\api\model;

use app\common\model\BaseModel;
use traits\model\SoftDelete;

class Contacts extends BaseModel
{
    use SoftDelete;
    protected $table = 'contacts';
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
     * @description 获取分页联系人列表
     * @param string $where
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPageList(string $where, int $page,int $size)
    {
        return $this->where($where)->page($page,$size)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 删除联系人列表
     * @param array $where
     * @return int
     */
    public function delContacts(array $where)
    {
        return Contacts::destroy($where);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 获取用户购买列表
     * @param int $uid
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserList(int $uid, string $field)
    {
        return $this->where(['uid'=>$uid])->field($field)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-03
     *
     * @description 判断是否存在
     * @param array $param
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getExist(array $param, string $field)
    {
        return $this->where($param)->field($field)->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 获取总条数
     * @param string $param
     * @return int|string
     */
    public function getTotal(string $param)
    {
        return $this->where($param)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-09
     *
     * @description 获取总数
     * @param array $param
     * @return int|string
     */
    public function getTotal2(array $param)
    {
        return $this->where($param)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 获取总额
     * @param string $where
     * @param string $field
     * @return float|int
     */
    public function getSum(string $where, string $field)
    {
        return $this->where($where)->sum($field);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 搜索解锁列表
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function searchContactList(array $where, string $field, int $page, int $size)
    {
        return $this->alias('c')->join('user u','c.uid=u.id')->where($where)->field($field)->page($page,$size)->order('c.id desc')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 搜索解锁总数
     * @param array $where
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function searchContactCount(array $where)
    {
        return $this->alias('c')->join('user u','c.uid=u.id')->where($where)->order('c.id desc')->count();
    }
}