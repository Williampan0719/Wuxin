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

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->attendance = new AttendanceLogic();
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
        //uuid必填
        return $this->attendance->addAttendance($param);
    }

    public function searchSum()
    {
        $param = $this->request->param();
        return $this->attendance->searchAttendance($param);
    }
}