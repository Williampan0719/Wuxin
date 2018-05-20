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
    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;


    /**
     * @Author panhao
     * @DateTime 2018-04-10
     *
     * @description 搜索家教列表
     * @param array $param
     * @param int $page
     * @param int $size
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function searchTutorList(array $param, int $page, int $size, string $field)
    {
        return $this->alias('u')->join('tutor t','t.uid=u.id')->join('user_cer_info c','c.uid=u.id','left')->where($param)->field($field)->order('u.id desc')->page($page,$size)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-25
     *
     * @description 家教列表总数
     * @param array $param
     * @return int|string
     */
    public function searchTutorCount(array $param)
    {
        return $this->alias('u')->join('tutor t','t.uid=u.id')->join('user_cer_info c','c.uid=u.id','left')->where($param)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-10
     *
     * @description 搜索家长列表
     * @param array $param
     * @param int $page
     * @param int $size
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function searchLearnList(array $param, int $page, int $size, string $field)
    {
        return $this->alias('u')->join('learn l','l.uid=u.id')->where($param)->field($field)->order('u.id desc')->page($page,$size)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-10
     *
     * @description 搜索家长家教混合列表
     * @param string $param
     * @param int $page
     * @param int $size
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function searchUserList(string $param, int $page, int $size, string $field)
    {
        return $this->alias('u')->join('learn l','l.uid=u.id', 'left')->join('tutor t','t.uid=u.id','left')->join('user_cer c','u.id=c.uid','left')->where($param)->field($field)->order('u.id desc')->group('u.id')->page($page,$size)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-25
     *
     * @description 混合列表总数
     * @param string $param
     * @return int|string
     */
    public function searchUserCount(string $param)
    {
        return $this->alias('u')->join('learn l','l.uid=u.id', 'left')->join('tutor t','t.uid=u.id','left')->join('user_cer c','c.uid=u.id','left')->where($param)->group('u.id')->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-25
     *
     * @description 家长列表总数
     * @param array $param
     * @return int|string
     */
    public function searchLearnCount(array $param)
    {
        return $this->alias('u')->join('learn l','l.uid=u.id')->join('user_cer_info c','c.uid=u.id','left')->where($param)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 获取微信审核列表
     * @param string $where
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getWechatAudit(string $where, int $page, int $size)
    {
        return $this->where($where)->page($page,$size)->order('id desc')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 获取微信审核总数
     * @param string $where
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getWechatAuditCount(string $where)
    {
        return $this->where($where)->order('id desc')->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 获取学历审核列表
     * @param string $where
     * @param int $page
     * @param int $size
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getEducationAudit(string $where, int $page, int $size,string $field = '*')
    {
        return $this->alias('u')->join('user_cer c','u.id=c.uid', 'left')->join('user_cer_info i','u.id=i.uid','left')->join('user_image m','u.id=m.uid','left')->where($where)->field($field)->page($page,$size)->order('u.id desc')->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 获取学历审核总数
     * @param string $where
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getEducationAuditCount(string $where, string $field = '*')
    {
        return $this->alias('u')->join('user_cer c','u.id=c.uid', 'left')->join('user_cer_info i','u.id=i.uid','left')->join('user_image m','u.id=m.uid','left')->where($where)->field($field)->order('u.id desc')->count();
    }

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
    public function getInfoByIds(array $where,string $field, string $order = 'addtime desc')
    {
        return $this->where($where)->field($field)->order($order)->select();
    }
}