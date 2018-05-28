<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/30
 * Time: 上午9:50
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\User;
use app\common\logic\BaseLogic;

class UserLogic extends BaseLogic
{
    protected $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-28
     *
     * @description 新增警员
     * @param array $param
     * @return array
     */
    public function addUser(array $param)
    {
        $a = $this->user->addOne($param);
        if ($a == 1) {
            return $this->ajaxSuccess(101);
        }
        return $this->ajaxError(111);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-28
     *
     * @description 警员列表
     * @return array
     */
    public function userList()
    {
        $list = $this->user->getInfoByIds([],'id,uuid,name');
        return $this->ajaxSuccess(104,['list'=>$list]);
    }
}