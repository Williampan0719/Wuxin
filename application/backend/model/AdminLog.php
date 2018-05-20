<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/4/17
 * Time: 下午2:21
 * @introduce
 */
namespace app\backend\model;

use app\common\model\BaseModel;
use traits\model\SoftDelete;

class AdminLog extends BaseModel
{
    use SoftDelete;
    protected $table = 'backend_admin_log';

    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';
    protected $deleteTime = 'delete_at';
}