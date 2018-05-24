<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/2
 * Time: 上午10:13
 * @introduce
 */
namespace app\backend\logic;
use app\backend\model\Admin;
use app\backend\model\Assistant;
use app\backend\model\RoleAdmin;
use app\common\logic\BaseLogic;
use extend\helper\PinYin;
use extend\helper\Utils;

class AssistantLogic extends BaseLogic
{
    protected $assistant;
    public function __construct()
    {
        parent::__construct();
        $this->assistant = new Assistant();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 添加
     * @param array $param
     * @return array
     */
    public function addOne(array $param)
    {
        $pin = new PinYin();
        $chinese = $pin->iconvStr('utf-8', 'gbk', $param['name']);
        $aid = $pin->getFirstPY($chinese); //获取简称
        $param['aid'] = $aid.str_pad(rand(0,99),3,0,STR_PAD_LEFT);

        //添加账号
        $admin = new Admin();
        $password = Utils::genPassword('NhEtQ0ty');
        $params['admin_name'] = $param['aid'];
        $params['admin_password'] = $password['password'];
        $params['admin_mobile'] = $param['phone'];
        $params['salt'] = $password['encrypt'];
        $admin_id = $admin->adminAdd($params);

        //绑定权限
        $adminRole = new RoleAdmin();
        $adminRole->save(['admin_id'=>$admin_id,'role_id'=>16]);

        $param['admin_id'] = $admin_id;
        $a = $this->assistant->addOne($param);
        if ($a == 1) {
            return $this->ajaxSuccess(101);
        }
        return $this->ajaxError(111);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 编辑
     * @param array $param
     * @return array
     */
    public function editOne(array $param)
    {
//        $a = explode(',',$param['city']);
//        $param['city'] = end($a);
        $a = $this->assistant->editOne($param,$param['id']);
        if ($a == 1) {
            return $this->ajaxSuccess(102);
        }
        return $this->ajaxError(112);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 删除
     * @param int $id
     * @return array
     */
    public function delOne(int $id)
    {
        $a = $this->assistant->delOne($id);
        if ($a == 1) {
            return $this->ajaxSuccess(103);
        }
        return $this->ajaxError(113);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-02
     *
     * @description 搜索列表
     * @param array $param
     * @return array
     */
    public function searchList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $where = ' status >= 0';
        if (isset($param['status']) && is_numeric($param['status'])) {
            $where = 'status = '.$param['status'];
        }
        if (!empty($param['name'])) {
            $where .= ' and (name like "%'.$param['name'].'%" or aid like "%'.$param['name'].'%")';
        }
        if (!empty($param['city'])) {
            $province = Utils::getProvinceByCity($param['city'],0);
            if (!empty($province['province'])) {
                $where .= ' and (city like "%' . $param['city'] . '%" or province like "%' . $province['province'] . '%" or is_country = 1 or province like "%' . $param['city'] . '%") ';
            }else{
                $where .= ' and (city like "%' . $param['city'] . '%" or is_country = 1 or province like "%' . $param['city'] . '%") ';
            }
        }
        $list = $this->assistant->searchList($where,'id,name,city,province,qrcode,status,create_at,aid,phone,assistant,is_country',$page,$size);
        $count = 0;
        if (!empty($list)) {
            $count = $this->assistant->searchCount($where);
            foreach ($list as $k => $v) {
                $list[$k]['long_qrcode'] = !empty($v['qrcode']) ? config('oss.outer_host').$v['qrcode'] : '';
                $list[$k]['long_assistant'] = !empty($v['assistant']) ? config('oss.outer_host').$v['assistant'] : '';
                $list[$k]['citys'] = !empty($v['province']) ? (!empty($v['city']) ? $v['province'].','.$v['city'] : $v['province']) : $v['city'];
                if ($v['is_country'] == 1) {
                    $list[$k]['citys'] = '全国';
                }
//                $temp = explode(',',$v['city']);
//                foreach ($city as $i => $j) {
//                    if ($j['label'] == current($temp)) { //取省id
//                        foreach ($j['child'] as $a => $b) {
//                            if ($b == end($temp)) {
//                                $list[$k]['city_id'] = [$i,$a];
//                                break;
//                            }
//                        }
//                    }
//                }
            }
        }
        return $this->ajaxSuccess(104,['list'=>$list,'total'=>$count]);
    }
}