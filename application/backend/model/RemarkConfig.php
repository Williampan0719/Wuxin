<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/18
 * Time: 下午3:37
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;

class RemarkConfig extends BaseModel
{
    protected $table = 'remark_config';

    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = 'create_at';
    protected $updateTime = false;
    protected $deleteTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 列表
     * @param string $param
     * @param string $field
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function remarkList(string $param,string $field = '*',int $page, int $size)
    {
        return $this->where($param)->field($field)->order('id desc')->page($page,$size)->select();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 总计
     * @param string $param
     * @return int|string
     */
    public function remarkTotal(string $param)
    {
        return $this->where($param)->count();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 添加配置
     * @param array $param
     * @return false|int
     */
    public function addRemark(array $param)
    {
        return $this->allowField(true)->save($param);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 编辑配置
     * @param array $param
     * @return false|int
     */
    public function editRemark(array $param,int $id)
    {
        return $this->allowField(true)->save($param,['id'=>$id]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 删除配置
     * @param int $id
     * @return false|int
     */
    public function delRemark(int $id)
    {
        return $this->where('id',$id)->delete();
    }
}