<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/2
 * Time: 上午10:11
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\AssistantLogic;
use think\Request;

class Assistant extends BaseAdmin
{

    protected  $assistant;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->assistant = new AssistantLogic();
    }

    /**
     * @api {get} /backend/assistant/add 添加助教
     * @apiGroup assistant
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} name 名称
     * @apiParam {string} province 省
     * @apiParam {string} city 市
     * @apiParam {int} is_country 是否全国 0否 1是
     * @apiParam {string} qrcode 助教二维码短路径
     * @apiParam {string} assistant 助教头像短路径
     * @apiParam {int} status 1启用 0禁用
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/assistant/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *  "data": []
     *  "code": 101
     *  }
     */
    public function addOne()
    {
        $params = $this->param;
        $result = $this->assistant->addOne($params);
        return $result;
    }

    /**
     * @api {get} /backend/assistant/edit 编辑助教
     * @apiGroup assistant
     * @apiName  edit
     * @apiVersion 1.0.0
     * @apiParam {string} name 名称
     * @apiParam {string} province 省
     * @apiParam {string} city 市
     * @apiParam {int} is_country 是否全国 0否 1是
     * @apiParam {string} qrcode 助教二维码短路径
     * @apiParam {string} assistant 助教头像短路径
     * @apiParam {int} status 1启用 0禁用
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/assistant/edit
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": []
     *  "code": 102
     *  }
     */
    public function editOne()
    {
        $params = $this->param;
        $result = $this->assistant->editOne($params);
        return $result;
    }

    /**
     * @api {get} /backend/assistant/del 删除助教
     * @apiGroup assistant
     * @apiName  del
     * @apiVersion 1.0.0
     * @apiParam {int} id 表id
     * @apiParam {int} status 1启用 0禁用
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/assistant/del
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "删除成功",
     *  "data": []
     *  "code": 103
     *  }
     */
    public function delOne()
    {
        $params = $this->param;
        $result = $this->assistant->delOne($params['id']);
        return $result;
    }

    /**
     * @api {get} /backend/assistant/search 搜索助教
     * @apiGroup assistant
     * @apiName  search
     * @apiVersion 1.0.0
     * @apiParam {string} name 名称
     * @apiParam {string} city 城市
     * @apiParam {int} status 1启用 0禁用
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/assistant/search
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *          "list": [
     *              {
     *              "name": "test1",
     *              "city": "宁波",
     *              "qrcode": "test/1.jpg",
     *              "long_qrcode":'',
     *              "status": 1,
     *              "create_at": "2018-05-02 10:49:08"
     *              }
     *          ],
     *          "total": 1
     *          },
     *  "code": 104
     *  }
     */
    public function searchList()
    {
        $params = $this->param;
        $result = $this->assistant->searchList($params);
        return $result;
    }

    /**
     * @api {get} /backend/assistant/city 城市列表
     * @apiGroup assistant
     * @apiName  city
     * @apiVersion 1.0.0
     * @apiParam {string} name 名称
     * @apiParam {string} city 城市
     * @apiParam {string} phone 手机
     * @apiParam {string} qrcode 短路径
     * @apiParam {int} status 1启用 0禁用
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/assistant/city
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *          "list": [
     *              {
     *              "name": "test1",
     *              "city": "宁波",
     *              "qrcode": "test/1.jpg",
     *              "long_qrcode":'',
     *              "status": 1,
     *              "create_at": "2018-05-02 10:49:08"
     *              }
     *          ],
     *          "total": 1
     *          },
     *  "code": 104
     *  }
     */
    public function getCityList()
    {
        $city = config('city');
        foreach ($city as $key => $value) {
            $city[$key]['value'] = $value['label'];
            foreach ($value['child'] as $k => $v) {
                $city[$key]['children'][$k]['value'] = $v;
                $city[$key]['children'][$k]['label'] = $v;
            }
            unset($city[$key]['child']);
        }
        return $this->ajaxSuccess(101,['list'=>$city]);
    }
}