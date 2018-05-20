<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/29
 * Time: 下午5:19
 * @introduce
 */
namespace app\backend\controller;


use app\api\logic\UserLogic;
use app\backend\model\AdminLog;
use app\common\controller\BaseController;
use app\common\traits\Api;
use think\Controller;
use think\Request;

class BaseAdmin extends BaseController
{
    use Api;
    protected $request;
    protected $param;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->allowWebClient();
        $this->param = $this->request->param();
        $header = $this->request->header();
        $hash = $header['hash'];
        $this->param['token'] = $header['token'] ?? '';
        $this->_validateHash($hash ?? '');
        $this->_validateToken($header['token'] ?? '');
    }

    /**
     * @Author panhao
     * @DateTime 2018-02-05
     *
     * @description 验证版本号
     * @param string $hash
     * @return bool
     */
    private function _validateHash(string $hash)
    {
        $system = new UserLogic();
        $data['hash'] = $hash;
        $result = $system->deVersionInfo($data);
        if (!in_array($result['version_hash']['client_version'],config('hash')) || empty($result['version_hash'])) {
            header('Content-Type:application/json; charset=utf-8');
            die(json_encode($this->ajaxError(1007, [],'请输入正确的版本加密秘钥')));
        }
    }

    private function _validateToken(string $token)
    {
        if ($this->request->baseUrl() == '/backend/admin/login' || $this->request->baseUrl() == '/index.php/backend/admin/login') {
            return true;
        }
        $log = new AdminLog();
        $count = $log->where(['token'=>$token])->count();
        if ($count == 0) {
            header('Content-Type:application/json; charset=utf-8');
            die(json_encode($this->ajaxError(20004, [],'登录失效,请重新登录')));
        }
    }
}