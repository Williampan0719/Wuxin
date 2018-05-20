<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/2
 * Time: 上午9:43
 * @introduce
 */
namespace app\backend\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'admin_name' => 'require',
        'admin_password' => 'require',
        'admin_mobile' => 'require',
    ];
    protected $message = [
        'admin_name.require'=>'用户名称必须填写',
        'admin_password.require'=>'密码必须填写',
        'admin_mobile.require'=>'手机号必须填写',
    ];
    protected $scene = [
        'add'=>['admin_name','admin_password','admin_mobile'],
    ];
}