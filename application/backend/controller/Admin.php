<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/12/8
 * Time: 上午10:36
 */

namespace app\backend\controller;


use app\backend\logic\AdminLogic;
use app\backend\logic\RoleLogic;
use think\Request;

class Admin extends BaseAdmin
{
    protected $adminLogic;
    protected $adminValidate;

    /**
     * Admin constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->adminLogic = new AdminLogic();
        $this->adminValidate = new \app\backend\validate\Admin();

    }

    /**
     * @api {get} /backend/admin/list 后台用户的列表
     * @apiGroup admin
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {int} page  页数
     * @apiParam {int} size  页码
     * @apiParam {string} keyword  关键词(admin_name)
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSuccess {int} list.status 状态 1:正常 2:禁用
     * @apiSuccess {string} list.admin_name   账号
     * @apiSuccess {string} list.admin_mobile 手机号
     * @apiSuccess {string} list.login_ip 登录IP
     * @apiSuccess {string} list.login_at 登录时间
     * @apiSuccess {string} list.created_at 创建时间
     * @apiSuccess {int} list.is_super 是否超级管理员 0:否 1:是
     * @apiSuccess {string} list.remark 备注
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *     "status": 1,
     *     "message": "获取成功",
     *     "data": {
     *          "list": [
     *              {
     *                  "admin_id": 5,
     *                  "admin_name": "liyongchuan2",
     *                  "admin_mobile": "13486627342",
     *                  "is_super": 0,
     *                  "status": 1,
     *                  "login_ip": "",
     *                  "login_at": null,
     *                  "remark": "",
     *                  "created_at": "2018-01-02 10:28:49",
     *              }
     *              ],
     *          "total": 1
     *        },
     *     "code": 202
     * }
     */
    public function adminList()
    {
        $params = $this->param;
        $result = $this->adminLogic->adminList($params);
        return $result;
    }

    /**
     * @api {get} /backend/admin/detail 后台用户的详情
     * @apiGroup admin
     * @apiName  detail
     * @apiVersion 1.0.0
     * @apiParam {int} admin_id  后台用户ID(必须)
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSuccess {Object} datail 数据部分,忽略
     * @apiSuccess {Object} datail 数据部分,忽略
     * @apiSuccess {string} admin_name   账号
     * @apiSuccess {string} admin_mobile 手机号
     * @apiSuccess {int} is_super 是否超级管理员 0:否 1:是
     * @apiSuccess {string} remark 备注
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/detail
     * @apiSuccessExample {json} Response 200 Example
     * {
     *     "status": 1,
     *     "message": "获取成功",
     *     "data": {
     *          "datail": {
     *                 "admin_name": "zhanglei",
     *                  "admin_mobile": "15968400324",
     *                  "is_super": 0,
     *                  "remark": ""
     *              },
     *     },
     *     "code": 202
     * }
     */
    public function adminDetail()
    {
        $params = $this->request->param();
        $result = $this->adminLogic->adminDetail($params);
        return $result;
    }

    /**
     * @api {post} /backend/admin/add 后台用户的添加
     * @apiGroup admin
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} admin_name  后台用户名(必须)
     * @apiParam {string} admin_password  后台用户密码(必须)
     * @apiParam {string} admin_mobile  后台用户手机
     * @apiParam {int} is_super  是否超级管理员 0:否 1:是
     * @apiParam {string} remark  后台用户描述
     * @apiParam {string} city 城市,选填
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "添加成功",
     *   "data": [],
     *   "code": 200
     * }
     */
    public function adminAdd()
    {

        $params = $this->request->param();
        $this->paramsValidate($this->adminValidate, 'add', $params);
        $result = $this->adminLogic->adminAdd($params);
        return $result;

    }

