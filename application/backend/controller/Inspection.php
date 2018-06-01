<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/6/1
 * Time: 上午8:50
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\InspectionLogic;
use think\Request;

class Inspection extends BaseAdmin
{
    protected $inspection = null;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->inspection = new InspectionLogic();
    }

    /**
     * @api {post} /backend/inspection/add 添加
     * @apiGroup inspection
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} uuid 警员id
     * @apiParam {float} wgkf 违规扣分
     * @apiParam {float} blwg 八类违规
     * @apiParam {float} jqfx 警情分析
     * @apiParam {float} yjsb 硬件设备
     * @apiParam {float} dmjb 点名交班
     * @apiParam {float} hbdj 患病登记
     * @apiParam {float} jjjl 进监记录
     * @apiParam {float} xyjl 寻医记录
     * @apiParam {float} xsjc 巡视检查
     * @apiParam {float} xcff 巡查放风
     * @apiParam {float} zbtg 值班脱岗
     * @apiParam {float} ksqk 考试情况
     * @apiParam {float} fxyh 发现隐患
     * @apiParam {float} dtjl 动态记录
     * @apiParam {float} zbxx 值班休息
     * @apiParam {float} dbjd 带班监督
     * @apiParam {float} else 其他
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/inspection/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function addInspection()
    {
        $param = $this->request->param();
        //$this->paramsValidate($this->attVali, 'add', $param);
        return $this->inspection->addInspection($param);
    }

    /**
     * @api {get} /backend/inspection/list 搜索
     * @apiGroup inspection
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
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/inspection/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": [
     *                  {
     *                    "uuid": "nb043",
     *                    "wgkf": "2.0", 违规扣分
     *                    "blwg": "0.5", 八类违规
     *                    "jqfx": "0.0", 警情分析
     *                    "yjsb": "0.0", 硬件设备
     *                    "dmjb": "0.0", 点名交班
     *                    "hbdj": "0.0", 患病登记
     *                    "jjjl": "0.0", 进监记录
     *                    "xyjl": "0.0", 寻医记录
     *                    "xsjc": "0.0", 巡视检查
     *                    "xcff": "0.0", 巡查放风
     *                    "zbtg": "0.0", 值班脱岗
     *                    "ksqk": "0.0", 考试情况
     *                    "fxyh": "0.0", 发现隐患
     *                    "dtjl": "0.0", 动态记录
     *                    "zbxx": "0.0", 值班休息
     *                    "dbjd": "0.0", 带班监督
     *                    "else": "0.0"  其他
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
        return $this->inspection->searchInspection($param);
    }

    /**
     * @api {post} /backend/inspection/del 删除时段
     * @apiGroup inspection
     * @apiName  del
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/inspection/del
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
        return $this->inspection->deleteList($param);
    }
}