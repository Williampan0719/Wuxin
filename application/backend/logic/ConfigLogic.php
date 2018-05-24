<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/8
 * Time: 上午11:02
 */

namespace app\backend\logic;


use app\backend\controller\Config;
use app\common\logic\BaseLogic;
use think\Db;
use think\Exception;
use think\Request;

class ConfigLogic extends BaseLogic
{
    protected $configmodel = null;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->configmodel = new \app\api\model\Config();

    }

    /**获取设置列表
     * auth smallzz
     */
    public function getList(){
        try{
            $list = $this->configmodel->select();
        }catch (Exception $exception){
            return $this->ajaxError(114);
        }
        return $this->ajaxSuccess(104,$list);
    }
    /**设置配置参数
     * auth smallzz
     */
    public function setParam(array $param){
        if(empty($param['id'])){
            $this->ajaxError(20001);
        }
        try{
            $this->configmodel->setValue(intval($param['id']),$param['val']);
        }catch (Exception $exception){
            return $this->ajaxError(112);
        }
        return $this->ajaxSuccess(102);
    }

    /**设置投诉项配置
     * auth smallzz
     */
    public function setComplaints(array $param){
        $param['type'] = 3;
        try{
            if(empty($param['id'])){
                $this->configmodel->setValues(0,$param['val']);
            }else{
                $res = $this->configmodel->setValues(intval($param['id']),$param['val']);
            }
            #var_dump($res);exit;
        }catch (Exception $exception){
            return $this->ajaxError(112);
        }
        return $this->ajaxSuccess(102);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 删除投诉
     * @param array $param
     * @return array
     */
    public function delConfig(array $param)
    {
        $a = Db::table('tut_config')->delete($param['id']);
        if ($a == 0) {
            return $this->ajaxError(113);
        }
        return $this->ajaxSuccess(103);
    }

    /** 获取投诉列表
     * auth smallzz
     * @return array
     */
    public function getComplaintsList(){
        $config = new \app\api\model\Config();
        $list = $config->where(['type'=>3])->field('id,namecn')->select();
        return $this->ajaxSuccess(104,$list);
    }
}