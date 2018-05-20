<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/26
 * Time: 上午9:28
 * @introduce
 */
namespace app\api\controller;

use app\api\logic\LearnLogic;
use app\api\logic\TutorLogic;
use think\Request;

class Learn extends BaseApi
{
    protected $learnValidate;
    protected $learn;
    protected $tutor;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->learnValidate = new \app\api\validate\Learn();
        $this->learn = new LearnLogic();
        $this->tutor = new TutorLogic();
    }

    /**
     * @api {get} /api/learn/my 1我的主页(家长)
     * @apiGroup learn
     * @apiName  my
     * @apiVersion 1.0.0
     * @apiParam {string} openid 用户openid
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/my
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                     "portrait": "www.baidu.com", //头像
     *                     "id": 1, // 用户id
     *                     "favorite_count": 0, //收藏数
     *                     "contacts_count": 0 // 联系人数
     *                     "body_name": null, //名称
     *                     "sex": 2, //性别
     *                     "certime": 1522134800, //是否认证
     *                     "is_order": 1, //是否预约
     *                   }
     *      },
     *  "code": 104
     *  }
     */
    public function myPage()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->learnValidate, 'myPage', $params);
        $result = $this->learn->getMyPage($params['openid']);
        return $result;
    }

    /**
     * @api {get} /api/learn/my-needs 2我的需求
     * @apiGroup learn
     * @apiName  my-needs
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/my-needs
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list":
     *              "learn_range": {
     *                      "0":全小学，
     *                      }
     *              "learn_subject": { //科目
     *                      "0": "语文",
     *                      "1": "数学",
     *                      },
     *               "tags": { // 特点
     *                      "0": "严格",
     *                      "1": "有针对性",
     *                      },
     *               "user": { //原选中内容
     *                      "id": 2,
     *                      "uid": 1, //用户id
     *                      "learn_range": [1], // 补习范围
     *                      "learn_subject": [2], // 补习科目
     *                      "tags": "开放,能干", //特点
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
        $this->paramsValidate($this->learnValidate, 'myNeeds', $params);
        $result = $this->learn->getMyNeeds($params['uid']);
        return $result;
    }

    /**
     * @api {post} /api/learn/save-needs 3保存我的需求
     * @apiGroup learn
     * @apiName  save-needs
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiParam {string} learn_range 补习范围
     * @apiParam {string} learn_subject 补习科目
     * @apiParam {string} lng 经度 -180～180
     * @apiParam {string} lat 纬度 -90～90
     * @apiParam {string} geo_name 地址内容
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/save-needs
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
        $this->paramsValidate($this->learnValidate, 'saveNeeds', $params);
        $result = $this->learn->saveMyNeeds($params);
        return $result;
    }

    /**
     * @api {get} /api/learn/list 4家教列表(家长用户看家教)
     * @apiGroup learn
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/list
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
     *                 //"tags": "严格", //特点
     *                 //"remark": "", //备注
     *                 "lng": "120.1212112", //经度
     *                 "lat": "30.3232323", //纬度
     *                 "geo_hash": "wtmkt2jteh4ywfdcv", // 地址
     *                 "portrait": "www.baidu.com", // 头像
     *                 "head_name": "潘", //姓
     *                 "distance": 6796 // 距离
     *               },
     *          ]
     *  "code": 104
     *  }
     */
    public function tutorList()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->learnValidate, 'list', $params);
        $result = $this->tutor->tutorList($params);
        return $result;
    }

    /**
     * @api {get} /api/learn/detail 5家教详情页(家长用户看家教)
     * @apiGroup learn
     * @apiName  detail
     * @apiVersion 1.0.0
     * @apiParam {int} from_uid 用户id
     * @apiParam {int} uid 家教的用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/detail
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": {
     *                  "portrait": null, //头像
     *                  "head_name": null, //姓
     *                  "body_name": null, //名
     *                  "sex": 0, //性别
     *                  "school": null, //学校
     *                  "range": [ //教学范围
     *                      "全小学",
     *                      "全高中"
     *                  ],
     *                  "subject": [ //科目
     *                      "数学",
     *                      "英语",
     *                      "科学"
     *                  ],
     *                  "distance": 6416 //距离
     *                  }
     *      },
     *  "code": 104
     *  }
     */
    public function learnDetail()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->learnValidate, 'detail', $params);
        $result = $this->tutor->getTutorDetail($params);
        return $result;
    }

    /**
     * @api {get} /api/learn/contacts 6联系人列表(家长用户看家教)
     * @apiGroup learn
     * @apiName  contacts
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/contacts
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
        $this->paramsValidate($this->learnValidate, 'contacts', $params);
        $result = $this->learn->getContactsList($params);
        return $result;
    }

    /**
     * @api {get} /api/learn/del-contacts 7删除联系人列表(通用)
     * @apiGroup learn
     * @apiName  del-contacts
     * @apiVersion 1.0.0
     * @apiParam {int} id 记录id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/del-contacts
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "删除成功",
     *  "data": {},
     *  "code": 103
     *  }
     */
    public function delContacts()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->learnValidate, 'del-contacts', $params);
        $result = $this->learn->delContacts($params);
        return $result;
    }

    /**
     * @api {post} /api/learn/add-favorites 8添加收藏
     * @apiGroup learn
     * @apiName  add-favorites
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} to_uid 收藏的用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/add-favorites
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *  "data": [],
     *  "code": 101
     * }
     */
    public function addFavorites()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->learnValidate, 'add-favorites', $params);
        $result = $this->learn->addFavorites($params);
        return $result;
    }

    /**
     * @api {get} /api/learn/favorites 9收藏列表(家长看家教)
     * @apiGroup learn
     * @apiName  favorites
     * @apiVersion 1.0.0
     * @apiParam {int} from_uid 用户id
     * @apiParam {int} uid 家长的用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/favorites
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
        $this->paramsValidate($this->learnValidate, 'favorites', $params);
        $result = $this->learn->getFavoritesList($params);
        return $result;
    }

    /**
     * @api {get} /api/learn/del-favorites 10删除收藏列表(通用)
     * @apiGroup learn
     * @apiName  del-favorites
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} to_uid 收藏的用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/learn/del-favorites
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "删除成功",
     *  "data": {},
     *  "code": 103
     *  }
     */
    public function delFavorites()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->learnValidate, 'del-contacts', $params);
        $result = $this->learn->delFavorites($params);
        return $result;
    }

}