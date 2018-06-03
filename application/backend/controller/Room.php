<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/29
 * Time: 下午5:18
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\RoomLogic;
use think\Request;

class Room extends BaseAdmin
{
    protected $room;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->room = new RoomLogic();
    }

    /**
     * @api {post} /backend/room/add 添加房间
     * @apiGroup room
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {int} room_id 房间号
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/room/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function addRoom()
    {
        $params = $this->param;
        $result = $this->room->addRoom($params);
        return $result;
    }

    /**
     * @api {get} /backend/room/list 警员列表
     * @apiGroup room
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {int} expor 1下载 0读取
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/room/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function roomList()
    {
        $result = $this->room->roomList();
        return $result;
    }
}