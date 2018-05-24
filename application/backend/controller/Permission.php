<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/12/8
 * Time: 上午10:39
 */

namespace app\backend\controller;


use app\backend\logic\PermissionLogic;
use think\Request;

class Permission extends BaseAdmin
{
    protected $permissionLogic=null;
    protected $permissionValidate=null;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->permissionLogic=new PermissionLogic();
        $this->permissionValidate=new \app\backend\validate\Permission();

    }

    /**
     * @api {get} /backend/permission/list 后台节点的列表
     * @apiGroup permission
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {int} page  页数
     * @apiParam {int} size  页码
     * @apiParam {string} keyword  关键词(admin_name)
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://miyin.my/backend/permission/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     *  }
     */
    public function permissionList()
    {
        $result=$this->permissionLogic->permissionList();
        return $result;
    }
    /**
     * @api {get} /backend/permission/add 后台节点的添加
     * @apiGroup permission
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} name  菜单名
     * @apiParam {int} show  是否显示 0 不显示 1显示
     * @apiParam {int} pid  父id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://miyin.my/backend/permission/add
     * @apiSuccessExample {json} Response 200 Example
     *
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     *  }
     */
    public function permissionAdd()
    {
        $params=$this->request->param();
        $this->paramsValidate($this->permissionValidate,'add',$params);
        $result=$this->permissionLogic->permissionAdd($params);
        return $result;
    }
    /**
     * @api {get} /backend/permission/detail 后台节点详情
     * @apiGroup permission
     * @apiName  detail
     * @apiVersion 1.0.0
     * @apiParam {int} id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://miyin.my/backend/permission/detail
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     *  }
     */
    public function permissionDetail()
    {
        $params=$this->request->param();
        $this->paramsValidate($this->permissionValidate,'detail',$params);
        $result=$this->permissionLogic->permissionDetail($params);
        return $result;
    }
    /**
     * @api {get} /backend/permission/edit 后台节点修改
     * @apiGroup permission
     * @apiName  edit
     * @apiVersion 1.0.0
     * @apiParam {string} name  字段名
     * @apiParam {int} show  是否显示
     * @apiParam {int} pid  父id
     * @apiParam {int} id  id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://miyin.my/backend/permission/edit
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "修改成功",
     *   "data": [],
     *   "code": 201
     *  }
     */
    public function permissionEdit()
    {
        $params=$this->request->param();
        $this->paramsValidate($this->permissionValidate,'edit',$params);
        $result=$this->permissionLogic->permissionEdit($params);
        return $result;
    }
}