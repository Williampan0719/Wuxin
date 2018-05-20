<?php
/**
 * Created by PhpStorm.
 * User: dongmingcui
 * Date: 2017/11/9
 * Time: 下午2:42
 */

namespace app\common\logic;

use app\common\traits\Api;
use think\Controller;

abstract class BaseLogic extends Controller
{
    use Api;
    public function logAdminAdd($uid,$content){
        
    }
    public function NotEmpty(array $array,int $int){
        if(!empty($array)){
            foreach ($array as $k=>$v){

            }
        }
    }
}