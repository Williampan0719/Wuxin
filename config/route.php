<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::group('backend', function () {
    Route::group('admin',[
        'login'=>['backend/Admin/login',['method' => 'post']],
        'add'=>['backend/Admin/addAdmin',['method' => 'post']],
        'logout'=>['backend/Admin/logout',['method' => 'post']],
        'edit'=>['backend/Admin/editAdmin',['method' => 'post']],
    ]);
});
