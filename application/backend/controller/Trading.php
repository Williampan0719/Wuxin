<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/9
 * Time: 上午10:22
 */

namespace app\backend\controller;


use app\backend\logic\TradingLogic;
use think\Request;

class Trading extends BaseAdmin
{
    protected $trading = null;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->trading = new TradingLogic();
    }
    /**
     * @api {get} /backend/trad/audlist 获取认证支付流水(停用)
     * @apiGroup trad
     * @apiName  audlist
     * @apiVersion 1.0.0
     * @apiParam {int} start_time
     * @apiParam {int} end_time
     * @apiParam {int} phone
     * @apiParam {int} status -1取消,1成功，2失败
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/trad/audlist
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function auditList(){
        $param = $this->param;
        return $this->trading->AuditWater($param);

    }
    /**
     * @api {get} /backend/trad/reflist 获取认证退款流水(停用)
     * @apiGroup trad
     * @apiName  reflist
     * @apiVersion 1.0.0
     *
     * @apiParam {int} start_time
     * @apiParam {int} end_time
     * @apiParam {int} phone
     * @apiParam {int} status -1取消,1成功，2失败
     *
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/trad/reflist
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function refundList(){
        $param = $this->param;
        return $this->trading->RefundWater($param);
    }

    /**
     * @api {get} /backend/trad/buylist 获取购买联系方式(停用)
     * @apiGroup trad
     * @apiName  buylist
     * @apiVersion 1.0.0
     * @apiParam {int} start_time
     * @apiParam {int} end_time
     * @apiParam {int} phone
     * @apiParam {int} status -1取消,1成功，2失败
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/trad/buylist
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function buyList(){
        $param = $this->param;
        return $this->trading->BuyWater($param);
    }

    /**
     * @api {get} /backend/trad/refund-log 退款记录(停用)
     * @apiGroup trad
     * @apiName  refund-log
     * @apiVersion 1.0.0
     * @apiParam {int} start_time
     * @apiParam {int} end_time
     * @apiParam {int} phone
     * @apiParam {int} status -1取消,1成功，2失败
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/trad/refund-log
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function refundLog()
    {
        $param = $this->param;
        return $this->trading->refundLog($param);
    }

    /**
     * @api {get} /backend/trad/contact-list 解锁记录
     * @apiGroup trad
     * @apiName  contact-list
     * @apiVersion 1.0.0
     * @apiParam {int} start_time
     * @apiParam {int} end_time
     * @apiParam {int} phone
     * @apiParam {int} to_phone
     * @apiParam {int} role
     * @apiParam {int} status -1取消,1成功，2失败
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/trad/contact-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data":
     *      "total": 1,
     *      "list": [
     *               {
     *                "id": 1,
     *                "uid": 39,
     *                "to_uid": 3,
     *                "chance": 1,
     *                "rest_chance": 9,
     *                "create_at": "2018-04-28 14:00:00",
     *                "phone": "15700082829",
     *                "to_phone": "13918027224"
     *               }
     *      ]
     *  "code": 102
     *  }
     */
    public function searchContactList(){
        $param = $this->param;
        return $this->trading->searchContactList($param);
    }

}