<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/27
 * Time: 上午10:27
 */

namespace app\api\validate;


use think\Validate;

class UserValidate extends Validate
{
    protected $rule =   [
        'phone'  => 'require|max:11',
        'wechat'   => 'require',
        'name'     => 'require|max:50',
        'idcard'     => 'require|max:18|min:15',
        'code'     => 'require',
        'encryptedData'     => 'require',
        'iv'     => 'require',
        'uid' => 'require',
        'to_uid' => 'require',
        'type' => 'require',
        'id' => 'require',
    ];
    protected $msg = [
        'phone.require' => 900,
        'phone.max'     => 901,
        'wechat.require'     => 902,
        'name.require'  => 903,
        'name.max'  => 904,
        'idcard.require'  => 905,
        'idcard.max'  => 906,
        'idcard.min'  => 906,
        'code.require'  => 907,
        'encryptedData.require'  => 908,
        'iv.require'  => 909,
        'uid.require'=>'用户id必须填写',
        'to_uid.require'=> '用户id必须填写',
        'type.require' => '投诉类型必填',
        'id.require'=>'id必须填写',
    ];
    protected $scene = [
        'UserInfo'   =>  ['phone','wechat','code'],
        'UserMobile'   =>  ['code','encryptedData','iv'],
        'position-list'   => ['uid'],
        'add-position' => ['uid'],
        'edit-position' => ['uid'],
        'del-position' => ['uid','id'],
        'save-complain' => ['uid','to_uid','type'],
        'zhima' => ['uid','idcard','name'],
    ];
}