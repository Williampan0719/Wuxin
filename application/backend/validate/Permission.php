<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2018/1/19
 * Time: 14:00
 * @introduce
 */
namespace app\backend\validate;

use think\Validate;

class Permission extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'name' => 'require|unique:backend_permissions',
        'pid' => 'require',
        'show'=>'in:0,1'
    ];

    protected $message = [
        'id.require' => '节点ID,不能为空',
        'id.number' => '节点ID,只能为数字',
        'name.require' => '节点名称不能为空',
        'name.unique' => '节点名称已存在',
        'pid.require'=> '父节点不能为空',
        'show.in'=>'必须选择是否显示',
    ];

    protected $scene = [
        'add' => ['name','pid','show'],
        'edit'=> ['id','name','pid','show'],
        'detail'=>['id'],
    ];
}