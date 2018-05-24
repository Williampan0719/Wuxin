<?php
/**
 * Created by PhpStorm.
 * User: panhao
 * Date: 2018/5/23
 * Time: 下午7:36
 * @introduce
 */
namespace app\cron\command;

use app\api\logic\TempSmsLogic;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Cron extends Command
{
    protected function configure()
    {
        $this->setName('cron')->setDescription('Here is the cron ');
    }

    protected function execute(Input $input, Output $output)
    {
        $tempSms = new TempSmsLogic();
        $completeSend = $tempSms->completeSend();
        $output->writeln("CronCommand:$completeSend");
        $assSend = $tempSms->assSend();
        $output->writeln("CronCommand:$assSend");
    }
}