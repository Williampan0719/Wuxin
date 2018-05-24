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
        'complain-list' => ['backend/User/getComplainList', ['method' => 'get']],
        'edit-complain' => ['backend/User/editComplain', ['method' => 'get']],
        'edit-role' => ['backend/User/editRole', ['method' => 'post']],
        'contact-refund' => ['backend/User/contactRefund', ['method' => 'post']],
        'tutor-list'  => ['backend/User/searchTutorList', ['method' => 'get']],
        'learn-list'  => ['backend/User/searchLearnList', ['method' => 'get']],
        'user-list'  => ['backend/User/searchUserList', ['method' => 'get']],
        'front-list'  => ['backend/User/frontList', ['method' => 'get']],
        'tutor-panel' => ['backend/User/tutorPanel', ['method' => 'get']],
        'learn-panel' => ['backend/User/learnPanel', ['method' => 'get']],
        'need-config' => ['backend/User/getNeedConfig', ['method' => 'get']],
        'resource-list' => ['backend/User/resourceList', ['method' => 'get']],
        'upload-pic' => ['backend/User/uploadPic', ['method' => 'post']],
        'edit-user' => ['backend/User/editUserInfo', ['method' => 'post']],
        'edit-remark' => ['backend/User/editRemark', ['method' => 'post']],
        'create-qrcode' => ['backend/User/createQrcode', ['method' => 'post']],
    ]);
});
