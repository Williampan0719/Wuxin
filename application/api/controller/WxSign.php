<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/16
 * Time: 上午11:41
 * @introduce
 */
namespace app\api\controller;

use app\api\logic\UserLogic;
use app\api\logic\WxPushLogic;
use extend\helper\Files;
use think\Request;

class WxSign extends BaseApi
{
    protected $user;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->user = new UserLogic();
    }

    /**
     * @api {get} /api/WxSign/verify-wechat 验证token
     * @apiGroup WxSign
     * @apiName  verify-wechat
     * @apiVersion 1.0.0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/WxSign/verify-wechat
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *      },
     *  "code": 104
     *  }
     */
    public function wechat()
    {
        ob_clean();
        if (isset($_GET['echostr'], $_GET['signature'], $_GET['timestamp'], $_GET['nonce'])) {
            $signature = $_GET['signature'] ?? '';
            $timestamp = $_GET['timestamp'] ?? '';
            $nonce = $_GET['nonce'] ?? '';
            $tmpArr = [config('wechat.gzh_token'), $timestamp, $nonce];
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            Files::CreateLog('gzh2.txt',$tmpStr);
            if ($tmpStr == $signature) {
                ob_clean();
                echo $_GET['echostr'];
                Files::CreateLog('success.txt',time());
            }
            die;
        }else{
            $wxPushLogic=new WxPushLogic();
            $wxPushLogic->handleMsg();
        }
    }

    /**
     * @api {get} /api/WxSign/openid-mobile 手机号登录
     * @apiGroup WxSign
     * @apiName  openid-mobile
     * @apiVersion 1.0.0
     * @apiParam {string} phone 用户手机
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/WxSign/openid-mobile
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *      },
     *  "code": 104
     *  }
     */
    public function getOpenidByMobile()
    {
        $params = $this->request->param();
        return $this->user->getOpenidByMobile($params);
    }

    /**
     * @api {post} /api/WxSign/upload-qrcode 上传二维码
     * @apiGroup WxSign
     * @apiName  upload-qrcode
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiParam {file} file 文件
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/WxSign/upload-qrcode
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *      },
     *  "code": 104
     *  }
     */
    public function uploadQrcode()
    {
        $params = $this->request->param();
        Files::CreateLog('gzh001.txt',$params);
        return $this->user->gzhuploadQrcode($params);
    }
}