<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/4/17
 * Time: 上午11:43
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Admin;
use app\backend\model\AdminLog;
use app\common\logic\BaseLogic;
use extend\helper\Utils;
use think\Exception;
use think\Request;

class AdminLogic extends BaseLogic
{
    protected $adminModel;

    public function __construct()
    {
        parent::__construct();
        $this->adminModel = new Admin();
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-02-06
     *
     * @description 登录
     * @param array $params
     * @return array
     */
    public function login(array $params)
    {
        try {
            $tokenId = Utils::createToken();
            $password = $params['admin_password'] ?? '';
            $username = trim($params['admin_name']) ?? '';
            $where['admin_name'] = $username;
            $salt = $this->adminModel->getFieldByKey($where, 'salt');
            if (empty($salt)) {
                return $this->ajaxError(201, [], '用户不存在');
            }
            $pwd = Utils::genPassword($password,$salt);

            $where['admin_password'] = $pwd;
            $count = $this->adminModel->isExist($where);
            if ($count == 0) {
                return $this->ajaxError(201, [], '用户名或密码错误');
            }
            $status = $this->adminModel->getFieldByKey($where, 'status');
            if($status==0) {
                return $this->ajaxError(201, [], '账号已被禁用,请联系管理员');
            }
            $adminId = $this->adminModel->getFieldByKey($where, 'admin_id');

            $data['admin_id'] = $adminId;
            $data['token'] = $tokenId;
            $data['last_login'] = date('Y-m-d H:i:s',time());

            $editResult = $this->adminModel->adminEdit($data);
            if ($editResult) {
                $log = new AdminLog();
                $r = Request::instance();
                $log->save(['admin_name'=>$username,'token'=>$tokenId,'create_at'=>date('Y-m-d H:i:s',time()),'ip'=>$r->ip()]);
                $result = $this->ajaxSuccess(20005, ['token'=>$tokenId], '登录成功');
            } else {
                $result = $this->ajaxError(20004, [], '登录失败');
            }
        }catch (Exception $exception){
            $result = $this->ajaxError(20004, [], '系统异常');
        }

        return $result;

    }

    /**
     * @Author zhanglei
     * @DateTime 2017-12-11
     *
     * @description 用户登录验证
     * @param array $params
     * @return int|mixed
     */
    public function auth(array $params)
    {
        $adminId = 0;
        try {

            $token = $params['token'] ?? '';
            $authList = config('auth');
            $request = Request::instance();
            $url = $request->baseUrl();
            $urlList = explode("/", $url);
            $actionName = $urlList[count($urlList) - 1]; //获取当前的路由名

            if (!in_array($actionName, $authList)) {
                if (!isset($token) || empty($token)) {

                    $result = $this->ajaxError(1004);

                } else {
                    $where['token_id'] = $token;
                    $adminId = $this->adminModel->getFieldByKey($where, 'admin_id');
                    $adminId = empty($adminId) || is_null($adminId) ? false : $adminId;

                    if (!$adminId) {
                        $result = $this->ajaxError(1005);
                    }

                }
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(1004);
        }
        if (!empty($result)) {
            echo json_encode($result);
            exit;
        }
        return $adminId;
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 后台账户添加
     * @param array $params
     * @return array
     */
    public function addAdmin(array $params)
    {
        try {
            $password = Utils::genPassword($params['admin_password']);
            $params['admin_password'] = $password['password'];
            $params['salt'] = $password['encrypt'];
            $admin_id = $this->adminModel->adminAdd($params);
            if ($admin_id > 0) {
                $result = $this->ajaxSuccess(101);
            } else {
                $result = $this->ajaxError(111);
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(111);
        }
        return $result;
    }

    public function editAdmin(array $param)
    {
        if ($param['admin_id'] != '2') {
            //todo
        }else{
            //todo
        }
    }
}