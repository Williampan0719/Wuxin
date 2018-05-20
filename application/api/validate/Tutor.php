<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/23
 * Time: 上午11:48
 * @introduce
 */
namespace app\api\validate;

use think\Validate;

class Tutor extends Validate
{
    protected $rule = [
        'openid' => 'require',
        'uid' => 'require|min:1',
        'from_uid'  => 'require',
        'teach_range' => 'require',
        'teach_subject' => 'require',
        'tags' => 'require',
    ];
    protected $message = [
        'openid.require'=>'用户openid必须填写',
        'uid.require'=>'用户id必须填写',
        'uid.min' => '用户id必须大于0',
        'from_uid.require'=>'用户id必须填写',
        'teach_range.require'=>'补习范围必须填写',
        'teach_subject.require'=>'补习科目必须填写',
        'tags.require'=>'特点需求必须填写',
    ];
    protected $scene = [
        'myPage'  => ['openid'],
        'myNeeds' => ['uid'],
        'saveNeeds'=> ['uid'],
        'contacts' => ['uid'],
        'favorites'=> ['uid'],
        'list' => ['uid'],
        'detail'  => ['from_uid','uid'],
    ];
}