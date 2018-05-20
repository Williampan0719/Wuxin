<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/8
 * Time: 上午10:57
 */

namespace app\backend\controller;


use app\backend\controller\BaseAdmin;
use app\backend\logic\ConfigLogic;
use think\Request;

class Config extends BaseAdmin
{
    protected $config = null;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->config = new ConfigLogic();
    }

    /**
     * @api {get} /backend/config/getlist 获取配置列表
     * @apiGroup config
     * @apiName  getlist
     * @apiVersion 1.0.0
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/config/getlist
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function getList(){
        return $this->config->getList();
    }

    /**
     * @api {get} /backend/config/setamount 设置参数
     * @apiGroup config
     * @apiName  setamount
     * @apiVersion 1.0.0
     * @apiParam {int} id   修改传id 反之
     * @apiParam {string} val   投诉项
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/config/setamount
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function setAmount(){
        $param = $this->param;
        return $this->config->setParam($param);
    }

    /**
     * @api {get} /backend/config/setcompl 设置投诉项配置
     * @apiGroup config
     * @apiName  setcompl
     * @apiVersion 1.0.0
     *
     * @apiParam {int} id   修改传id 反之
     * @apiParam {string} val   投诉项
     *
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/config/setcompl
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function setComplaints(){
        $param = $this->param;
        return $this->config->setComplaints($param);
    }

    /**
     * @api {post} /backend/config/del-config 投诉配置删除
     * @apiGroup config
     * @apiName  del-config
     * @apiVersion 1.0.0
     * @apiParam {int} id 投诉项id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/config/del-config
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "删除成功",
     *      "data": {
     *      },
     *  "code": 103
     *  }
     */
    public function delConfig()
    {
        $params = $this->param;
        $result = $this->config->delConfig($params);
        return $result;
    }

    /**
     * @api {get} /backend/config/getcomlt 获取投诉列表
     * @apiGroup config
     * @apiName  getcomlt
     * @apiVersion 1.0.0
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/config/getcomlt
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": [
     *          ]
     *  "code": 104
     *  }
     */
    public function getComList(){
        return $this->config->getComplaintsList();
    }


}