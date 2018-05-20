<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/9
 * Time: 上午10:58
 */

namespace app\backend\controller;


use app\backend\logic\AuditLogic;
use app\backend\logic\UserLogic;
use think\Request;

class Audit extends BaseAdmin
{
    protected $audit = null;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->audit = new AuditLogic();
    }
    /**
     * @api {get} /backend/audit/parlist 获取家长审核列表(停用)
     * @apiGroup audit
     * @apiName  parlist
     * @apiVersion 1.0.0
     * @apiParam {int} type 类型 0待审核  1不通过  2已通过
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {string} name 姓名
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/audit/parlist
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function parentsAudit(){
        $param = $this->param;
        return $this->audit->parentsAudit($param);

    }
    /**
     * @api {post} /backend/audit/setpar 家长设置状态(停用)
     * @apiGroup audit
     * @apiName  setpar
     * @apiVersion 1.0.0
     * @apiParam {int} uid
     * @apiParam {int} status 1 通过 0 拒绝
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/audit/setpar
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function setStatusPar(){
        $param = $this->param;
        return $this->audit->setStatusPar(intval($param['id']),intval($param['status']));
    }

    /**
     * @api {get} /backend/audit/tutorlist 获取家教审核列表(停用)
     * @apiGroup audit
     * @apiName  tutorlist
     * @apiVersion 1.0.0
     * @apiParam {int} type 类型 0待审核  1不通过  2已通过
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {string} name 姓名
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/audit/tutorlist
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function tutorAudit(){
        $param = $this->param;
        return $this->audit->tutorAudit($param);
    }
    /**
     * @api {post} /backend/audit/settut 设置家教状态(停用)
     * @apiGroup audit
     * @apiName  settut
     * @apiVersion 1.0.0
     * @apiParam {int} uid
     * @apiParam {int} status 1通过  0拒绝
     * @apiParam {int} type  2学历 3微信
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/backend/audit/settut
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function setStatusTut(){
        $param = $this->param;
        return $this->audit->setStatusTut(intval($param['uid']),$param['status'],intval($param['type']));
    }

    /**
     * @api {get} /backend/audit/fail-step 获取哪一步审核失败(停用)
     * @apiGroup audit
     * @apiName  fail-step
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/audit/fail-step
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
    public function getFailStep(){
        $params = $this->param;
        $user = new UserLogic();
        $result = $user->getFailStep($params);
        return $result;
    }

    /**
     * @api {get} /backend/audit/wechat-audit 微信审核列表
     * @apiGroup audit
     * @apiName  wechat-audit
     * @apiVersion 1.0.0
     * @apiParam {int} type 类型 0待审核  1不通过  2已通过
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {string} name 姓名
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/audit/wechat-audit
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
    public function wechatAudit(){
        $params = $this->param;
        $result = $this->audit->wechatAudit($params);
        return $result;
    }

    /**
     * @api {get} /backend/audit/edu-audit 学历审核列表
     * @apiGroup audit
     * @apiName  edu-audit
     * @apiVersion 1.0.0
     * @apiParam {int} type 类型 0待审核  1不通过  2已通过
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {string} name 姓名
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/audit/edu-audit
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
    public function educationAudit(){
        $params = $this->param;
        $result = $this->audit->educationAudit($params);
        return $result;
    }

    /**
     * @api {get} /backend/audit/qrcode-audit 二维码审核列表
     * @apiGroup audit
     * @apiName  qrcode-audit
     * @apiVersion 1.0.0
     * @apiParam {int} type 类型 0待审核  1不通过  2已通过
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {string} name 姓名
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/audit/qrcode-audit
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
    public function qrcodeAudit(){
        $params = $this->param;
        $result = $this->audit->qrcodeAudit($params);
        return $result;
    }

    /**
     * @api {post} /backend/audit/set-audit 审核
     * @apiGroup audit
     * @apiName  set-audit
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} status 1通过  0拒绝
     * @apiParam {int} type  2学历 3微信号 4二维码
     * @apiParam {int} role 角色 1家长 2家教
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/audit/set-audit
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": {
     *
     * }
     *  "code": 112
     *  }
     */
    public function setAudit(){
        $param = $this->param;
        if ($param['role'] == 1) { //家长
            return $this->audit->setStatusPar($param['uid'],$param['status'],$param['type']);
        }
        return $this->audit->setStatusTut($param['uid'],$param['status'],$param['type']); //家教
    }
}