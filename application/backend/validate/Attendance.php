<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/31
 * Time: 下午8:14
 * @introduce
 */
namespace app\backend\validate;

use think\Validate;

class Attendance extends Validate
{
    protected $rule = [
        'uuid' => 'require',
    ];
    protected $message = [
        'uuid.require'=>'警员id必须填写',
    ];
    protected $scene = [
        'add' => ['uuid'],
    ];
}