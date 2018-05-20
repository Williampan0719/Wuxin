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

Route::group('api', function () {
    Route::group('tutor',[
        'test'=>['api/Tutor/test',['method' => 'get']],
        'my'=>['api/Tutor/myPage',['method' => 'get']],
        'my-needs'=>['api/Tutor/myNeeds',['method' => 'get']],
        'save-needs'=>['api/Tutor/saveNeeds',['method' => 'post']],
        'list'=>['api/Tutor/learnList',['method' => 'get']],
        'detail'=>['api/Tutor/learnDetail',['method' => 'get']],
        'contacts'=>['api/Tutor/myContacts',['method' => 'get']],
        'favorites'=>['api/Tutor/myFavorites',['method' => 'get']],
    ]);
    Route::group('learn',[
        'test'=>['api/Learn/test',['method' => 'get']],
        'my'=>['api/Learn/myPage',['method' => 'get']],
        'my-needs'=>['api/Learn/myNeeds',['method' => 'get']],
        'save-needs'=>['api/Learn/saveNeeds',['method' => 'post']],
        'list'=>['api/Learn/tutorList',['method' => 'get']],
        'detail'=>['api/Learn/learnDetail',['method' => 'get']],
        'contacts'=>['api/Learn/myContacts',['method' => 'get']],
        'favorites'=>['api/Learn/myFavorites',['method' => 'get']],
        'del-contacts'=>['api/Learn/delContacts',['method' => 'get']],
        'del-favorites'=>['api/Learn/delFavorites',['method' => 'get']],
        'add-favorites'=>['api/Learn/addFavorites',['method' => 'post']],
    ]);
    Route::group('user',[
        'ureg'=>['api/User/userReg',['method' => 'post']],
        'ucrole'=>['api/User/userChooseRole',['method' => 'post']],
        'uchkpro'=>['api/User/userCheckProcess',['method' => 'post']],
        'ugetphone'=>['api/User/userGetPhone',['method' => 'post']],
        'urlist'=>['api/User/userRoleList',['method' => 'post']],
        'myinfo'=>['api/User/userInfo',['method' => 'post']],
        'mydetail'=>['api/User/userDetail',['method' => 'post']],
        'my-wechat'=>['api/User/userWechat',['method' => 'get']],
        'ucode'=>['api/User/userCode',['method' => 'post']],
        'uccode'=>['api/User/userConCode',['method' => 'post']],
        'order-status'=>['api/User/editOrderStatus',['method' => 'post']],
        'zhima'=>['api/User/zhimaAuth',['method' => 'post']],
        'upload-qrcode'=>['api/User/uploadQrcode',['method' => 'post']],
        'fail-step'=>['api/User/getFailStep',['method' => 'get']],
        'position-list'=>['api/User/getPositionList',['method' => 'get']],
        'add-position'=>['api/User/addPosition',['method' => 'post']],
        'edit-position'=>['api/User/editPosition',['method' => 'post']],
        'del-position'=>['api/User/delPosition',['method' => 'post']],
        'position-status'=>['api/User/editPositionStatus',['method' => 'post']],
        'udipc'=>['api/User/uDipCer',['method' => 'post']],
        'unamec'=>['api/User/uNameCer',['method' => 'post']],
        'ugpayr'=>['api/User/uGetPayRes',['method' => 'get']],
        'usordercan'=>['api/User/uSetOrderCancel',['method' => 'post']],
        'ubw'=>['api/User/uBuyWho',['method' => 'post']],
        'uphead'=>['api/User/upHead',['method' => 'post']],
        'upwechat'=>['api/User/upWechat',['method' => 'post']],
        'save-complain'=>['api/User/saveComplain',['method' => 'post']],
        'getcomlt'=>['api/User/getComList',['method' => 'get']],
        'get-share'=>['api/User/getShare',['method' => 'get']],
        'add-contact'=>['api/User/addContact',['method' => 'post']],
        'after-contact'=>['api/User/afterContact',['method' => 'get']],
        'user-auth'=>['api/User/getUserAuth',['method' => 'get']],
        'ass-detail'=>['api/User/assistantDetail',['method' => 'get']],
    ]);
    Route::group('WxSign', [
        'verify-wechat' => ['api/WxSign/wechat'],
        'openid-mobile' => ['api/WxSign/getOpenidByMobile', ['method' => 'get']],
        'upload-qrcode' => ['api/WxSign/uploadQrcode', ['method' => 'post']],
    ]);
    Route::group('wec',[
        'not'=>['api/Wechat/notify',['method' => 'post']],
        'notbuy'=>['api/Wechat/notifybuy',['method' => 'post']],
    ]);
    Route::group('wmodel',[
        'aforid'=>['api/Formid/addFormId',['method' => 'post']],
    ]);
    Route::group('conf',[
        'getaut'=>['api/Config/getAut',['method' => 'get']],
        'getbuy'=>['api/Config/getBuy',['method' => 'get']],
    ]);
});
Route::group('backend', function () {
    Route::group('admin',[
        'login'=>['backend/Admin/login',['method' => 'post']],
        'add'=>['backend/Admin/addAdmin',['method' => 'post']],
        'logout'=>['backend/Admin/logout',['method' => 'post']],
        'edit'=>['backend/Admin/editAdmin',['method' => 'post']],
    ]);
    Route::group('assistant',[
        'add'=>['backend/Assistant/addOne',['method' => 'post']],
        'edit'=>['backend/Assistant/editOne',['method' => 'post']],
        'del'=>['backend/Assistant/delOne',['method' => 'post']],
        'search'=>['backend/Assistant/searchList',['method' => 'get']],
        'city'=>['backend/Assistant/getCityList',['method' => 'get']],
    ]);
    Route::group('config',[
        'getlist'=>['backend/Config/getList',['method' => 'get']],
        'setamount'=>['backend/Config/setAmount',['method' => 'get']],
        'setcompl'=>['backend/Config/setComplaints',['method' => 'get']],
        'del-config' => ['backend/Config/delConfig', ['method' => 'post']],
        'getcomlt' => ['backend/Config/getComList', ['method' => 'get']],
    ]);
    Route::group('user', [
        'complain-list' => ['backend/User/getComplainList', ['method' => 'get']],
        'edit-complain' => ['backend/User/editComplain', ['method' => 'get']],
        'edit-role' => ['backend/User/editRole', ['method' => 'post']],
        'contact-refund' => ['backend/User/contactRefund', ['method' => 'post']],
        'tutor-list'  => ['backend/User/searchTutorList', ['method' => 'get']],
        'learn-list'  => ['backend/User/searchLearnList', ['method' => 'get']],
        'user-list'  => ['backend/User/searchUserList', ['method' => 'get']],
        'tutor-panel' => ['backend/User/tutorPanel', ['method' => 'get']],
        'learn-panel' => ['backend/User/learnPanel', ['method' => 'get']],
        'need-config' => ['backend/User/getNeedConfig', ['method' => 'get']],
        'resource-list' => ['backend/User/resourceList', ['method' => 'get']],
        'upload-pic' => ['backend/User/uploadPic', ['method' => 'post']],
        'edit-user' => ['backend/User/editUserInfo', ['method' => 'post']],
        'edit-remark' => ['backend/User/editRemark', ['method' => 'post']],
        'create-qrcode' => ['backend/User/createQrcode', ['method' => 'post']],
    ]);
    Route::group('audit',[
        'fail-step'   => ['backend/Audit/getFailStep',['method' => 'get']],
        'wechat-audit'   => ['backend/Audit/wechatAudit',['method' => 'get']],
        'edu-audit'   => ['backend/Audit/educationAudit',['method' => 'get']],
        'qrcode-audit'   => ['backend/Audit/qrcodeAudit',['method' => 'get']],
        'set-audit'   => ['backend/Audit/setAudit',['method' => 'post']],
    ]);
    Route::group('remark',[
        'list'   => ['backend/remarkConfig/remarkList',['method' => 'get']],
        'add'   => ['backend/remarkConfig/addRemark',['method' => 'post']],
        'edit'   => ['backend/remarkConfig/editRemark',['method' => 'post']],
        'del'   => ['backend/remarkConfig/delRemark',['method' => 'post']],
    ]);
    Route::group('resource',[
        'resource-list'   => ['backend/Resource/resourceList',['method' => 'get']],
        'create-resource'   => ['backend/Resource/createResource',['method' => 'post']],
        'edit-resource'   => ['backend/Resource/editResource',['method' => 'post']],
    ]);
    Route::group('statistic',[
        'search-index'   => ['backend/Statistic/searchIndex',['method' => 'get']],
        'search-detail'   => ['backend/Statistic/searchDetail',['method' => 'get']],
    ]);
    Route::group('wechatMenu',[
        'create-menu'   => ['backend/WechatMenu/create_menu',['method' => 'get']],
    ]);
    Route::group('trad',[
        'buylist'=>['backend/Trading/buyList',['method' => 'get']],
        'reflist'=>['backend/Trading/refundList',['method' => 'get']],
        'audlist'=>['backend/Trading/auditList',['method' => 'get']],
        'refund-log'=>['backend/Trading/refundLog',['method' => 'get']],
        'contact-list'=>['backend/Trading/searchContactList',['method' => 'get']],
    ]);
});
