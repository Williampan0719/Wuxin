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
    protected $regex = [
        'mobile' => '/^134[0-8]\d{7}$|^13[^4]\d{8}$|^14[5-9]\d{8}$|^15[^4]\d{8}$|^16[6]\d{8}$|^17[0-8]\d{8}$|^18[\d]{9}$|^19[8,9]\d{8}$/',
    ];
    protected $rule = [
        'page' => 'number',
        'size' => 'number',
        'admin_id' => 'require|number',
        'admin_name' => 'require|unique:backend_admin',
        'admin_password' => 'require',
        'admin_mobile' => 'regex:mobile|unique:backend_admin',
        'is_super' => 'in:0,1',//是否是超级管理员
        'status' => 'in:1,2',//状态
        'new_password'=>'require|confirm',

    ];

    protected $message = [
        'page.number' => '页数必须为数字',
        'size.number' => '页码必须为数字',
        'admin_id.require' => '后台用户ID,不能为空',
        'admin_id.number' => '后台用户ID,只能为数字',
        'admin_name.require' => '后台用户名不能为空',
        'admin_name.unique' => '后台用户名已存在',
        'admin_password.require' => '密码不能为空',
        'admin_mobile.regex' => '手机号格式不正确',
        'is_super.in' => '必须选择是否超级管理员',
        'status.in' => '必须选择用户状态',
        'admin_mobile.unique' => '后台用户手机号已存在',
        'new_password.require'=>'新密码不能为空',
        'new_password.confirm'=>'两次输入的密码不相同',
    ];

    protected $scene = [
        'add' => ['admin_name', 'admin_password', 'admin_mobile', 'is_super'],
        'detail' => ['admin_id'],
        'setStatus' => ['admin_id','status'],
        'edit' => ['admin_id', 'admin_name', 'admin_mobile', 'is_super'],
        'list' => ['page', 'size'],
        'delete' => ['admin_id'],
        'updpwd'=>['admin_id','new_password']
    ];
}