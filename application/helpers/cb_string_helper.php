<?php

/**
 * 判断是否是手机号
 */
function is_phone($phone) {
    if (preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断是否是数字
 */
function is_number($number) {
    if(preg_match("/^\d*$/", $number)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断正整数
 */
function is_p_number($number) {
    if(preg_match("/^\+?[1-9][0-9]*$/", $number)) {
        return true;
    } else {
        return false;
    }
}