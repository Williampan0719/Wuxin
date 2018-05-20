<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/26
 * Time: 上午9:42
 * @introduce
 */
namespace app\api\validate;

use think\Validate;

class Learn extends Validate
{
    protected $rule = [
        'openid' => 'require',
        'uid'  => 'require|min:1',
        'to_uid' => 'require|min:1',
        'from_uid'  => 'require|min:1',
    ];
    protected $message = [
        'openid.require'=>'用户openid必须填写',
        'uid.require'=>'用户id必须填写',
        'to_uid.require'=>'用户id必须填写',
        'from_uid.require'=>'用户id必须填写',
        'uid.min' => '用户id必须大于0',
        'to_uid.min' => '用户id必须大于0',
        'from_uid.min' => '用户id必须大于0',
    ];
    protected $scene = [
        'myPage'  => ['openid'],
        'myNeeds' => ['uid'],
        'saveNeeds'=> ['uid'],
        'list' => ['uid'],
        'detail'  => ['from_uid','uid'],
        'contacts' => ['uid'],
        'favorites'=> ['uid'],
        'del-contacts'=>['id'],
        'del-favorites'=>['to_uid'],
        'add-favorites'=>['uid','to_uid'],
    ];
}