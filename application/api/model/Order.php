<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/26
 * Time: 下午4:22
 */

namespace app\api\model;


use app\common\model\BaseModel;
use think\Exception;

class Order extends BaseModel
{
    protected $table = 'order';

    /** 获取订单号
     *  auth smallzz
     *  @param int $id
     *  @return bool|mixed
     */
    public function getOrderSn(int $id){
        try{
            $res = $this->where(['id'=>$id])->value('order_sn');
        }catch (Exception $exception){
            return false;
        }
        return $res;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-13
     *
     * @description 获取单条
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getOne(array $where, string $field)
    {
        return $this->where($where)->field($field)->order('id desc')->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-13
     *
     * @description 修改订单退款状态
     * @param int $id
     * @return false|int
     */
    public function editOneRefund(int $id)
    {
        return $this->save(['is_refund'=>time()],['id'=>$id]);
    }




}