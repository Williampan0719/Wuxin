<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/29
 * Time: 下午5:23
 * @introduce
 */
namespace app\backend\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'type' => 'require',
    ];
    protected $message = [
        'type.require'=>'用户类型必须填写',
    ];
    protected $scene = [
    ];
}