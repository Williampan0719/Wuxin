<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/28
 * Time: 上午9:34
 */
namespace app\api\model;

use app\common\model\BaseModel;

class UserCerInfo extends BaseModel
{
    protected $table = 'user_cer_info';

    /** 检查是否认证学历
     * auth smallzz
     * @param int $uid
     * @return bool
     */
    public function CheckUid(int $uid){
        $count = $this->where(['uid'=>$uid,'type'=>2])->count();
        if($count > 0){
            return false;
        }
        return true;
    }

    /**
     * @Author panhao
     * @DateTime 2018-03-27
     *
     * @description 获取单条认证
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string
     */
    public function getOne(array $where, string $field)
    {
        return $this->where($where)->field($field)->find();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-08
     *
     * @description 获取信息
     * @param array $param
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getInfo(array $param,string $field)
    {
        return $this->alias('i')->join('user_image m','i.uid=m.uid','left')->where($param)->field($field)->find();
    }

    /** 删除
     * auth smallzz
     * @param int $id
     * @return int
     */
    public function del(int $id){
        return $this->where(['uid'=>$id])->delete();
    }
}