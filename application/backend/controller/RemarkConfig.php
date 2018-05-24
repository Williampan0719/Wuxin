<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/18
 * Time: 下午3:36
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\RemarkConfigLogic;
use think\Request;

class RemarkConfig extends BaseAdmin
{
    protected $remark = null;

    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->remark = new RemarkConfigLogic();
    }

    /**
     * @api {get} /backend/remark/list 读取备注配置
     * @apiGroup remark
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {int} type 0通用 1家长 2家教
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/remark/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *  "data": {
     *          "total": 2,
     *          "list": [
     *              {
     *                  "id": 2,
     *                  "type": "家长",
     *                  "name": "测试家长标签",
     *                  "create_at": "2018-05-18 15:53:36"
     *              },
     *              {
     *                  "id": 1,
     *                  "type": "通用",
     *                  "name": "测试通用标签",
     *                  "create_at": "2018-05-18 15:53:14"
     *              }
     *          ]
     * }
     *  "code": 104
     *  }
     */
    public function remarkList()
    {
        $params = $this->request->param();
        return $this->remark->remarkList($params);
    }

    /**
     * @api {get} /backend/remark/user 用户备注列表
     * @apiGroup remark
     * @apiName  user
     * @apiVersion 1.0.0
     * @apiParam {int} role 角色
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/remark/user
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *          "list": [
     *              {
     *                  "id": 2,
     *                  "type": "家长",
     *                  "name": "测试家长标签",
     *                  "create_at": "2018-05-18 15:53:36"
     *              },
     *              {
     *                  "id": 1,
     *                  "type": "通用",
     *                  "name": "测试通用标签",
     *                  "create_at": "2018-05-18 15:53:14"
     *              }
     *          ]
     * }
     *  "code": 104
     *  }
     */
    public function userRemark()
    {
        $params = $this->request->param();
        return $this->remark->userRemark($params);
    }

    /**
     * @api {post} /backend/remark/add 添加备注配置
     * @apiGroup remark
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {int} type 0通用 1家长 2家教
     * @apiParam {string} name 姓名
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/remark/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *  "data": {
     * }
     *  "code": 101
     *  }
     */
    public function addRemark()
    {
        $params = $this->request->param();
        return $this->remark->addRemark($params);
    }

    /**
     * @api {post} /backend/remark/edit 修改备注配置
     * @apiGroup remark
     * @apiName  edit
     * @apiVersion 1.0.0
     * @apiparam {int} id 修改id
     * @apiParam {int} type 0通用 1家长 2家教
     * @apiParam {string} name 姓名
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/remark/edit
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": {
     * }
     *  "code": 102
     *  }
     */
    public function editRemark()
    {
        $params = $this->request->param();
        return $this->remark->editRemark($params);
    }

    /**
     * @api {post} /backend/remark/del 删除备注配置
     * @apiGroup remark
     * @apiName  del
     * @apiVersion 1.0.0
     * @apiParam {int} id 修改id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/remark/del
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "删除成功",
     *  "data": {
     * }
     *  "code": 103
     *  }
     */
    public function delRemark()
    {
        $params = $this->request->param();
        return $this->remark->delRemark($params['id']);
    }
}