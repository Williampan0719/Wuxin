<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/27
 * Time: 上午11:30
 */

namespace app\api\model;


use app\common\model\BaseModel;

class UserImage extends BaseModel
{
    protected $table = 'user_image';

    protected $createTime = 'addtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    /**
     * @Author panhao
     * @DateTime 2018-04-19
     *
     * @description 删除
     * @param array $where
     * @return int
     */
    public function delImage(array $where)
    {
        return UserImage::destroy($where);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-09
     *
     * @description 单条
     * @param int $uid
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getOne(int $uid,string $field)
    {
        return $this->where(['uid'=>$uid])->field($field)->order('id desc')->find();
    }

}