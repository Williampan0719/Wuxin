<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/8
 * Time: 下午4:37
 */

namespace app\backend\logic;


use app\api\model\Contacts;
use app\api\model\Order;
use app\api\model\Orderqy;
use app\api\model\Refund;
use app\api\model\User;
use app\common\logic\BaseLogic;
use think\Exception;
use think\Request;

class TradingLogic extends BaseLogic
{
    protected $order = null;
    protected $orderqy = null;
    protected $user = null;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->user = new User();
        $this->order = new Order();
        $this->orderqy = new Orderqy();
    }
    /** 认证流水
     * auth smallzz
     */
    public function AuditWater(array $param){
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = 'o.type = 1';
        if(!empty($param['start_time'])){
            $where .= ' and o.addtime >= "'.$param['start_time'].'"';
        }
        if(!empty($param['end_time'])){
            $where .= ' and o.addtime <= "'.$param['end_time'].'"';
        }
        if(!empty($param['phone'])){
            $where .= ' and u.phone = "'.$param['phone'].'"';
        }
        if(!empty($param['status'])){
            $where .= ' and o.status = "'.$param['status'].'"';
        }
        try{
            $count = $this->order->alias('o')
                ->join('tut_user u','u.id = o.uid','inner')
                ->join('tut_user_cer uc','uc.uid = u.id','inner')
                ->where($where)

                ->count();
            $list = $this->order->alias('o')
                ->join('tut_user u','u.id = o.uid','inner')
                ->join('tut_user_cer uc','uc.uid = u.id','inner')
                ->field('`o`.`id`,`u`.`nickname`,`u`.`phone`,`u`.`body_name`,`o`.`addtime`,`o`.`wx_order_sn`,`o`.`amount`,CASE o.status WHEN 1 THEN "成功" WHEN 2 THEN "失败" WHEN -1 THEN "取消" ELSE "未知" END as status ,CASE uc.status WHEN 1 THEN "认证不通过" WHEN 2 THEN "认证通过" ELSE "认证待审核" END as rz_status')
                ->where($where)
                ->page($page,$size)
                ->order('u.id desc')
                ->select();
            $lists = [
                'total'=>$count,
                'list'=>$list,
            ];
        }catch (Exception $exception){
            return $this->ajaxError(114);
        }
        return $this->ajaxSuccess(104,$lists);
    }

    /** 退款流水
     * auth smallzz
     */
    public function RefundWater(array $param){
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = 'o.type = 1';
        if(!empty($param['start_time'])){
            $where .= ' and o.addtime >= "'.$param['start_time'].'"';
        }
        if(!empty($param['end_time'])){
            $where .= ' and o.addtime <= "'.$param['end_time'].'"';
        }
        if(!empty($param['phone'])){
            $where .= ' and u.phone = "'.$param['phone'].'"';
        }
        if(!empty($param['status'])){
            $where .= ' and o.status = "'.$param['status'].'"';
        }
        try{
            $count = $this->orderqy->alias('o')
                ->join('tut_user u','u.id = o.uid','inner')
                ->join('tut_user_cer uc','uc.uid = u.id','inner')
                ->where($where)
                ->count();
            $list = $this->orderqy->alias('o')
                ->join('tut_user u','u.id = o.uid','inner')
                ->join('tut_user_cer uc','uc.uid = u.id','inner')
                ->field('`o`.`id`,`u`.`nickname`,`u`.`phone`,`u`.`body_name`,`o`.`addtime`,`o`.`wx_order_sn`,`o`.`amount`,CASE o.status WHEN 1 THEN "成功" WHEN 2 THEN "失败" WHEN -1 THEN "取消" ELSE "未知" END as status ,CASE uc.status WHEN 1 THEN "认证不通过" WHEN 2 THEN "认证通过" ELSE "认证待审核" END as rz_status')
                ->where($where)
                ->page($page,$size)
                ->order('u.id desc')
                ->select();
            $lists = [
                'total'=>$count,
                'list'=>$list,
            ];
        }catch (Exception $exception){
            return $this->ajaxError(114);
        }
        return $this->ajaxSuccess(104,$lists);
    }

    /** 购买联系方式流水
     * auth smallzz
     */
    public function BuyWater(array $param){
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = 'o.type = 2';
        if(!empty($param['start_time'])){
            $where .= ' and o.addtime >= "'.$param['start_time'].'"';
        }
        if(!empty($param['end_time'])){
            $where .= ' and o.addtime <= "'.$param['end_time'].'"';
        }
        if(!empty($param['phone'])){
            $where .= ' and u.phone = "'.$param['phone'].'"';
        }
        if(!empty($param['status'])){
            $where .= ' and o.status = "'.$param['status'].'"';
        }
        try{
            $count = $this->order->alias('o')
                ->join('tut_user u','u.id = o.uid','inner')
                ->join('tut_user u2','u2.id = o.buyid','inner')
                ->join('tut_user_cer uc','uc.uid = u.id','inner')
                ->where($where)
                ->count();
            $list = $this->order->alias('o')
                ->join('tut_user u','u.id = o.uid','inner')
                ->join('tut_user u2','u2.id = o.buyid','inner')
                ->join('tut_user_cer uc','uc.uid = u.id','inner')
                ->field('`o`.`id`,`u`.`role`,`u`.`wechat`,`u`.`phone`,`u`.`body_name`,`o`.`wx_order_sn`,`o`.`amount`,u2.body_name as be_body_name,u2.phone as be_phone,o.status as status')
                ->where($where)
                ->page($page,$size)
                ->order('u.id desc')
                ->select();
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    $list[$k]['status'] = $v['status'] == 1 ?($v['is_refund'] > 0 ?'已退款':'成功'):($v['status'] == 2 ?'失败':'取消');
                }
            }
            $lists = [
                'total'=>$count,
                'list'=>$list,
            ];
        }catch (Exception $exception){
            return $this->ajaxError(114);
        }
        return $this->ajaxSuccess(104,$lists);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-11
     *
     * @description 流水记录
     * @param array $param
     * @return array
     */
    public function refundLog(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = [];
        if (!empty($param['start_time']) && !empty($param['end_time'])) {
            $where['r.create_at'] = ['between', [$param['start_time'],$param['end_time']]];
        }elseif (!empty($param['start_time'])) {
            $where['r.create_at'] = ['gt',$param['start_time']];
        }elseif (!empty($param['end_time'])) {
            $where['r.create_at'] = ['lt',$param['end_time']];
        }
        if (!empty($param['phone'])) {
            $where['u.phone'] = $param['phone'];
        }
        $field = 'r.uid,r.order_sn,r.money,r.editor,r.create_at,u.nickname,u.wechat,u.phone,u.body_name';

        $refund = new Refund();
        $list = $refund->getRefundLog($where,$field,$page,$size);
        $total = $refund->getTotal($where);
        return $this->ajaxSuccess(104,['list'=>$list,'total'=>$total]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-28
     *
     * @description 搜索解锁列表
     * @param array $param
     * @return array
     */
    public function searchContactList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = [];
        if (!empty($param['phone'])) {
            $where['uid'] = $this->user->searchValue(['phone'=>$param['phone']],'id');
        }
        if (!empty($param['to_phone'])) {
            $where['to_uid'] = $this->user->searchValue(['phone'=>$param['to_phone']],'id');
        }
        if (!empty($param['start_time']) && !empty($param['end_time'])) {
            $where['c.create_at'] = ['between', [$param['start_time'],$param['end_time']]];
        }elseif (!empty($param['start_time'])) {
            $where['c.create_at'] = ['gt',$param['start_time']];
        }elseif (!empty($param['end_time'])) {
            $where['c.create_at'] = ['lt',$param['end_time']];
        }
        if (!empty($param['role'])) {
            $where['u.role'] = $param['role'];
        }

        $contact = new Contacts();
        $field = 'c.id,c.uid,c.to_uid,c.chance,c.rest_chance,c.create_at,u.phone,u.role';
        $count = $contact->searchContactCount($where);
        $list = $contact->searchContactList($where,$field,$page,$size);
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $list[$key]['to_phone'] = $this->user->getValue($value['to_uid'],'phone');
            }
        }
        return $this->ajaxSuccess(104,['total'=>$count,'list'=>$list]);
    }

}