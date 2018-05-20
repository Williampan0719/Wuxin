<?php
#   系统公共方法

/** 简单验证手机号
 * auth smallzz
 * @param string $phone
 * @return bool
 */
function CheckPhone(string $phone){

    if(strlen($phone) == 11 && is_numeric($phone)){
        return true;
    }
    return false;
}
