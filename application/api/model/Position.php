<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/26
 * Time: 上午11:20
 * @introduce
 */
namespace app\api\model;


use app\common\model\BaseModel;
use traits\model\SoftDelete;

class Position extends BaseModel
{
    protected $table = 'position';
    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';
    protected $deleteTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-03-26
     *
     * @description 条件获取单条
     * @param array $where
     * @param string $field
     * @param string $order
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getOne(array $where, string $field = '*',$order = 'id desc')
    {
        return $this->where($where)->field($field)->order($order)->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 地址管理列表
     * @param int $uid
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPositionList(int $uid, string $field = '*')
    {
        return $this->where(['uid'=>$uid])->field($field)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-28
     *
     * @description 添加
     * @param array $param
     * @return false|int
     */
    public function addPosition(array $param)
    {
        $position = new Position($param);
        return $position->allowField(true)->save();
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-28
     *
     * @description 编辑
     * @param array $param
     * @param array $where
     * @return false|int
     */
    public function editPosition(array $param, array $where)
    {
        return $this->allowField(['geo_name','lng','lat','geo_hash','create_at','update_at'])->save($param,$where);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-28
     *
     * @description 编辑
     * @param array $param
     * @param array $where
     * @return false|int
     */
    public function editPositionStatus(array $param, array $where)
    {
        return $this->allowField(true)->save($param,$where);
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-28
     *
     * @description 编辑
     * @param int $id
     * @return false|int
     */
    public function delPosition(int $id)
    {
        return Position::destroy($id);
    }
}