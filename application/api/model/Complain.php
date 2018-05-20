<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/4/8
 * Time: 下午4:58
 * @introduce
 */
namespace app\api\model;

use app\common\model\BaseModel;

class Complain extends BaseModel
{
    protected $table = 'complain';
    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = 'create_at';

    /**
     * @Author panhao
     * @DateTime 2018-04-08
     *
     * @description 保存投诉
     * @param array $param
     * @return false|int
     */
    public function saveComplain(array $param)
    {
        return $this->allowField(true)->save($param);
    }
}