<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/3
 * Time: 下午1:24
 */

namespace app\api\controller;


use app\api\logic\FormIdLogic;
use think\Request;

class Formid extends BaseApi
{
    private $formIdSer = null;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->formIdSer = new FormIdLogic();
    }
    /**
     * @api {post} /api/wmodel/aforid 用户formid存储
     * @apiGroup wmodel
     * @apiName  aforid
     * @apiVersion 1.0.0
     * @apiParam {string} form_id  form_id
     * @apiSampleRequest https://tutortest.pgyxwd.com/api/wmodel/aforid
     * @apiSuccessExample {json} Response 200 Example
     * {
     * }
     */
    public function addFormId(){
        $param = $this->request->param();
        $res = $this->formIdSer->addFormId($param['uid'],$param['form_id']);
        return $this->ajaxSuccess(104);
    }
}