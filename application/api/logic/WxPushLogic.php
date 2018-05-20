<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/16
 * Time: 上午11:42
 * @introduce
 */
namespace app\api\logic;

use app\common\logic\BaseLogic;
use extend\helper\Utils;
use extend\service\WechatService;

class WxPushLogic extends BaseLogic
{
    /**
     * @Author liyongchuan
     * @DateTime 2017-12-25
     *
     * @description 微信客服回复
     */
    public function handleMsg()
    {
        $wx = new WechatService();

        $postStr = file_get_contents("php://input") or die('access deny');

        $message = Utils::parseMsgData($postStr);
        if ($message) {
            $fromUser = $message['FromUserName'];
            $text = '欢迎关注家教汪';
            $wx->sendTextMsg2($fromUser, $text);
        }
    }
}