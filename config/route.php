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
        'list' => ['backend/Admin/adminList', ['method' => 'get']],
        'menu-list' => ['backend/Admin/menuList', ['method' => 'get']],
        'login'=>['backend/Admin/login',['method' => 'post']],
        'set-status' => ['backend/Admin/setStatus', ['method' => 'post']],
        'add'=>['backend/Admin/adminAdd',['method' => 'post']],
        'logout'=>['backend/Admin/logout',['method' => 'post']],
        'edit'=>['backend/Admin/adminEdit',['method' => 'post']],
        'detail' => ['backend/Admin/adminDetail', ['method' => 'get']],
        'delete' => ['backend/Admin/adminDelete', ['method' => 'post']],
        'add-html'=>['backend/Admin/adminAddHtml',['method'=>'get']],
        'upd-pwd'=>['backend/Admin/adminUpdPwd',['method'=>'post']],
        'role-all'=>['backend/Admin/roleAll',['method'=>'get']],
        'admin-role-add'=>['backend/Admin/adminRoleAdd',['method'=>'post']],

    ]);
    Route::group('user', [
        'add' => ['backend/User/addUser', ['method' => 'post']],
        'list' => ['backend/User/userList', ['method' => 'get']],
    ]);
    Route::group('att', [
        'add' => ['backend/Attendance/addAttendance', ['method' => 'post']],
        'list' => ['backend/Attendance/searchSum', ['method' => 'get']],
        'del' => ['backend/Attendance/deleteList', ['method' => 'post']],
    ]);
    Route::group('inspection', [
        'add' => ['backend/Inspection/addInspection', ['method' => 'post']],
        'list' => ['backend/Inspection/searchSum', ['method' => 'get']],
        'del' => ['backend/Inspection/deleteList', ['method' => 'post']],
    ]);
    Route::group('team', [
        'add' => ['backend/Team/addTeam', ['method' => 'post']],
        'list' => ['backend/Team/searchSum', ['method' => 'get']],
        'del' => ['backend/Team/deleteList', ['method' => 'post']],
    ]);
    Route::group('control', [
        'add' => ['backend/Control/addControl', ['method' => 'post']],
        'list' => ['backend/Control/searchSum', ['method' => 'get']],
        'del' => ['backend/Control/deleteList', ['method' => 'post']],
    ]);
    Route::group('monitor', [
        'add' => ['backend/Monitor/addMonitor', ['method' => 'post']],
        'list' => ['backend/Monitor/searchSum', ['method' => 'get']],
        'del' => ['backend/Monitor/deleteList', ['method' => 'post']],
    ]);
    Route::group('room', [
        'add' => ['backend/Room/addRoom', ['method' => 'post']],
        'list' => ['backend/Room/roomList', ['method' => 'get']],
    ]);
});