    /**
     * @api {post} /backend/admin/edit 后台用户的修改
     * @apiGroup admin
     * @apiName  edit
     * @apiVersion 1.0.0
     * @apiParam {int} admin_id  后台用户ID(必须)
     * @apiParam {string} admin_name  后台用户名(必须)
     * @apiParam {string} admin_mobile  后台用户手机
     * @apiParam {int} is_super  是否超级管理员 0:否 1:是
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/edit
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     * }
     */
    public function adminEdit()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->adminValidate, 'edit', $params);
        $result = $this->adminLogic->adminEdit($params);
        return $result;
    }


    /**
     * @api {post} /backend/admin/set-status 禁用用户
     * @apiGroup admin
     * @apiName  set-status
     * @apiVersion 1.0.0
     * @apiParam {int} admin_id  后台用户ID(必须)
     * @apiParam {int} status  状态 1:正常 2:禁用
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/set-status
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "操作成功",
     *   "data": [],
     *   "code": 201
     * }
     */
    public function setStatus()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->adminValidate, 'setStatus', $params);
        $result = $this->adminLogic->adminEdit($params);
        return $result;
    }



    /**
     * @api {post} /backend/admin/delete 后台用户的删除
     * @apiGroup admin
     * @apiName  delete
     * @apiVersion 1.0.0
     * @apiParam {int} admin_id  后台用户ID(必须)
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/delete
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "删除成功",
     *   "data": [],
     *   "code": 203
     * }
     */
    public function adminDelete()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->adminValidate, 'delete', $params);
        $result = $this->adminLogic->adminDelete($params);
        return $result;
    }

    /**
     * @api {get} /backend/admin/add-html 后台用户的添加页面
     * @apiGroup admin
     * @apiName  add-html
     * @apiVersion 1.0.0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/add-html
     * @apiSuccessExample {json} Response 200 Example
     * {
     *    "status": 1,
     *    "message": "获取成功",
     *    "data": {
     *          "all": [
     *              {
     *                  "id": 1,
     *                  "display_name": "客服"
     *              },
     *              {
     *                  "id": 2,
     *                  "display_name": "老板"
     *              },
     *              {
     *                  "id": 3,
     *                  "display_name": "老板1"
     *              }
     *              ]
     *          },
     *    "code": 202
     * }
     */
    public function adminAddHtml()
    {
        $roleLogic = new RoleLogic();
        $result = $roleLogic->roleAll();
        return $result;
    }

    /**
     * @api {post} /backend/admin/upd-pwd 后台用户的修改密码
     * @apiGroup admin
     * @apiName  upd-pwd
     * @apiVersion 1.0.0
     * @apiParam {int} admin_id  后台用户ID(必须)
     * @apiParam {string} admin_password  后台用户密码(必须)
     * @apiParam {string} new_password  后台用户新密码
     * @apiParam {string} new_password_confirm  后台用户新密码确认
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/upd-pwd
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     * }
     */
    public function adminUpdPwd()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->adminValidate, 'updpwd', $params);
        $result = $this->adminLogic->adminUpdPwd($params);
        return $result;
    }

    /**
     * @api {get} /backend/admin/role-all 所有角色
     * @apiGroup admin
     * @apiName  role-all
     * @apiVersion 1.0.0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/role-all
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *      "list": [
     *          {
     *              "id": 16,       //角色id
     *              "name": "助教"   //角色名称
     *          }
     *      ]
     *  },
     *  "code": 200
     * }
     */
    public function roleAll()
    {
        $roleLogic = new RoleLogic();
        $result = $roleLogic->roleAll();
        return $result;
    }

    /**
     * @api {post} /backend/admin/admin-role-add 账户角色的添加
     * @apiGroup admin
     * @apiName  admin-role-add
     * @apiVersion 1.0.0
     * @apiParam {int}  admin_id
     * @apiParam {string}  role_id  以,隔开的role_id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/admin-role-add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     * }
     */
    public function adminRoleAdd()
    {
        $params['admin_id'] = $this->paramValidate('admin_id');
        $params['role_id'] = $this->paramValidate('role_id');
        $result = $this->adminLogic->adminRoleAdd($params);
        return $result;
    }


    /**
     * @api {post} /backend/admin/login 用户登录
     * @apiGroup admin
     * @apiName  login
     * @apiVersion 1.0.0
     * @apiParam {string} admin_name 账号
     * @apiParam {string} admin_password 密码
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/login
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [
     *  ],
     *   "code": 201
     * }
     */
    public function login()
    {
        $params = $this->param;
        $result = $this->adminLogic->login($params);
        return $result;
    }



    /**
     * @api {post} /backend/admin/logout 用户退出
     * @apiGroup admin
     * @apiName  logout
     * @apiVersion 1.0.0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/logout
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "退出成功",
     *   "data": [
     *  ],
     *   "code": 201
     * }
     */
    public function logout()
    {
        $params = $this->param;
        $token = $params['token'];
        $result = $this->adminLogic->logout($token);
        return $result;
    }



    /**
     * @api {get} /backend/admin/menu-list 获取菜单列表(停用)
     * @apiGroup admin
     * @apiName  menu-list
     * @apiVersion 1.0.0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/menu-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *  "list": [
     *              {
     *              "id": 24,               //ID
     *              "name": "用户管理",     //节点名称
     *              "display_name": "",
     *              "description": "",     //描述
     *              "pid": 0,           //父节点ID
     *              "level": 1,         //级别
     *              "children": [       //子节点列表
     *                  {
     *                      "id": 25,
     *                      "name": "用户列表",
     *                      "display_name": "",
     *                      "description": "",
     *                      "pid": 24,
     *                      "level": 2,
     *                      "children": []
     *                  }
     *              ]
     *          },
     *      ]
     *  },
     *  "code": 200
     *  }
     */
    public function menuList()
    {
        $params['admin_id'] = 13;
        $params['isuper'] = 1;
        //$params = $this->param;
        $adminId = $params['admin_id'];
        $isuper = $params['isuper'];
        $result = $this->adminLogic->menuList($adminId,$isuper);
        return $result;
    }

}