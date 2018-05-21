<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/20
 * Time: 下午8:11
 * @introduce
 */
namespace app\backend\controller;

use think\Request;

class Test extends BaseAdmin
{
    function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function pan()
    {
        echo 'success';
    }
}