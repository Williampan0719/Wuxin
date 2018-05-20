<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/3
 * Time: 下午1:13
 */

namespace app\api\logic;


use extend\service\RedisService;

class FormIdLogic extends RedisService
{
    /** 添加form_id
     * auth smallzz
     * @param string $form_id
     */
    public function addFormId($uid,string $form_id){
        $res = $this->lpush($uid.'_formid',$form_id);
        $this->expire('formid',604800);  #有效期
    }

    /** 获取form_id
     * auth smallzz
     * @return string
     */
    public function getFormId(int $uid){
        return $this->lpop($uid.'_formid');
    }
}