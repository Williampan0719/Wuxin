<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/6/1
 * Time: 下午3:02
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\MonitorLogic;
use think\Request;

class Monitor extends BaseAdmin
{
    protected $monitor = null;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->monitor = new MonitorLogic();
    }

    /**
     * @api {post} /backend/monitor/add 监控添加
     * @apiGroup monitor
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {int} room_id 房间号
     * @apiParam {float} sdzx 时段秩序
     * @apiParam {float} xwgf 行为规范
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/monitor/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function addMonitor()
    {
        $param = $this->request->param();
        //$this->paramsValidate($this->attVali, 'add', $param);
        return $this->monitor->addMonitor($param);
    }

    /**
     * @api {get} /backend/monitor/list 监控搜索
     * @apiGroup monitor
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiParam {int} expor 1下载 0读取
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/monitor/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": [
     *                  {
     *                   room_id  房间号
     *                   sdzx  时段秩序
     *                   xwgf  行为规范
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
        return $this->monitor->searchMonitor($param);
    }

    /**
     * @api {post} /backend/monitor/del 监控删除
     * @apiGroup monitor
     * @apiName  del
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/monitor/del
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
        return $this->monitor->deleteList($param);
    }
}