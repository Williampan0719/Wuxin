<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2018/1/2
 * Time: 14:56
 * @introduce
 */

namespace app\backend\logic;

use app\backend\model\Permission;
use app\backend\model\PermissionRole;
use app\backend\model\Role;
use app\common\logic\BaseLogic;
use think\Exception;

class RoleLogic extends BaseLogic
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new Role();
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-02
     *
     * @description 角色的添加
     * @param array $params
     * @return array
     */
    public function roleAdd(array $params)
    {
        try {
            $id = $this->roleModel->roleAdd($params);
            if ($id > 0) {
                $result = $this->ajaxSuccess(200, [], '操作成功');
            } else {
                $result = $this->ajaxError(201, [], '添加失败');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-02
     *
     * @description 角色的详情
     * @param array $params
     * @return array
     */
    public function roleDetail(array $params)
    {
        try {
            $role = $this->roleModel->roleDetail($params['id']);
            if ($role != false) {
                if ($role != null) {
                    $result = $this->ajaxSuccess(200, ['datail' => $role], '操作成功');
                } else {
                    $result = $this->ajaxSuccess(200, ['datail' => []], '操作成功');
                }
            } else {
                $result = $this->ajaxError(201, [], '添加失败');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-02
     *
     * @description 角色的修改
     * @param array $params
     * @return array
     */
    public function roleEdit(array $params)
    {
        try {
            $bool = $this->roleModel->roleEdit($params);
            if ($bool != false) {
                $result = $this->ajaxSuccess(200, [], '操作成功');
            } else {
                $result = $this->ajaxError(201, [], '操作失败');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-02
     *
     * @description 角色的删除
     * @param array $params
     * @return array
     */
    public function roleDelete(array $params)
    {
        try {
            $del = $this->roleModel->roleDelete($params['id']);
            if ($del > 0) {
                $result = $this->ajaxSuccess(200, [], '操作成功');
            } else {
                $result = $this->ajaxError(201, [], '系统异常');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-02
     *
     * @description 角色的列表
     * @param array $params
     * @return array
     */
    public function roleList(array $params)
    {
        try {
            $page = $params['page'] ?? config('paginate.default_page');
            $size = $params['size'] ?? config('paginate.default_size');
            $keyword = $params['keyword'] ?? '';
            $total = $this->roleModel->roleCount($keyword);
            if ($total > 0) {
                $roleList = $this->roleModel->roleList($page, $size, $keyword);
                if ($roleList != false) {
                    $result = $this->ajaxSuccess(200, ['list' => $roleList, 'total' => $total],'获取成功');
                } else {
                    $result = $this->ajaxSuccess(200, ['list' => [], 'total' => $total],'获取成功');
                }
            } else {
                $result = $this->ajaxSuccess(200, ['list' => [], 'total' => $total],'获取成功');
            }

        } catch (Exception $exception) {
            echo $exception;exit;
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-02
     *
     * @description 获取所有角色
     * @return array
     */
    public function roleAll()
    {
        try {
            $roleAll = $this->roleModel->roleAll();
            if ($roleAll != false) {
                $result = $this->ajaxSuccess(200, ['list' => $roleAll],'获取成功');
            } else {
                $result = $this->ajaxSuccess(200, ['list' => []],'获取成功');
            }
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-17
     *
     * @description 角色权限详情
     * @param array $params
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function rolePermissionHtml(array $params)
    {
        try {
            //$permissionModel = new Permission();
            $permissionRoleModel = new PermissionRole();
           // $permissionAll = $permissionModel->permissionAll();
            $permissRole = $permissionRoleModel->rolePermissionByRole($params['id']);
            $result = $this->ajaxSuccess(200, ['list' => $permissRole],'获取成功');
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        };
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime
     *
     * @description 权限的所有
     * @return array
     */
    public function permissionAll()
    {
        try {
            $permissionModel = new Permission();
            $permissionAll = $permissionModel->permissionAll();
            $result = $this->ajaxSuccess(200, ['list' => $permissionAll],'获取成功');
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        };
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-17
     *
     * @description 角色权限修改
     * @param array $params
     * @return array
     */
    public function rolePermissionAdd(array $params)
    {
        try {
            $permissionRoleModel = new PermissionRole();
            $del = $permissionRoleModel->rolePermissionDelete($params['id']);
            $add = $permissionRoleModel->rolePermissionAdd($params['id'], $params['permission_id'],'获取成功');
            $result = $this->ajaxSuccess(200,[],'操作成功');
        } catch (Exception $exception) {
            $result = $this->ajaxError(201, [], '系统异常');
        }
        return $result;
    }
}