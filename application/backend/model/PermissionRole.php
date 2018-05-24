<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/12/8
 * Time: 上午10:24
 */

namespace app\backend\model;

use app\common\model\BaseModel;

class PermissionRole extends BaseModel
{
    protected $table = 'backend_permission_role';

    protected $autoWriteTimestamp = false;

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-17
     *
     * @description 获取role的权限
     * @param int $roleId
     * @return array
     */
    public function rolePermissionByRole(int $roleId)
    {
        return  $this->where('role_id',$roleId)->column('permission_id');
    }


    /**
     * @Author liyongchuan
     * @DateTime 2018-01-17
     *
     * @description 获取role的权限
     * @param array $where
     * @return array
     */
    public function rolePermissionList(array $where)
    {
        return  $this->where($where)->column('permission_id');
    }


    /**
     * @Author liyongchuan
     * @DateTime 2018-01-17
     *
     * @description 删除role的权限
     * @param int $roleId
     * @return int
     */
    public function rolePermissionDelete(int $roleId)
    {
        return $this->where('role_id',$roleId)->delete();
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-17
     *
     * @description 添加role权限
     * @param int $roleId
     * @param string $permissionStr
     * @return array|false
     */
    public function rolePermissionAdd(int $roleId , string $permissionStr)
    {
        $permission=explode(',',$permissionStr);
        $data=[];
        foreach ($permission as $key=>$vo){
            $data[$key]['role_id']=$roleId;
            $data[$key]['permission_id']=$vo;
        }
        return $this->saveAll($data);
    }
}