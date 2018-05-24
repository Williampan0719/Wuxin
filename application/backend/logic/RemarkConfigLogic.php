<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/18
 * Time: 下午3:37
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\RemarkConfig;
use app\common\logic\BaseLogic;
use think\Request;

class RemarkConfigLogic extends BaseLogic
{
    protected $remark = null;

    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->remark = new RemarkConfig();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 配置列表
     * @param array $param
     * @return array
     */
    public function remarkList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = '';
        if (isset($param['type']) && is_numeric($param['type'])) {
            $where = 'type = '.$param['type'];
        }
        $list = $this->remark->remarkList($where,'*',$page,$size);
        $total = $this->remark->remarkTotal($where);
        return $this->ajaxSuccess(104,['total'=>$total,'list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-21
     *
     * @description 用户标签列表
     * @param array $param
     * @return array
     */
    public function userRemark(array $param)
    {
        $where = 'type = '.$param['role'].' or type = 0';
        $list = $this->remark->userRemark($where);
        return $this->ajaxSuccess(104,['list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 添加配置
     * @param array $param
     * @return array
     */
    public function addRemark(array $param)
    {
        $a = $this->remark->addRemark($param);
        if ($a == 1) {
            return $this->ajaxSuccess(101);
        }
        return $this->ajaxError(111);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 修改配置
     * @param array $param
     * @return array
     */
    public function editRemark(array $param)
    {
        $a = $this->remark->editRemark($param,$param['id']);
        if ($a == 1) {
            return $this->ajaxSuccess(102);
        }
        return $this->ajaxError(112);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 删除配置
     * @param int $id
     * @return array
     */
    public function delRemark(int $id)
    {
        $a = $this->remark->delRemark($id);
        if ($a == 1) {
            return $this->ajaxSuccess(103);
        }
        return $this->ajaxError(113);
    }
}