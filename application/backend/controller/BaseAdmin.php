<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/29
 * Time: 下午5:19
 * @introduce
 */
namespace app\backend\controller;


use app\backend\model\Admin;
use app\common\controller\BaseController;
use app\common\traits\Api;
use think\Cache;
use think\Request;

class BaseAdmin extends BaseController
{
    use Api;
    protected $request;
    protected $param;
    public static $admin_id = '';
    public static $isSuper = 0;

    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->request = $request;
        $this->allowWebClient();
        $this->param = $this->request->param() ?? [];
        $header = $this->request->header();
        $this->param['token'] = $header['token'] ?? '';
        $token = $this->param['token'];
        $this->_validateToken($token ?? '');
    }



    private function _validateToken(string $token)
    {
        if ($this->request->baseUrl() == '/backend/admin/login' || $this->request->baseUrl() == '/index.php/backend/admin/login') {
            return true;
        }
        if(empty($token)) {
            die(json_encode($this->ajaxError(201, [],'token不能为空')));

        }
        $adminModel = new Admin();
        $adminId = Cache::get("$token");
        $count = $adminModel->where(['admin_id'=>$adminId])->count();

        if ($count == 0) {
            header('Content-Type:application/json; charset=utf-8');
            die(json_encode($this->ajaxError(20004, [],'登录失效,请重新登录')));
        }else{
            $detail = $adminModel->where(['admin_id'=>$adminId])->field('admin_id,is_super')->find();
            self::$admin_id = $detail['admin_id'];
            self::$isSuper = $detail['is_super'];
        }
    }
}