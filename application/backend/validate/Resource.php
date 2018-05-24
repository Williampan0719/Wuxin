<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/05/17
 * Time: 下午5:23
 * @introduce
 */
namespace app\backend\validate;

use think\Validate;

class Resource extends Validate
{
    protected $rule = [
        'name' => 'require',
        'id'   => 'require'
    ];
    protected $message = [
        'name.require'=>'渠道名称必须填写',
        'id.require'=>'渠道id必须填写',
    ];
    protected $scene = [
        'add'  => ['name'],
        'edit' => ['id'],
    ];
}