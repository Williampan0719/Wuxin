<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/12/8
 * Time: 上午10:37
 */

namespace app\backend\controller;


use app\backend\logic\RoleLogic;
use think\Request;

class Role extends BaseAdmin
{
    protected $roleLogic;
    protected $roleValidate;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->roleLogic = new RoleLogic();
        $this->roleValidate = new \app\backend\validate\Role();
    }

    /**
     * @api {post} /backend/role/add 角色的添加
     * @apiGroup role
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} name  角色名(必须)
     * @apiParam {string} display_name  角色显示名(必须)
     * @apiParam {string} description  角色描述
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/role/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "添加成功",
     *   "data": [],
     *   "code": 200
     * }
     */
    public function roleAdd()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->roleValidate, 'add', $params);
        $result = $this->roleLogic->roleAdd($params);
        return $result;
    }
    /**
     * @api {get} /backend/role/detail 角色的详情
     * @apiGroup role
     * @apiName  detail
     * @apiVersion 1.0.0
     * @apiParam {int} id  角色ID(必须)
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/admin/role
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "获取成功",
     *   "data": {
     *         "datail": {
     *              "name": "boss2",
     *               "display_name": "老板2",
     *               "description": ""
     *            }
     *          },
     *   "code": 202
     * }
     */
    public function roleDetail()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->roleValidate, 'detail', $params);
        $result = $this->roleLogic->roleDetail($params);
        return $result;
    }
    /**
     * @api {post} /backend/role/edit 角色修改
     * @apiGroup role
     * @apiName  edit
     * @apiVersion 1.0.0
     * @apiParam {int} id  角色ID(必须)
     * @apiParam {string} name  角色名(必须)
     * @apiParam {string} display_name  角色显示名
     * @apiParam {string} description  角色描述
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/role/edit
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     * }
     */
    public function roleEdit()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->roleValidate, 'edit', $params);
        $result = $this->roleLogic->roleEdit($params);
        return $result;
    }
    /**
     * @api {post} /backend/role/delete 角色的删除
     * @apiGroup role
     * @apiName  delete
     * @apiVersion 1.0.0
     * @apiParam {int} id  角色ID(必须)
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/role/delete
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "删除成功",
     *   "data": [],
     *   "code": 203
     * }
     */
    public function roleDelete()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->roleValidate, 'delete', $params);
        $result = $this->roleLogic->roleDelete($params);
        return $result;
    }
    /**
     * @api {get} /backend/role/list 角色的列表
     * @apiGroup role
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {int} page  页数
     * @apiParam {int} size  页码
     * @apiParam {string} keyword  关键词(display_name)
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/role/list
     * @apiSuccessExample {json} Response 200 Example
     *  {
     *     "status": 1,
     *     "message": "获取成功",
     *     "data": {
     *          "list": [
     *              {
     *                  "id": 1,
     *                  "name": "customer",
     *                  "display_name": "客服",
     *                  "description": "客服人员",
     *                  "created_at": "2017-12-11 09:16:09",
     *                  "updated_at": "2017-12-11 09:16:14",
     *                  "deleted_at": null
     *              }
     *          ],
     *          "total": 1
     *          },
     *     "code": 202
     *  }
     */
    public function roleList()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->roleValidate, 'list', $params);
        $result = $this->roleLogic->roleList($params);
        return $result;
    }

    /**
     * @api {get} /backend/role/html 角色权限的详情
     * @apiGroup role
     * @apiName  html
     * @apiVersion 1.0.0
     * @apiParam {int} id  role_id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/role/html
     * @apiSuccessExample {json} Response 200 Example
     *  {
     *      "status": 1,
     *      "message": "获取成功",
     *      "data": {
     *          "list": [   //节点ID列表
     *                      24,
     *                      25
     *              ]
     *          },
     *      "code": 200
     * }
     *
     */
    public function rolePermissionHtml()
    {
        $params=$this->request->param();
        $this->paramsValidate($this->roleValidate, 'detail', $params);
        $result = $this->roleLogic->rolePermissionHtml($params);
        return $result;
    }
    /**
     * @api {get} /backend/role/permission-all 角色权限所有
     * @apiGroup role
     * @apiName  permission-all
     * @apiVersion 1.0.0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/role/permission-all
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
    public function permissionAll()
    {
        $result = $this->roleLogic->permissionAll();
        return $result;
    }
    /**
     * @api {post} /backend/role/permission-add 角色权限的添加
     * @apiGroup role
     * @apiName  permission-add
     * @apiVersion 1.0.0
     * @apiParam {int} id  role_id
     * @apiParam {string} permission_id字符串,以,隔开
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/role/permission-add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     *  }
     */
    public function rolePermissionAdd()
    {
        $params=$this->request->param();
        $this->paramsValidate($this->roleValidate, 'permission', $params);
        $result = $this->roleLogic->rolePermissionAdd($params);
        return $result;
    }
}