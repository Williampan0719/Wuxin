<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/17
 * Time: 下午3:35
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Resource;
use app\common\logic\BaseLogic;
use extend\service\WechatService;
use think\Request;

class ResourceLogic extends BaseLogic
{
    protected $resource = null;

    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->resource = new Resource();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 渠道列表
     * @param array $param
     * @return array
     */
    public function resourceList(array $param)
    {
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;
        $total = $this->resource->getTotal([]);
        $list = $this->resource->getPageList([],'*',$page,$size);
        return $this->ajaxSuccess(104,['total'=>$total,'list'=>$list]);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-17
     *
     * @description 渠道新增
     * @param array $param
     * @return array
     */
    public function createResource(array $param)
    {
        $data = [
            'name' => $param['name'],
            'role' => $param['role'] ?? 0,
            'direction' => $param['direction'] ?? 0,
            'addtime' => date('Y-m-d H:i:s'),
        ];
        $id = $this->resource->createResource($data);
        if (empty($id)) {
            return $this->ajaxError(111,[],'新增失败');
        }
        $wechat = new WechatService();
        $qrcode = $wechat->getQrCode($id,'pages/index/index',430,0);
        //添加二维码
        $this->resource->editResource(['qrcode'=>$qrcode],$id);

        return $this->ajaxSuccess(101);
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-18
     *
     * @description 编辑来源
     * @param array $param
     * @return array
     */
    public function editResource(array $param)
    {
        $data = [
            'name' => $param['name'],
            'role' => $param['role'] ?? 0,
            'direction' => $param['direction'] ?? 0,
        ];
        $this->resource->editResource($data,$param['id']);
        return $this->ajaxSuccess(102);
    }
}