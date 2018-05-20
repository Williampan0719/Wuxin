<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/3
 * Time: 下午5:31
 * @introduce
 */
namespace app\api\model;

use app\common\model\BaseModel;

class Statistic extends BaseModel
{
    protected $table = 'statistic';

    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-05-04
     *
     * @description 新增
     * @param array $param
     * @return false|int
     */
    public function add(array $param)
    {
        return $this->allowField(true)->save($param);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-04
     *
     * @description 批量新增
     * @param array $param
     * @return array|false
     */
    public function addAll(array $param)
    {
        return $this->allowField(true)->saveAll($param);
    }
}