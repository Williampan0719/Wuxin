<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2018/1/2
 * Time: 14:57
 * @introduce
 */

namespace app\backend\validate;

use think\Validate;

class Role extends Validate
{
    protected $rule = [
        'page' => 'number',
        'size' => 'number',
        'id' => 'require|number',
        'name' => 'require',
        'display_name' => 'require',
        'permission_id'=>'require',
    ];
    protected $message = [
        'page.number' => '页数只能为数字',
        'size.number' => '页码只能为数字',
        'id.number' => '角色ID只能为数字',
        'id.require' => '角色ID不能为空',
        'name.require' => '角色名不能为空',
        'display_name.require' => '角色显示名不能为空',
        'permission_id.require'=>'权限不能为空',
    ];
    protected $scene = [
        'add' => ['name', 'display_name'],
        'detail'=>['id'],
        'edit'=>['name','display_name','id'],
        'delete'=>['id'],
        'list'=>['page','size'],
        'permission'=>['permission_id','id']
    ];
}