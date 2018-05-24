<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/12/8
 * Time: 上午10:24
 */

namespace app\backend\model;

use app\common\model\BaseModel;
use think\Db;

class Permission extends BaseModel
{
    protected $table = 'backend_permissions';

    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-17
     *
     * @description 获取所有权限
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function permissionAll()
    {
        $list = $this->field('id,name,display_name,description,pid,level,path')->where('level',1)->order('id asc')->select();
        if($list){
            foreach($list as $k=>$v){
                $second = $this->field('id,name,display_name,description,pid,level,path')->where('pid',$v['id'])->order('id asc')->select();
                if($second){
                    foreach($second as $m=>$n){
                        $second[$m]['children'] = $this->field('id,name,display_name,description,pid,level,path')->where('pid',$n['id'])->order('id asc')->select();
                    }
                }
                $list[$k]['children']=$second;
            }
        }
        return $list;
    }
    /**
     * @Author liyongchuan
     * @DateTime 2018-01-18
     *
     * @description 节点详情
     * @param int $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function permissionDetail(int $id)
    {
        return $this->where('id',$id)->field('id,name,description,pid,level,show,path')->find();
    }

    /**
     * @Author liyongchuan
     * @DateTime 2018-01-19
     *
     * @description 节点的添加
     * @param array $params
     * @return mixed
     */
    public function permissionAdd(array $params)
    {
        $permissModel=new Permission($params);
        $permissModel->allowField(true)->save();
        return $permissModel->id;
    }
    /**
     * @Author liyongchuan
     * @DateTime 2018-01-19
     *
     * @description 节点的修改
     * @param array $params
     * @return false|int
     */
    public function permissionEdit(array $params)
    {
        return $this->allowField(['name','display_name','description','pid','level','path','show','updated_at'])
            ->save($params, ['id' => $params['id']]);
    }


    /**
     * @Author zhanglei
     * @DateTime 2018-05-22
     *
     * @description 菜单列表
     * @param array $where
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function permissionList(array $where)
    {

        $list = Db::table('tut_backend_permissions')->field('id,name,pid,path,display_name')->where($where)->order('id asc')->select();

        return $list;
    }

}