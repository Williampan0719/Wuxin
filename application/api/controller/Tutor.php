<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/22
 * Time: 下午3:07
 * @introduce 家教用户
 */
namespace app\api\controller;

use app\api\logic\LearnLogic;
use app\api\logic\TutorLogic;
use extend\service\Geohash;
use think\Request;

class Tutor extends BaseApi
{
    protected $tutorValidate;
    protected $tutor;
    protected $learn;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->tutorValidate = new \app\api\validate\Tutor();
        $this->tutor = new TutorLogic();
        $this->learn = new LearnLogic();
    }

    public function test()
    {
        $n_latitude  = '29.8599422';
        $n_longitude = '121.6224557';
        $geo = new Geohash();
        $geo_hash = $geo->encode($n_latitude,$n_longitude);
        return $geo_hash;
    }

    /**
     * @api {get} /api/tutor/my 1我的主页(家教)
     * @apiGroup tutor
     * @apiName  my
     * @apiVersion 1.0.0
     * @apiParam {string} openid 用户openid
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/tutor/my
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *               "list": {
     *                     "portrait": "www.baidu.com", //头像
     *                     "id": 1, // 用户id
     *                     "favorite_count": 0, //收藏数
     *                     "contacts_count": 0 // 联系人数
     *                     "body_name": null, //名称
     *                     "sex": 2, //性别
     *                     "school": null, //大学
     *                     "professional": null, //专业
     *                     "is_order": 1, //是否预约
     *                     "certime": 1522134800,
     *                   }
     *      },
     *  "code": 104
     *  }
     */
    public function myPage()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->tutorValidate, 'myPage', $params);
        $result = $this->tutor->getMyPage($params);
        return $result;
    }

    /**
     * @api {get} /api/tutor/my-needs 2我的教学
     * @apiGroup tutor
     * @apiName  my-needs
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/tutor/my-needs
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list":
     *              "teach_subject": { //科目
     *                      "1": "语文",
     *                      "2": "数学",
     *                      },
     *               "tags": { // 特点
     *                      "1": "严格",
     *                      "2": "有针对性",
     *                      },
     *               "user": { //原选中内容
     *                      "id": 2,
     *                      "uid": 1, //用户id
     *                      "teach_range": [2], // 补习范围
     *                      "teach_subject": [1,2], // 补习科目
     *                      "tags": "幽默", //特点
     *                      "remark": "", // 其他要求
     *                      "lng": "120.1212112", //经度
     *                      "lat": "30.3232323", //纬度
     *                      "geo_hash": "wtmkt2jteh4ywfdcv", // 地址算法
     *                      "geo_name": "" // 地址名称
     *                      }
     *      },
     *  "code": 104
     *  }
     */
    public function myNeeds()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->tutorValidate, 'myNeeds', $params);
        $result = $this->tutor->getMyNeeds($params['uid']);
        return $result;
    }

    /**
     * @api {post} /api/tutor/save-needs 3保存我的教学
     * @apiGroup tutor
     * @apiName  save-needs
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiParam {string} teach_range 教学范围
     * @apiParam {string} teach_subject 教学科目
     * @apiParam {string} lng 经度 -180～180
     * @apiParam {string} lat 纬度 -90～90
     * @apiParam {string} geo_name 地址名称
     * @apiParam {string} city 城市
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/tutor/save-needs
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": []
     *  "code": 102
     *  }
     */
    public function saveNeeds()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->tutorValidate, 'saveNeeds', $params);
        $result = $this->tutor->saveMyNeeds($params);
        return $result;
    }

    /**
     * @api {get} /api/tutor/list 4家长列表(家教用户看家长)
     * @apiGroup tutor
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/tutor/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": [
     *              {
     *                 "id": 2,
     *                 "uid": 1, //用户id
     *                 "learn_range": "全小学", //补习范围
     *                 "learn_subject": "全初中", //补习科目
     *                 "tags": "严格", //特点
     *                 "remark": "", //备注
     *                 "lng": "120.1212112", //经度
     *                 "lat": "30.3232323", //纬度
     *                 "geo_hash": "wtmkt2jteh4ywfdcv", // 地址
     *                 "portrait": "www.baidu.com", // 头像
     *                 "head_name": "潘先生", //称呼
     *                 "sex": 1 性别
     *                 "distance": 6km // 距离
     *               },
     *          ]
     *  "code": 104
     *  }
     */
    public function learnList()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->tutorValidate, 'list', $params);
        $result = $this->learn->learnList($params);
        return $result;
    }

    /**
     * @api {get} /api/tutor/detail 5家长详情页(家教用户看家长)
     * @apiGroup tutor
     * @apiName  detail
     * @apiVersion 1.0.0
     * @apiParam {int} from_uid 用户id
     * @apiParam {int} uid 家长的用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/tutor/detail
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                     "portrait": "www.baidu.com",
     *                     "head_name": "潘",
     *                     "range": "数学",
     *                     "subject": "全小学",
     *                     "tags": [
     *                          "严格",
     *                          "生动"
     *                      ],
     *                     "remark": "",
     *                     "distance": 5825
     *                   }
     *      },
     *  "code": 104
     *  }
     */
    public function learnDetail()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->tutorValidate, 'detail', $params);
        $result = $this->learn->getLearnDetail($params);
        return $result;
    }

    /**
     * @api {get} /api/tutor/contacts 6联系人列表(家教看家长)
     * @apiGroup tutor
     * @apiName  contacts
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/tutor/contacts
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": [
     *                   {
     *                     "id": 1,
     *                     "uid": 1, //用户id
     *                     "to_uid": 2, //联系人用户id
     *                     "money": "10.00", //价格
     *                     "create_at": "2018-03-27 00:00:00", // 时间
     *                     "portrait": null, //头像
     *                     "head_name": null // 姓
     *                     "phone": "15700082849", //联系方式
     *                     "buy": 1 //购买1 被购买0
     *                   },
     *                  ]
     *      },
     *  "code": 104
     *  }
     */
    public function myContacts()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->tutorValidate, 'contacts', $params);
        $result = $this->learn->getContactsList($params);
        return $result;
    }

    /**
     * @api {get} /api/tutor/favorites 7收藏列表(家教看家长)
     * @apiGroup tutor
     * @apiName  favorites
     * @apiVersion 1.0.0
     * @apiParam {int} uid 家长的用户id
     * @apiParam {int} page 当前页
     * @apiParam {int} size 每页数
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/tutor/favorites
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                     "portrait": "www.baidu.com",
     *                     "head_name": "潘",
     *                     "body_name": "浩",
     *                     "range": "数学",
     *                     "subject": "全小学",
     *                     "tags": [
     *                          "严格",
     *                          "生动"
     *                      ],
     *                     "distance": 5825
     *                   }
     *      },
     *  "code": 104
     *  }
     */
    public function myFavorites()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->tutorValidate, 'favorites', $params);
        $result = $this->learn->getFavoritesList($params);
        return $result;
    }
}