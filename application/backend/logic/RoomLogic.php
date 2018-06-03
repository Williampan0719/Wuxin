<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/3/30
 * Time: 上午9:50
 * @introduce
 */
namespace app\backend\logic;

use app\backend\model\Room;
use app\common\logic\BaseLogic;

class RoomLogic extends BaseLogic
{
    protected $room;

    public function __construct()
    {
        parent::__construct();
        $this->room = new Room();
    }

    /**
     * @Author panhao
     * @DateTime 2018-05-28
     *
     * @description 新增房间
     * @param array $param
     * @return array
     */
    public function addRoom(array $param)
    {
        $a = $this->room->addOne($param);
        if ($a == 1) {
            return $this->ajaxSuccess(101);
        }
        return $this->ajaxError(111);
    }

    /**
     * @Author panhao
     * @DateTime 2018-06-03
     *
     * @description 房间列表
     * @param array $param
     * @return array|int
     */
    public function roomList(array $param)
    {
        $list = $this->room->getInfoByIds([],'id,room_id');
        if (!empty($param['expor'])) {
            foreach ($list as $key => $value) {
                $new[$key]['room_id'] = $value['room_id'];
            }
            $expor = new ExcelLogic();
            return $expor->export(date('YmdHis'),$new,['房间号']);
        }
        return $this->ajaxSuccess(104,['list'=>$list]);
    }
}