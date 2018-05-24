<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/12/8
 * Time: 上午10:28
 */

namespace app\backend\model;

use app\common\model\BaseModel;
use think\Db;

class AdminLog extends BaseModel
{

    protected $table = 'backend_admin_log';

    protected $createTime = 'create_at';
    protected $deleteTime = 'delete_at';



    /**
     * @Author zhanglei
     * @DateTime  2018-02-11
     *
     * @description 添加数据
     * @param $param
     * @return mixed
     */
    public function addLog($param)
    {
        $model = new AdminLog($param);
        $model->allowField(true)->save();
        return $model->id;
    }


    /**
     * @Author zhanglei
     * @DateTime 2018-02-11
     *
     * @description 日志数量
     * @param array $where
     * @return int|string
     */
    public function logCount(array $where)
    {

        $count = AdminLog::where($where)->count();
        return $count;
    }



    /**
     * @Author zhanglei
     * @DateTime 2018-02-11
     *
     * @description 日志列表
     * @param array $params
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function logList(array $params)
    {
        $field = $params['field'] ?? '*';
        $page = $params['page'] ?? '';
        $size = $params['size'] ?? '';
        $where = $params['where'] ?? '';

        return Db::table($this->table)->field($field)->where($where)->page($page, $size)->select();

    }

}