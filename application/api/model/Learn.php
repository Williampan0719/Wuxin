<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/26
 * Time: 上午11:07
 * @introduce
 */
namespace app\api\model;


use app\common\model\BaseModel;
use think\Db;

class Learn extends BaseModel
{
    protected $table = 'learn';

    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-03-23
     *
     * @description 获取个数
     * @param int $uid
     * @return int|string
     */
    public function getCount(int $uid)
    {
        return $this->where(['uid'=>$uid])->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-26
     *
     * @description 获取单条
     * @param int $uid
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getOne(int $uid, string $field = '*')
    {
        return $this->where(['uid'=>$uid])->field($field)->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-30
     *
     * @description 获取单字段
     * @param int $uid
     * @param string $value
     * @return mixed
     */
    public function getValue(int $uid, string $value)
    {
        return $this->where(['uid'=>$uid])->value($value);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-26
     *
     * @description 编辑家长信息
     * @param array $param
     * @param int $uid
     * @return false|int
     */
    public function editLearn(array $param, int $uid)
    {
        return $this->allowField(true)->save($param,['uid'=>$uid]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-26
     *
     * @description 新增家长信息
     * @param array $param
     * @return false|int
     */
    public function addLearn(array $param)
    {
        $learn = new Learn($param);
        return $learn->allowField(true)->save();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-09
     *
     * @description 带分页家长列表
     * @param string $where
     * @param int $page
     * @param int $size
     * @param string $field
     * @param string $field2
     * @param string $where2
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPageList(string $where, int $page, int $size, string $field = '*',string $field2 = '*',string $where2 = '')
    {
        //return $this->alias('l')->join('user u','u.id=l.uid')->where($where)->field($field)->page($page,$size)->order($order)->select();
//        return Db::table('tut_learn')->alias('l')->join('tut_user u ','u.id=l.uid')->field($field)->where($where)
//            ->union('SELECT '.$field.' FROM tut_learn l INNER JOIN tut_user u ON u.id=l.uid where '.$where2)
//            ->page($page,$size)
//            ->select();
        return Db::query(
            'SELECT '.'a.*'.' from (SELECT '.$field.' FROM tut_learn l INNER JOIN tut_user u ON l.uid=u.id where '.$where.' order by distance asc limit 20) as a UNION SELECT '.'b.*'.' from (SELECT '.$field2.' FROM tut_learn l INNER JOIN tut_user u ON l.uid=u.id where '.$where2.' order by distance asc limit 20) as b limit '.($page-1)*$size.','.$size.'');
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-09
     *
     * @description 另类带分页家长列表
     * @param array $where
     * @param int $start
     * @param int $end
     * @param string $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getLimitList(array $where,int $start, int $end, string $field = '*', string $order = 'id desc')
    {
        return $this->alias('l')->join('user u','u.id=l.uid')->where($where)->field($field)->limit($start,$end)->order($order)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 家长列表
     * @param array $where
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getList(array $where, string $order = 'id desc')
    {
        return $this->where($where)->order($order)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 获取总条数
     * @param string $where
     * @param string $field
     * @param string $where2
     * @return int|string
     */
    public function getTotal(string $where, string $field, string $where2)
    {
        //return $this->alias('t')->join('user u ','u.id=t.uid')->where($param)->count();
        return Db::table('tut_learn')->alias('l')->join('tut_user u ','u.id=l.uid')->field($field)->where($where2)
            //->union('SELECT '.$field.' FROM tut_tutor t INNER JOIN tut_user u ON u.id=t.uid where '.$where2)
            ->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-19
     *
     * @description 删除
     * @param array $where
     * @return int
     */
    public function delLearn(array $where)
    {
        return Learn::destroy($where);
    }
}