<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/6/1
 * Time: 上午9:50
 * @introduce
 */
namespace app\backend\controller;

use app\backend\logic\TeamLogic;
use think\Request;

class Team extends BaseAdmin
{
    protected $team = null;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->team = new TeamLogic();
    }

    /**
     * @api {post} /backend/team/add 添加
     * @apiGroup team
     * @apiName  add
     * @apiVersion 1.0.0
     * @apiParam {string} uuid 警员id
     * @apiParam {float} work 上班
     * @apiParam {float} overtime 加班
     * @apiParam {float} late_early 迟到早退
     * @apiParam {float} honor 光荣榜
     * @apiParam {float} ask_leave 上班请假
     * @apiParam {float} absent 旷工
     * @apiParam {float} zbqj 值班请假
     * @apiParam {float} zlh 早列会
     * @apiParam {float} qshyxd 全所会议行动
     * @apiParam {float} sbzqt 上班做其他
     * @apiParam {float} jrfj 警容风纪
     * @apiParam {float} txbc 通讯不畅
     * @apiParam {float} nwws 内务卫生
     * @apiParam {float} dmsb 灯门设备没关
     * @apiParam {float} qzts 群众投诉
     * @apiParam {float} dmqz 打骂群众
     * @apiParam {float} ndzy 虐待在押人员
     * @apiParam {float} fxqxy 服刑区吸烟
     * @apiParam {float} aqjc 安全检查
     * @apiParam {float} shys 损坏遗失
     * @apiParam {float} kjsth 跨监室谈话
     * @apiParam {float} dctb 督查通报
     * @apiParam {float} yl 演练
     * @apiParam {float} xxcl 学习材料
     * @apiParam {float} wx 外宣
     * @apiParam {float} xxlr 信息录入
     * @apiParam {float} else 其他
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/team/add
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "添加成功",
     *      "data": {
     *      },
     *  "code": 101
     *  }
     */
    public function addTeam()
    {
        $param = $this->request->param();
        return $this->team->addTeam($param);
    }

    /**
     * @api {get} /backend/team/list 搜索
     * @apiGroup team
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
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/team/list
     * @apiSuccessExample {json} Response 200 Example
     * {
     *  "status": 1,
     *  "message": "获取成功",
     *      "data": {
     *          "list": [
     *                  {
     *                   uuid 警员id
     *                   work 上班
     *                   overtime 加班
     *                   late_early 迟到早退
     *                   honor 光荣榜
     *                   ask_leave 上班请假
     *                   absent 旷工
     *                   zbqj 值班请假
     *                   zlh 早列会
     *                   qshyxd 全所会议行动
     *                   sbzqt 上班做其他
     *                   jrfj 警容风纪
     *                   txbc 通讯不畅
     *                   nwws 内务卫生
     *                   dmsb 灯门设备没关
     *                   qzts 群众投诉
     *                   dmqz 打骂群众
     *                   ndzy 虐待在押人员
     *                   fxqxy 服刑区吸烟
     *                   aqjc 安全检查
     *                   shys 损坏遗失
     *                   kjsth 跨监室谈话
     *                   dctb 督查通报
     *                   yl 演练
     *                   xxcl 学习材料
     *                   wx 外宣
     *                   xxlr 信息录入
     *                   else 其他
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
        return $this->team->searchTeam($param);
    }

    /**
     * @api {post} /backend/team/del 删除时段
     * @apiGroup team
     * @apiName  del
     * @apiVersion 1.0.0
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiSuccess {int} status 调用状态 1-调用成功 0-调用失败
     * @apiSuccess {int} code   仅供参考
     * @apiSuccess {string} message 提示消息
     * @apiSuccess {Object} data 数据部分,忽略
     * @apiSampleRequest http://apitest.jkxxkj.com/backend/team/del
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
        return $this->team->deleteList($param);
    }
}