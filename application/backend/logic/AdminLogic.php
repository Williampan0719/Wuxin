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
use app\backend\model\Permission;
use app\backend\model\PermissionRole;
use app\backend\model\Role;
use app\backend\model\RoleAdmin;
use app\common\logic\BaseLogic;
use extend\helper\Utils;
use think\Cache;
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
            $isSuper = $this->adminModel->getFieldByKey($where, 'is_super');
            $adminId = $this->adminModel->getFieldByKey($where, 'admin_id');

            $data['admin_id'] = $adminId;
            $data['token_id'] = $tokenId;
            $data['last_login'] = date('Y-m-d H:i:s',time());

            $editResult = $this->adminModel->adminEdit($data);
            Cache::set("$tokenId",$adminId,3600);
            if ($editResult) {
                $log = new AdminLog();
                $r = Request::instance();

                $log->addLog(['admin_id'=>$adminId,'admin_name'=>$username,'create_at'=>date('Y-m-d H:i:s',time()),'ip'=>$r->ip()]);
                if ($isSuper == 1) {
                    $menu = $this->menuList($adminId,1);
                    $result = $this->ajaxSuccess(20005, ['token'=>$tokenId,'menu'=>$menu,'admin_id'=>$adminId], '登录成功');
                }else{
                    $menu = $this->menuList($adminId,0);
                    $result = $this->ajaxSuccess(20005, ['token'=>$tokenId,'menu'=>$menu,'admin_id'=>$adminId], '登录成功');
                }
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
     * @Author zhanglei
     * @DateTime 2018-05-21
     *
     * @description 后台用户添加
     * @param array $params
     * @return array
     */
    public function adminAdd(array $params)
    {
        try {
            $password = Utils::genPassword($params['admin_password']);
            $params['admin_password'] = $password['password'];
            $params['salt'] = $password['encrypt'];
            if (!empty($param['city'])) {
                $params['city'] = $param['city'];
            }
            $admin_id = $this->adminModel->adminAdd($params);
            if ($admin_id > 0) {
                $result = $this->ajaxSuccess(200, [], '添加成功');
            } else {
                $result = $this->ajaxError(201, [], '添加失败');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-05-21
     *
     * @description 后台用户详情
     * @param array $params
     * @return array
     */
    public function adminDetail(array $params)
    {
        try {
            $admin = $this->adminModel->adminDetail($params['admin_id']);
            if ($admin != false) {
                if ($admin != null) {
                    $result = $this->ajaxSuccess(200, ['datail' => $admin],'获取成功');
                } else {
                    $result = $this->ajaxSuccess(200, [],'获取成功');
                }
            } else {
                $result = $this->ajaxError(201, [], '获取失败');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-05-21
     *
     * @description 后台用户的修改
     * @param array $params
     * @return array
     */
    public function adminEdit(array $params)
    {
        try {

            $bool = $this->adminModel->adminEdit($params);
            if ($bool != false) {
                $result = $this->ajaxSuccess(200,[],'操作成功');
            } else {
                $result = $this->ajaxError(201, [], '操作失败');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-05-21
     *
     * @description 后台用户的列表
     * @param array $params
     * @return array
     */
    public function adminList(array $params)
    {
        try {
            $page = $params['page'] ?? config('paginate.default_page');
            $size = $params['size'] ?? config('paginate.default_size');
            $keyword = $params['keyword'] ?? '';
            $total = $this->adminModel->adminCount($keyword);
            if ($total > 0) {
                $roleAdminModel=new RoleAdmin();
                $adminList = $this->adminModel->adminList($page, $size, $keyword);
                foreach ($adminList as $key=>$vo){
                    $adminRole=$roleAdminModel->roleAdminDetail($vo['admin_id']);
                    $adminList[$key]['adminRole']=$adminRole;
                }
                if ($adminList != false) {
                    $result = $this->ajaxSuccess(200, ['list' => $adminList, 'total' => $total]);
                } else {
                    $result = $this->ajaxSuccess(200, ['list' => [], 'total' => $total]);
                }
            } else {
                $result = $this->ajaxSuccess(200, ['list' => [], 'total' => $total]);
            }

        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '获取失败');
        }
        return $result;
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-05-21
     *
     * @description 后台用户的删除
     * @param array $params
     * @return array
     */
    public function adminDelete(array $params)
    {
        try {
            $del = $this->adminModel->adminDelete($params['admin_id']);
            if ($del > 0) {
                $result = $this->ajaxSuccess(203);
            } else {
                $result = $this->ajaxError(204);
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(204);
        }
        return $result;
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-05-21
     *
     * @description 后台用户修改密码
     * @param array $params
     * @return array
     */
    public function adminUpdPwd(array $params)
    {
        try {

            $admin = $this->adminModel->adminDetail($params['admin_id']);
            if ($admin) {
                $password = Utils::genPassword($params['new_password']);
                $params['admin_password'] = $password['password'];
                $params['salt'] = $password['encrypt'];
                $bool = $this->adminModel->adminEdit($params);
                if ($bool != false) {
                    $result=$this->ajaxSuccess(200,[],'操作成功');
                }else{
                    $result = $this->ajaxError(201, [], '修改失败');
                }
            } else {
                $result = $this->ajaxError(201, [], '密码错误');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author zhanglei
     * @DateTime 2018-05-21
     *
     * @description admin账号角色的添加
     * @param array $params
     * @return array
     */
    public function  adminRoleAdd(array $params)
    {
        try{
            $roleAdminModel=new RoleAdmin();
            $roleAdminModel->roleAdminDelete($params['admin_id']);
            $roleAdmin=$roleAdminModel->roleAdminAdd($params['role_id'],$params['admin_id']);
            if(count($roleAdmin)>0){
                $result=$this->ajaxSuccess(200,[],'操作成功');
            }else{
                $result = $this->ajaxError(201, [], '修改失败');
            }
        }catch (Exception $exception){
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }
    /**
     * @Author liyongchuan
     * @DateTime 2018-01-17
     *
     * @description 查询账号的所有角色和权限
     * @param int $adminId
     * @return array
     */
    public function adminPermissions(int $adminId)
    {
        try{
            $roleAdminModel=new RoleAdmin();
            $permissionRoleModel=new PermissionRole();
            $roleModel=new Role();
            $permissionModel=new Permission();
            $roleAll=$roleAdminModel->roleAdminDetail($adminId);//获取角色id
            if($roleAll){
                $roleInfo=[];
                foreach ($roleAll as $key=>$vo){
                    $role=$roleModel->roleDetail($vo);//获取角色信息
                    $rolePerm=$permissionRoleModel->rolePermissionByRole($vo);//获取角色权限id
                    $permissions=[];
                    if($rolePerm){
                        foreach ($rolePerm as $k=>$v){
                            $a=$permissionModel->permissionDetail($v);
                            $permissions[$k]=$a;
                        }

                    }
                    $role['permissions']=$permissions;
                    $roleInfo[$key]=$role;
                }
                $result=$roleInfo;
            }else{
                $result=null;
            }
        }catch (Exception $exception){
            $result=false;
        }
        return $result;
    }




    /**
     * @Author zhanglei
     * @DateTime 2018-02-07
     *
     * @description 用户退出
     * @param string $token
     * @return array
     */
    public function logout(string $token)
    {

        try{

            Cache::rm($token);
            $result = $this->ajaxSuccess(200, [], '退出成功');

        }catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }

        return $result;
    }


    /**
     * @Author zhanglei
     * @DateTime 2018-05-22
     *
     * @description 菜单列表
     * @param int $adminId
     * @param int $isuper
     * @return array
     */
    public function menuList(int $adminId,int $isuper)
    {

        try{

            $where = [];
            if($isuper==0) {
                $roleAdminModel = new RoleAdmin();
                $roleIds = $roleAdminModel->roleAdminDetail($adminId);
                $where['role_id'] = ['in',$roleIds];
                $PsonRoleModel = new PermissionRole();
                $ids = $PsonRoleModel->rolePermissionList($where);
                unset($where);
                $where['id'] = ['in',$ids];
            }
            $PsonModel = new Permission();
            $lists = $PsonModel->permissionList($where);
            $menuList = Utils::treeList($lists);
            return $menuList;
            //$result = $this->ajaxSuccess(200, ['menu_list'=>$menuList], '获取成功');
        }catch (Exception $exception) {
            return $this->ajaxError(201, [], '系统异常');
        }
    }


}