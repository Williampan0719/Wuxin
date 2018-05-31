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
     * @api {post} /backend/user/add 添加警员
     * @apiGroup user
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} name 警员姓名
     * @apiParam {string} uuid 警员id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function addUser()
    {
        $params = $this->param;
        $this->paramsValidate($this->userValidate, 'add', $params);
        $result = $this->user->addUser($params);
        return $result;
    }

    /**
     * @api {get} /backend/user/list 警员列表
     * @apiGroup user
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/user/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function userList()
    {
        $result = $this->user->userList();
        return $result;
    }
}