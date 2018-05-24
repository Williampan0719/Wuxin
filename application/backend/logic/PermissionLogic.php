<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2018/1/19
 * Time: 13:58
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Permission;
use app\common\logic\BaseLogic;
use think\Exception;

class PermissionLogic extends BaseLogic
{
    protected $permissionModel=null;

    public function __construct()
    {
        $this->permissionModel=new Permission();
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-19
     *
     * @description 节点的树形结构
     * @return array
     */
    public function permissionList()
    {
        try{
            $permissionAll=$this->permissionModel->permissionAll();
            $result=$this->ajaxSuccess(200,['permission'=>$permissionAll],'获取成功');
        }catch (Exception $exception){
            $result=$this->ajaxError(201,[],'系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-19
     *
     * @description 节点的添加
     * @param array $params
     * @return array
     */
    public function permissionAdd(array $params)
    {
        try{
            $params['path'] = '/'.$params['name'];
            $p_info=$this->permissionModel->permissionDetail($params['pid']);
            $params['level']=$p_info['level']+1;
            $id=$this->permissionModel->permissionAdd($params);
            if($id>0){
                $result=$this->ajaxSuccess(200,[],'添加成功');
            }else{
                $result=$this->ajaxError(201,[],'系统异常');
            }
        }catch (Exception $exception){
            $result=$this->ajaxError(201,[],'系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-19
     *
     * @description 节点的详情
     * @param array $params
     * @return array
     */
    public function permissionDetail(array $params)
    {
        try{
            $detail=$this->permissionModel->permissionDetail($params['id']);
            if($detail){
                $result=$this->ajaxSuccess(200,['detail'=>$detail],'获取成功');
            }else{
                $result=$this->ajaxError(201,[],'系统异常');
            }
        }catch (Exception $exception){
            $result=$this->ajaxError(201,[],'系统异常');
        }
        return $result;
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-19
     *
     * @description 节点的修改
     * @param array $params
     * @return array
     */
    public function permissionEdit(array $params)
    {
        try{
            $one = $this->permissionModel->permissionDetail($params['id']);
            if ($one['name'] != $params['name']) {
                $params['path'] = '/'.$params['name'];
            }
            $edit=$this->permissionModel->permissionEdit($params);
            if($edit!=false){
                $result=$this->ajaxSuccess(200,[],'修改成功');
            }else{
                $result=$this->ajaxError(201,[],'系统异常');
            }
        }catch (Exception $exception){
            $result=$this->ajaxError(201,[],'系统异常');
        }
        return $result;
    }
}