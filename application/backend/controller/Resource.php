<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/17
 * Time: 下午3:34
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\ResourceLogic;
use think\Request;

class Resource extends BaseAdmin
{
    protected $resource = null;
    protected $resourceV = null;

    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->resource = new ResourceLogic();
        $this->resourceV = new \app\backend\validate\Resource();
    }

    /**
     * @api {post} /backend/resource/resource-list 来源渠道列表
     * @apiGroup resource
     * @apiName  resource-list
     * @apiVersion 1.0.0
     * @apiParam {int} page
     * @apiParam {int} size
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/resource/resource-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *
     * }
     *  "code": 104
     *  }
     */
    public function resourceList()
    {
        $params = $this->request->param();
        return $this->resource->resourceList($params);
    }


    /**
     * @api {post} /backend/resource/create-resource 创建来源渠道
     * @apiGroup resource
     * @apiName  create-resource
     * @apiVersion 1.0.0
     * @apiParam {int} direction 1线上 2线下
     * @apiParam {int} role 1家长2家教
     * @apiParam {string} name 姓名
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/resource/create-resource
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *
     * }
     *  "code": 104
     *  }
     */
    public function createResource()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->resourceV, 'add', $params);
        return $this->resource->createResource($params);
    }

    /**
     * @api {post} /backend/resource/edit-resource 编辑来源渠道
     * @apiGroup resource
     * @apiName  edit-resource
     * @apiVersion 1.0.0
     * @apiParam {int} direction 1线上 2线下
     * @apiParam {int} role 1家长2家教
     * @apiParam {string} name 姓名
     * @apiParam {int} id 编辑id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/resource/edit-resource
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *
     * }
     *  "code": 104
     *  }
     */
    public function editResource()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->resourceV, 'edit', $params);
        return $this->resource->editResource($params);
    }
}