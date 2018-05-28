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
        'uuid' => 'require',
        'name' => 'require',
    ];
    protected $message = [
        'uuid.require'=>'警员id必须填写',
        'name.require'=>'警员姓名必须填写',
    ];
    protected $scene = [
        'add' => ['uuid','name'],
    ];
}