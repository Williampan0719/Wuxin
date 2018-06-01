<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/6/1
 * Time: 下午3:02
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\ControlLogic;
use think\Request;

class Control extends BaseAdmin
{
    protected $control = null;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->control = new ControlLogic();
    }

    /**
     * @api {post} /backend/control/add 管教添加
     * @apiGroup control
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} uuid 警员id
     * @apiParam {float} swxc 上午巡查
     * @apiParam {float} swjjs 上午进监室
     * @apiParam {float} swsdw 上午三定位
     * @apiParam {float} xwxc 下午巡查
     * @apiParam {float} xwjjs 下午进监室
     * @apiParam {float} xwsdw 下午三定位
     * @apiParam {float} laq 六安全
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/control/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function addControl()
    {
        $param = $this->request->param();
        //$this->paramsValidate($this->attVali, 'add', $param);
        return $this->control->addControl($param);
    }

    /**
     * @api {get} /backend/control/list 管教搜索
     * @apiGroup control
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/control/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": [
     *                  {
     *                   uuid  警员id
     *                   swxc  上午巡查
     *                   swjjs  上午进监室
     *                   swsdw  上午三定位
     *                   xwxc  下午巡查
     *                   xwjjs  下午进监室
     *                   xwsdw  下午三定位
     *                   laq  六安全
     *                  },
     *                  ],
     *          "total": 3
     *      },
     *  "code": 104
     *  }
     */
    public function searchSum()
    {
        $param = $this->request->param();
        return $this->control->searchControl($param);
    }

    /**
     * @api {post} /backend/control/del 管教删除
     * @apiGroup control
     * @apiName  del
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/control/del
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "删除成功",
     *      "data": {
     *      },
     *  "code": 103
     *  }
     */
    public function deleteList()
    {
        $param = $this->request->param();
        return $this->control->deleteList($param);
    }
}