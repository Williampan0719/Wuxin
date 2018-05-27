<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/22
 * Time: 上午9:06
 */

namespace app\backend\controller;

use think\Db;
use think\Request;

class Test extends BaseAdmin
{
    function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function index(){
        return Db::table('wx_attendance')->group('uuid')->field('uuid,SUM(ask_leave) as ask_leave,SUM(absent) as absent')->select();
    }
}