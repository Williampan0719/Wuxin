<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/4
 * Time: 上午11:56
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\StatisticLogic;
use think\Request;

class Statistic extends BaseAdmin
{
    protected $statistic = null;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->statistic = new StatisticLogic();
    }

    /**
     * @api {get} /backend/statistic/search-index 统计首页
     * @apiGroup statistic
     * @apiName  search-index
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {string} province 省
     * @apiParam {string} city 市
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/statistic/search-index
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                "regist_today_tutor": 3,
     *                "regist_today_learn": 1,
     *                "regist_today_sum": 4,
     *                "regist_all_tutor": 4,
     *                "regist_all_learn": 2,
     *                "regist_all_sum": 6,
     *                "auth_today_tutor": 1,
     *                "auth_today_learn": 0,
     *                "auth_today_sum": 1,
     *                "auth_all_tutor": 1,
     *                "auth_all_learn": 0,
     *                "auth_all_sum": 1,
     *                "contact_today_tutor": 1,
     *                "contact_today_learn": 0,
     *                "contact_today_sum": 1,
     *                "contact_all_tutor": 1,
     *                "contact_all_learn": 0,
     *                "contact_all_sum": 1,
     *                "share_today_tutor": 0,
     *                "share_today_learn": 1,
     *                "share_today_sum": 1,
     *                "share_all_tutor": 0,
     *                "share_all_learn": 1,
     *                "share_all_sum": 1,
     *                "auth_today_tutor_ratio": "33.3%",
     *                "auth_today_learn_ratio": "0.0%",
     *                "auth_today_sum_ratio": "25.0%",
     *                "auth_all_tutor_ratio": "25.0%",
     *                "auth_all_learn_ratio": "0.0%",
     *                "auth_all_sum_ratio": "16.7%"
     *             }
     *      },
     *  "code": 104
     *  }
     */
    public function searchIndex()
    {
        $params = $this->param;
        $result = $this->statistic->searchIndex($params);
        return $result;
    }

    /**
     * @api {get} /backend/statistic/search-detail 统计详情
     * @apiGroup statistic
     * @apiName  search-detail
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {int} type 类型
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/statistic/search-detail
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *             }
     *      },
     *  "code": 104
     *  }
     */
    public function searchDetail()
    {
        $params = $this->param;
        $result = $this->statistic->searchDetail($params);
        return $result;
    }
}