<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/29
 * Time: 下午5:18
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\UserLogic;
use think\Request;

class User extends BaseAdmin
{
    protected $userValidate;
    protected $user;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->userValidate = new \app\backend\validate\User();
        $this->user = new UserLogic();
    }

    /**
     * @api {get} /backend/user/tutor-list 家教列表(停用)
     * @apiGroup user
     * @apiName  tutor-list
     * @apiVersion 1.0.0
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/tutor-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                     "id": 5, //用户id
     *                     "portrait": "https://wx.qlogo.cn/mmopen/vi_32/Q0vjsTA/0", //头像
     *                     "wechat": "13918027532", //微信号
     *                     "openid": "o1iFc5fCO4NN8pwVPuoSv6HmHW8g", //openid
     *                     "body_name": "张缘", //姓名
     *                     "phone": "13918027224", //手机
     *                     "city": "Ningbo", //城市
     *                     "certime": "未认证", //认证状态
     *                     "school": "宁波大学", //学校
     *                     "is_order": 1 //是否预约
     *                  }
     *      },
     *  "code": 104
     *  }
     */
    public function searchTutorList()
    {
        $params = $this->param;
        $result = $this->user->searchTutorList($params);
        return $result;
    }

    /**
     * @api {get} /backend/user/learn-list 家长列表(停用)
     * @apiGroup user
     * @apiName  learn-list
     * @apiVersion 1.0.0
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/learn-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                     "id": 5, //用户id
     *                     "portrait": "https://wx.qlogo.cn/mmopen/vi_32/Q0vjsTA/0", //头像
     *                     "wechat": "13918027532", //微信号
     *                     "openid": "o1iFc5fCO4NN8pwVPuoSv6HmHW8g", //openid
     *                     "body_name": "张缘", //姓名
     *                     "phone": "13918027224", //手机
     *                     "certime": "未认证", //认证状态
     *                     "is_order": 1 //是否预约
     *                  }
     *      },
     *  "code": 104
     *  }
     */
    public function searchLearnList()
    {
        $params = $this->param;
        $result = $this->user->searchLearnList($params);
        return $result;
    }

    /**
     * @api {get} /backend/user/user-list 混合列表
     * @apiGroup user
     * @apiName  user-list
     * @apiVersion 1.0.0
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiParam {int} role 角色
     * @apiParam {string} body_name 姓名
     * @apiParam {string} phone 手机
     * @apiParam {string} city 城市
     * @apiParam {int} is_order 预约
     * @apiParam {int} is_remark 是否备注
     * @apiParam {int} resource_id 渠道id
     * @apiParam {int} certime 认证
     * @apiParam {string} nickname 昵称
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/user-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "total": 39,
     *          "list": [
     *                  {
     *                      "id": 54, 用户id
     *                      "role": 2, 角色
     *                      "body_name": "", 姓名
     *                      "phone": "", 手机
     *                      "certime": "未认证", //认证状态
     *                      "lcity": null,
     *                      "tcity": "广州市",
     *                      "lorder": null,
     *                      "torder": 1,
     *                      "city": "广州市", 城市
     *                      "is_order": 1 //是否预约
     *                  },
     *                 ]
     *      },
     *  "code": 104
     *  }
     */
    public function searchUserList()
    {
        $params = $this->param;
        $result = $this->user->searchUserList($params);
        return $result;
    }

    /**
     * @api {get} /backend/user/tutor-panel 家教面板
     * @apiGroup user
     * @apiName  tutor-panel
     * @apiVersion 1.0.0
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiParam {int} uid 用户id
     * @apiParam {int} class type为3时分类 2购买 3被购买 默认混合
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/tutor-panel
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                     "id": 5, //用户id
     *                     "portrait": "https://wx.qlogo.cn/mmopen/vi_32/Q0vjsTA/0", //头像
     *                     "wechat": "13918027532", //微信号
     *                     "openid": "o1iFc5fCO4NN8pwVPuoSv6HmHW8g", //openid
     *                     "head_name": "张老师", //姓名
     *                     "phone": "13918027224", //手机
     *                     "city": "Ningbo", //城市
     *                     "sex": 2, //性别
     *                     "addtime": "2018-04-10 16:07:23",  //注册时间
     *                     "teach_range": "", //教学范围
     *                     "teach_subject": "", //教学科目
     *                     "tags": "", //特点
     *                     "remark": "", //备注
     *                     "is_order": 1, //是否预约
     *                     "identity": "认证通过", //是否身份认证
     *                     "identity_time": "2018-04-10 16:30:52", //时间
     *                     "education": "认证待审核", //是否学历认证
     *                     "education_time": "",
     *                     "wechat_auth": "未认证", //微信号认证
     *                     "wechat_auth_time": "",
     *                     "school": "浙江工业大学", //大学
     *                     "list": [], //分页列表
     *                     "total": 0,
     *                     "sum": 0
     *                  }
     *      },
     *  "code": 104
     *  }
     */
    public function tutorPanel()
    {
        $params = $this->param;
        $result = $this->user->tutorPanel($params);
        return $result;
    }

    /**
     * @api {get} /backend/user/learn-panel 家长面板
     * @apiGroup user
     * @apiName  learn-panel
     * @apiVersion 1.0.0
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiParam {int} uid 用户id
     * @apiParam {int} type 列表类型 2地址 3投诉 默认 购买
     * @apiParam {int} class type为3时分类 2购买 3被购买 默认混合
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/learn-panel
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                     "id": 5, //用户id
     *                     "portrait": "https://wx.qlogo.cn/mmopen/vi_32/Q0vjsTA/0", //头像
     *                     "wechat": "13918027532", //微信号
     *                     "openid": "o1iFc5fCO4NN8pwVPuoSv6HmHW8g", //openid
     *                     "head_name": "张老师", //姓名
     *                     "phone": "13918027224", //手机
     *                     "city": "Ningbo", //城市
     *                     "sex": 2, //性别
     *                     "addtime": "2018-04-10 16:07:23",  //注册时间
     *                     "learn_range": "", //教学范围
     *                     "learn_subject": "", //教学科目
     *                     "tags": "", //特点
     *                     "remark": "", //备注
     *                     "is_order": 1, //是否预约
     *                     "identity": "认证通过", //是否身份认证
     *                     "identity_time": "2018-04-10 16:30:52", //时间
     *                     "education": "认证待审核", //是否学历认证
     *                     "education_time": "",
     *                     "wechat_auth": "未认证", //微信号认证
     *                     "wechat_auth_time": "",
     *                     "school": "浙江工业大学", //大学
     *                     "list": [], //分页列表
     *                     "total": 0,
     *                     "sum": 0
     *                  }
     *      },
     *  "code": 104
     *  }
     */
    public function learnPanel()
    {
        $params = $this->param;
        $result = $this->user->learnPanel($params);
        return $result;
    }

    /**
     * @api {get} /backend/user/need-config 需求配置列表
     * @apiGroup user
     * @apiName  need-config
     * @apiVersion 1.0.0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/need-config
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *      },
     *  "code": 104
     *  }
     */
    public function getNeedConfig()
    {
        $result = $this->user->getNeedConfig();
        return $result;
    }

    /**
     * @api {get} /backend/user/resource-list 渠道搜索
     * @apiGroup user
     * @apiName  resource-list
     * @apiVersion 1.0.0
     * @apiParam {string} resource 来源
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/resource-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *      },
     *  "code": 104
     *  }
     */
    public function resourceList()
    {
        $param = $this->param;
        $result = $this->user->resourceList($param);
        return $result;
    }

    /**
     * @api {post} /backend/user/contact-refund 退款
     * @apiGroup user
     * @apiName  contact-refund
     * @apiVersion 1.0.0
     * @apiParam {int} uid 购买者id
     * @apiParam {int} to_uid 被购买者id
     * @apiParam {string} editor 操作人
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/contact-refund
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "退款成功",
     *      "data": {
     *      },
     *  "code": 20002
     *  }
     */
    public function contactRefund()
    {
        $params = $this->param;
        $result = $this->user->contactRefund($params);
        return $result;
    }

    /**
     * @api {get} /backend/user/complain-list 投诉列表
     * @apiGroup user
     * @apiName  complain-list
     * @apiVersion 1.0.0
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiParam {string} start_time
     * @apiParam {string} end_time
     * @apiParam {int} type 投诉类别
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/complain-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                  "id": 1,
     *                  "uid": 33, //投诉人
     *                  "to_uid": 34, //被投诉人
     *                  "type": "色情传播", //投诉类型
     *                  "remark": "wwww", // 备注
     *                  "img": "/Users/panhao/workspace/tutor/public/complain/20180408171244_685974.png", //图片
     *                  "status": 0, //处理状态
     *                  "create_at": "2018-04-08 17:12:44", //时间
     *                  "portrait": "https://wx.qlogo.cn/mmopen/vi_32/Q0j4Two66ibyqibJg/0", //头像
     *                  "nickname": "new了个t", //昵称
     *                  "wechat": "wwwasdf", //微信号
     *                  "phone": "18368092182" //手机
     *                  "to_wechat": '' //被投诉人微信
     *                  "to_phone": '' //被投诉人手机
     *                  }
     *      },
     *  "code": 104
     *  }
     */
    public function getComplainList()
    {
        $params = $this->param;
        $result = $this->user->getComplainList($params);
        return $result;
    }

    /**
     * @api {get} /backend/user/edit-complain 投诉编辑
     * @apiGroup user
     * @apiName  edit-complain
     * @apiVersion 1.0.0
     * @apiParam {int} id 投诉id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/edit-complain
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *      "data": {
     *      },
     *  "code": 104
     *  }
     */
    public function editComplain()
    {
        $params = $this->param;
        $result = $this->user->editComplain($params);
        return $result;
    }

    /**
     * @api {post} /backend/user/edit-role 编辑角色
     * @apiGroup user
     * @apiName  edit-role
     * @apiVersion 1.0.0
     * @apiParam {int} uid
     * @apiParam {int} role 1换成家长 2换成家教
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/edit-role
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *      "data": {
     *      },
     *  "code": 102
     *  }
     */
    public function editRole()
    {
        $params = $this->param;
        $result = $this->user->editRole($params);
        return $result;
    }

    /**
     * @api {post} /backend/user/upload-pic 上传图片
     * @apiGroup user
     * @apiName  upload-pic
     * @apiVersion 1.0.0
     * @apiParam {string} pic base64
     * @apiParam {string} type 默认qrcode
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/upload-pic
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "新建成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function uploadPic()
    {
        $params = $this->param;
        $result = $this->user->uploadPic($params);
        return $result;
    }

    /**
     * @api {post} /backend/user/edit-user 编辑用户基本信息
     * @apiGroup user
     * @apiName  edit-user
     * @apiVersion 1.0.0
     * @apiParam {string} body_name 姓名
     * @apiParam {string} idcard 身份证
     * @apiParam {string} school 学校
     * @apiParam {string} diploma 学历
     * @apiParam {string} professional 专业
     * @apiParam {string} phone 手机号
     * @apiParam {string} role 类型(重点)
     * @apiParam {string} range 范围
     * @apiParam {string} subject 科目
     * @apiParam {string} qrcode 二维码
     * @apiParam {string} wechat 微信号
     * @apiParam {string} level 用户分级 ABC
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/edit-user
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *      "data": {
     *      },
     *  "code": 102
     *  }
     */
    public function editUserInfo()
    {
        $params = $this->param;
        $result = $this->user->editUserInfo($params);
        return $result;
    }

    /**
     * @api {post} /backend/user/edit-remark 编辑用户备注
     * @apiGroup user
     * @apiName  edit-remark
     * @apiVersion 1.0.0
     * @apiParam {string} remark 备注
     * @apiparam {string} quick_remark 快速标签
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/edit-remark
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *      "data": {
     *      },
     *  "code": 102
     *  }
     */
    public function editRemark()
    {
        $params = $this->param;
        $result = $this->user->editRemark($params);
        return $result;
    }

    /**
     * @api {post} /backend/user/create-qrcode 创建二维码
     * @apiGroup user
     * @apiName  create-qrcode
     * @apiVersion 1.0.0
     * @apiParam {string} scene 场景
     * @apiParam {string} page 跳转页面 pages/index/index
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/create-qrcode
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *      "data": {
     *      },
     *  "code": 102
     *  }
     */
    public function createQrcode()
    {
        $params = $this->param;
        $result = $this->user->createQrcode($params);
        return $result;
    }

//    public function loadUserInfo()
//    {
//        $params = $this->param;
//        $userlogic = new UserLogic();
//        $result = $userlogic->loadUserInfo($params);
//        return $result;
//    }
}