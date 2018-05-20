<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/3/22
 * Time: 下午2:01
 */

namespace app\api\controller;


use app\api\logic\UserLogic;
use app\common\controller\BaseController;
use app\common\traits\Api;

use think\Request;

class BaseApi extends BaseController
{
    use Api;
    protected $request;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->allowWebClient();
        $hash = $this->request->header('hash');
        $this->_validateHash($hash ?? '');
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
        if(($this->request->baseUrl() == '/api/WxSign/verify-wechat') || ($this->request->baseUrl() == 'index.php/api/WxSign/verify-wechat')) {
            return true;
        }
        $system = new UserLogic();
        $data['hash'] = $hash;
        $result = $system->deVersionInfo($data);
        if (!in_array($result['version_hash']['client_version'],config('hash')) || empty($result['version_hash'])) {
            header('Content-Type:application/json; charset=utf-8');
            die(json_encode($this->ajaxError(1007, [],'请输入正确的版本加密秘钥')));
        }
    }
}