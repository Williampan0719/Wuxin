<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/30
 * Time: 下午8:23
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\AttendanceLogic;
use think\Request;

class Attendance extends BaseAdmin
{
    protected $attendance = null;
    protected $attVali = null;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->attendance = new AttendanceLogic();
        $this->attVali = new \app\backend\validate\Attendance();
    }

    /**
     * @api {post} /backend/att/add 添加
     * @apiGroup att
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} uuid 警员id
     * @apiParam {float} ask_leave 请假次数
     * @apiParam {float} absent 旷工
     * @apiParam {float} cdzt 迟到早退次数
     * @apiParam {float} swjb 上午加班
     * @apiParam {float} zwjb 中午加班
     * @apiParam {float} xwjb 下午加班
     * @apiParam {float} wsjb 晚上加班
     * @apiParam {float} txjb 通宵加班
     * @apiParam {float} zssb 在所上班
     * @apiParam {float} business_trip 出差
     * @apiParam {float} train 培训
     * @apiParam {float} dute 值班
     * @apiParam {float} rest 休息
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/att/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function addAttendance()
    {
        $param = $this->request->param();
        $this->paramsValidate($this->attVali, 'add', $param);
        return $this->attendance->addAttendance($param);
    }

    /**
     * @api {get} /backend/att/list 搜索
     * @apiGroup att
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
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/att/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": [
     *                  {
     *                    "uuid": "nb043",
     *                    "ask_leave": "2.0", 请假次数
     *                    "absent": "0.5", 旷工
     *                    "cdzt": "1.0", 迟到早退次数
     *                    "swjb": "0.0", 上午加班
     *                    "zwjb": "0.0", 中午加班
     *                    "xwjb": "0.0", 下午加班
     *                    "wsjb": "0.0", 晚上加班
     *                    "txjb": "0.0", 通宵加班
     *                    "zssb": "0.0", 在所上班
     *                    "business_trip": "0.0", 出差
     *                    "train": "0.0", 培训
     *                    "dute": "0.0", 值班
     *                    "rest": "0" 休息
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
        return $this->attendance->searchAttendance($param);
    }

    /**
     * @api {post} /backend/att/del 删除时段
     * @apiGroup att
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/att/list
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
        return $this->attendance->deleteList($param);
    }
}