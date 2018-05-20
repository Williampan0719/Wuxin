<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/11/15
 * Time: 上午10:40
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Test extends Command
{
    protected function configure()
    {
        $this->setName('test')->setDescription('Here is the test ');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("TestCommand:");
    }
}