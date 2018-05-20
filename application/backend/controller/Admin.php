<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/4/17
 * Time: 上午11:39
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\AdminLogic;
use think\Request;

class Admin extends BaseAdmin
{
    protected $adminLogic;
    protected $adminVa;

    /**
     * Admin constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        $this->adminLogic = new AdminLogic();
        $this->adminVa = new \app\backend\validate\Admin();
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
     * @apiSampleRequest https://miyin.my/backend/admin/login
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "登录成功",
     *   "data": [
     *        "token": "0DuG9pj8rQs7uzm7kHlSikPle3Dzy9FrX1gOBFDhMFPKQYePmUHe5s9ttXEB4jxc+hZJwnCCvfDP3IlGQuvWEJ1K"
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
     * @api {post} /backend/admin/add 后台用户的添加
     * @apiGroup admin
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} admin_name  后台用户名(必须)
     * @apiParam {string} admin_password  后台用户密码(必须)
     * @apiParam {string} admin_mobile  后台用户手机
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   状态响应码 为0时表示无错误发生，大于0时表示发生了特定错误
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://miyin.my/backend/admin/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *   "status": 1,
     *   "message": "添加成功",
     *   "data": [],
     *   "code": 200
     * }
     */
    public function addAdmin()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->adminVa, 'add', $params);
        $result = $this->adminLogic->addAdmin($params);
        return $result;
    }


    public function editAdmin()
    {
        $params = $this->param;
        $result = $this->adminLogic->editAdmin($params);
        return $result;
    }
}