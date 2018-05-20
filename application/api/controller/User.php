<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/22
 * Time: 下午2:12
 */

namespace app\api\controller;


use app\api\logic\PositionLogic;
use app\api\logic\UserLogic;
use app\api\validate\UserValidate;
use think\Request;

class User extends BaseApi
{
    private $user = '';
    private $userValidate;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->user = new UserLogic();
        $this->userValidate = new UserValidate();
    }
    /**
     * @api {post} /api/user/ureg 用户授权注册
     * @apiGroup user
     * @apiName  ureg
     * @apiVersion 1.0.0
     * @apiParam {string} code  用户授权code码
     * @apiParam {string} encryptedData  加密数据
     * @apiParam {string} iv 加密算法的初始向量
     * @apiParam {string} resource 来源渠道
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/ureg
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userReg(){
        $param = $this->request->param();
        return $this->user->UserReg($param);
    }
    /**
     * @api {post} /api/user/ucrole 用户选择角色
     * @apiGroup user
     * @apiName  ucrole
     * @apiVersion 1.0.0
     * @apiParam {string} uid  用户id
     * @apiParam {string} role  角色【1家长，2家教】
     * @apiParam {int}    sex 性别 1男2女
     * @apiParam {string} lng 经度 -180~180
     * @apiParam {string} lat 纬度 -90~90
     * @apiParam {string} city 城市
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/ucrole
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userChooseRole(){
        $param = $this->request->param();
        return $this->user->ChooseRole($param);
    }
    /**
     * @api {post} /api/user/uchkpro 验证用户走到哪一步
     * @apiGroup user
     * @apiName  uchkpro
     * @apiVersion 1.0.0
     * @apiParam {string} uid  用户id
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/uchkpro
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userCheckProcess(){
        $param = $this->request->param();
        return $this->user->CheckProcess($param);
    }
    /**
     * @api {post} /api/user/myinfo 个人资料填写
     * @apiGroup user
     * @apiName  myinfo
     * @apiVersion 1.0.0
     * @apiparam {int} uid 用户id
     * @apiParam {string} role  用户角色【家长／家教
     * @apiParam {string} phone  手机号
     * @apiParam {string} wechat  微信号
     * @apiParam {string} lng 经度 -180~180
     * @apiParam {string} lat 纬度 -90~90
     * @apiParam {string} geo_name 住址名称
     * @apiParam {string} city 城市 //家教时必填
     * @apiParam {string} role_pos  角色详细【role为家长的时候传
     * @apiParam {string} code      code
     * @apiParam {string} openid    用户openid
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/myinfo
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userInfo(){
        $param = $this->request->param();
        return $this->user->UserInfo($param);
    }
    /**
     * @api {post} /api/user/mydetail 个人资料
     * @apiGroup user
     * @apiName  mydetail
     * @apiVersion 1.0.0
     * @apiParam {string} uid  用户id
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/mydetail
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userDetail(){
        $param = $this->request->param();
        return $this->user->MyDetail($param);
    }

    /**
     * @api {get} /api/user/my-wechat 微信号
     * @apiGroup user
     * @apiName  my-wechat
     * @apiVersion 1.0.0
     * @apiParam {string} uid  用户id
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/my-wechat
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userWechat()
    {
        $param = $this->request->param();
        $user = new \app\api\model\User();
        $a = $user->finds($param['uid'],'wechat,body_name,portrait');
        return $this->ajaxSuccess(101,['list'=>$a]);
    }
    /**
     * @api {post} /api/user/ugetphone 获取手机号
     * @apiGroup user
     * @apiName  ugetphone
     * @apiVersion 1.0.0
     * @apiParam {string} openid  微信openid
     * @apiParam {string} code  小程序code
     * @apiParam {string} encryptedData  加密数据
     * @apiParam {string} iv  解密密数据
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/ugetphone
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userGetPhone(){
        $param = $this->request->param();
        return $this->user->GetMobile($param);
    }

    /**
     * @api {post} /api/user/ucode 用户发送验证码(停用)
     * @apiGroup user
     * @apiName  ucode
     * @apiVersion 1.0.0
     * @apiParam {string} phone  手机号
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/ucode
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userCode(){
        $param = $this->request->param();
        return $this->user->Vercode($param);
    }

    /**
     * @api {post} /api/user/urlist 用户角色列表
     * @apiGroup user
     * @apiName  urlist
     * @apiVersion 1.0.0
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/urlist
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userRoleList()
    {
        $data = [
            0=>'父亲',
            1=>'母亲',
            2=>'叔叔',
            3=>'阿姨',
            4=>'其他',
        ];
        return $this->ajaxSuccess(104,$data);
    }
    /**
     * @api {post} /api/user/uccode 用户验证码对比
     * @apiGroup user
     * @apiName  uccode
     * @apiVersion 1.0.0
     * @apiParam {string} phone  手机号
     * @apiParam {string} code  验证码
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/uccode
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function userConCode(){
        $param = $this->request->param();
        return $this->user->Contrastcode($param);
    }

    /**
     * @api {post} /api/user/udipc 用户学历提交
     * @apiGroup user
     * @apiName  udipc
     * @apiVersion 1.0.0
     * @apiParam {string} uid  用户id
     * @apiParam {file} file  图片
     * @apiParam {string} professional  专业
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/udipc
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function uDipCer(){
        $param = $this->request->param();
        if (empty($param['professional'])) {
            return $this->ajaxError(210,[],'请填写所属专业');
        }

        return $this->user->Diplomacertification($param);
    }
    /**
     * @api {post} /api/user/unamec 用户个人身份认证(停用)
     * @apiGroup user
     * @apiName  unamec
     * @apiVersion 1.0.0
     * @apiParam {string} openid  用户openid
     * @apiParam {string} idcard  身份证号
     * @apiParam {string} phone   手机号
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/unamec
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function uNameCer(){
        $param = $this->request->param();
        return $this->user->Namecertification($param);
    }
    /**
     * @api {get} /api/user/ugpayr 用户支付结果(停用)
     * @apiGroup user
     * @apiName  ugpayr
     * @apiVersion 1.0.0
     * @apiParam {string} uid  用户id
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/ugpayr
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function uGetPayRes(){
        $param = $this->request->param();
        return $this->user->GetPayResult($param);

    }
    /**
     * @api {post} /api/user/usordercan 设置取消订单(停用)
     * @apiGroup user
     * @apiName  usordercan
     * @apiVersion 1.0.0
     * @apiParam {string} uid  用户id
     * @apiParam {string} order_sn  订单号
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/usordercan
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function uSetOrderCancel(){
        $param = $this->request->param();
        return $this->user->SetCancelStatus($param);
    }
    /**
     * @api {post} /api/user/ubw 购买谁(停用)
     * @apiGroup user
     * @apiName  ubw
     * @apiVersion 1.0.0
     * @apiParam {int} uid  用户id
     * @apiParam {int} buyid  被买者id
     * @apiParam {string} openid  用户openid
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/ubw
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function uBuyWho(){
        $param = $this->request->param();
        return $this->user->BuyWho($param);
    }
    /**
     * @api {post} /api/user/order-status 预约状态设置
     * @apiGroup user
     * @apiName  order-status
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} is_order 预约 0否 1是
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/user/order-status
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {}
     *  "code": 104
     *  }
     */
    public function editOrderStatus()
    {
        $params = $this->request->param();
        $result = $this->user->editOrderStatus($params);
        return $result;
    }

    /**
     * @api {post} /api/user/zhima 芝麻分认证(改为同盾)
     * @apiGroup user
     * @apiName  zhima
     * @apiVersion 1.0.0
     * @apiParam {string} name 用户真实姓名
     * @apiParam {string} idcard 身份证
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/user/zhima
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {}
     *  "code": 104
     *  }
     */
    public function zhimaAuth()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->userValidate, 'zhima', $params);
        $result = $this->user->zhimaAuth($params);
        return $result;
    }

    /**
     * @api {post} /api/user/upload-qrcode 上传微信通用接口
     * @apiGroup user
     * @apiName  upload-qrcode
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {file} file 二维码图片
     * @apiParam {string} wechat 微信号
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/user/upload-qrcode
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {}
     *  "code": 104
     *  }
     */
    public function uploadQrcode()
    {
        $params = $this->request->param();
        $result = $this->user->uploadQrcode($params);
        return $result;
    }

    /**
     * @api {get} /api/user/fail-step 获取哪一步审核失败
     * @apiGroup user
     * @apiName  fail-step
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/user/fail-step
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *          type:1 //1身份2学历3微信
     * }
     *  "code": 104
     *  }
     */
    public function getFailStep()
    {
        $params = $this->request->param();
        $result = $this->user->getFailStep($params);
        return $result;
    }

    /**
     * @api {get} /api/user/position-list 用户地址管理列表
     * @apiGroup user
     * @apiName  position-list
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/position-list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *  "data": {
     *          list: [
     *                  {
     *                  "id": 1,  //地址id
     *                  "uid": 2, //用户id
     *                  "name": "测试地址1", //联系人
     *                  "geo_name": "测试地址1", //地址名称
     *                  "geo_detail": "",
     *                  "lng": "120.1683220", //经度
     *                  "lat": "30.2776100", //纬度
     *                  "geo_hash": "wtmknrzguge6f",
     *                  "status": 0, //是否默认
     *                  "create_at": "2018-03-28 09:30:00", //创建时间
     *                  "update_at": null,
     *                  "delete_at": null,
     *                  "phone": "15700082839"
     *                  "city": "宁波"
     *                  },
     *                ]
     * }
     *  "code": 104
     *  }
     */
    public function getPositionList()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->userValidate, 'position-list', $params);
        $position = new PositionLogic();
        $result = $position->getPositionList($params);
        return $result;
    }

    /**
     * @api {get} /api/user/add-position 添加地址
     * @apiGroup user
     * @apiName  list
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiParam {string} geo_name 地址名称
     * @apiParam {string} lng 经度
     * @apiParam {string} lat 纬度
     * @apiParam {string} city 城市
     * @apiParam {int} status 是否启用 默认0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/tutor/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *  "data": [
     *          ]
     *  "code": 101
     *  }
     */
    public function addPosition()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->userValidate, 'add-position', $params);
        $position = new PositionLogic();
        $result = $position->addPosition($params);
        return $result;
    }

    /**
     * @api {post} /api/user/edit-position 编辑地址
     * @apiGroup user
     * @apiName  edit-position
     * @apiVersion 1.0.0
     * @apiParam {string} uid 用户id
     * @apiParam {int} id 地址id
     * @apiParam {string} geo_name 地址名称
     * @apiParam {string} lng 经度
     * @apiParam {string} lat 纬度
     * @apiParam {string} city 城市
     * @apiParam {int} status 是否启用 默认0
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/edit-position
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function editPosition()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->userValidate, 'edit-position', $params);
        $position = new PositionLogic();
        $result = $position->editPosition($params);
        return $result;
    }

    /**
     * @api {post} /api/user/del-position 删除地址
     * @apiGroup user
     * @apiName  del-position
     * @apiVersion 1.0.0
     * @apiParam {int} id 地址id
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/del-position
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "删除成功",
     *  "data": [
     *          ]
     *  "code": 103
     *  }
     */
    public function delPosition()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->userValidate, 'del-position', $params);
        $position = new PositionLogic();
        $result = $position->delPosition($params);
        return $result;
    }

    /**
     * @api {post} /api/user/position-status 设为默认
     * @apiGroup user
     * @apiName  position-status
     * @apiVersion 1.0.0
     * @apiParam {int} id 地址id
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/position-status
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function editPositionStatus()
    {
        $params = $this->request->param();
        $position = new PositionLogic();
        $result = $position->editPositionStatus($params);
        return $result;
    }
    /**
     * @api {post} /api/user/uphead 修改头像
     * @apiGroup user
     * @apiName  uphead
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {file} File 文件
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/uphead
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function upHead(){
        $params = $this->request->param();
        return $this->user->upHead($params);
    }
    /**
     * @api {post} /api/user/upwechat 微信修改(停用)
     * @apiGroup user
     * @apiName  upwechat
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} wechat
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/upwechat
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function upWechat(){
        $params = $this->request->param();
        return $this->user->upWechat($params);
    }

    /**
     * @api {post} /api/user/save-complain 保存投诉
     * @apiGroup user
     * @apiName  save-complain
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} to_uid 投诉用户id
     * @apiParam {int} type 投诉类型
     * @apiParam {string} remark 备注
     * @apiParam {file} file 图片文件(非必填)
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/save-complain
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "新增成功",
     *  "data": [
     *          ]
     *  "code": 101
     *  }
     */
    public function saveComplain()
    {
        $params = $this->request->param();
        $this->paramsValidate($this->userValidate, 'save-complain', $params);
        return $this->user->saveComplain($params);
    }

    /**
     * @api {get} /api/user/getcomlt 获取投诉列表
     * @apiGroup user
     * @apiName  getcomlt
     * @apiVersion 1.0.0
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/getcomlt
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
        return $this->user->getComplaintsList();
    }

    /**
     * @api {get} /api/user/get-share 分享
     * @apiGroup user
     * @apiName  get-share
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/get-share
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "编辑成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function getShare()
    {
        $params = $this->request->param();
        return $this->user->getShare($params);
    }

    /**
     * @api {post} /api/user/add-contact 解锁
     * @apiGroup user
     * @apiName  add-contact
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} to_uid 被解锁人id
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/add-contact
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "解锁成功",
     *  "data": [
     *          ]
     *  "code": 102
     *  }
     */
    public function addContact()
    {
        $params = $this->request->param();
        return $this->user->addContact($params);
    }

    /**
     * @api {get} /api/user/after-contact 解锁成功后信息页
     * @apiGroup user
     * @apiName  after-contact
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiParam {int} to_uid 被解锁人id
     * @apiParam {int} role 使用者角色
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/user/after-contact
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "解锁成功",
     *  "data": [
     *          ]
     *  "code": 104
     *  }
     */
    public function afterContact()
    {
        $params = $this->request->param();
        return $this->user->afterContact($params);
    }

    /**
     * @api {get} /api/user/user-auth 获取用户认证状态
     * @apiGroup user
     * @apiName  user-auth
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/user/user-auth
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *      },
     *  "code": 104
     *  }
     */
    public function getUserAuth()
    {
        $params = $this->request->param();
        $result = $this->user->getUserAuth($params);
        return $result;
    }

    /**
     * @api {get} /api/user/ass-detail 助教资料
     * @apiGroup user
     * @apiName  ass-detail
     * @apiVersion 1.0.0
     * @apiParam {int} uid 用户id
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/api/user/ass-detail
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *      },
     *  "code": 104
     *  }
     */
    public function assistantDetail()
    {
        $params = $this->request->param();
        $result = $this->user->assistantDetail($params);
        return $result;
    }
}