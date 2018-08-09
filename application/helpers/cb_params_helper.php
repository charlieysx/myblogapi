<?php

/**
 * 从请求参数中获取page和pageSize的值（列表才有）
 */
function get_page($params, $maxPageSize = 99, $defaultPageSize = 15) {
    $key = array(
        'page',
        'pageSize'
    );
    $page = elements($key, $params, '');
    if(!is_p_number($page['page'])) {
        $page['page'] = '0';
    }
    if(!is_p_number($page['pageSize']) || intval($page['pageSize']) > $maxPageSize) {
        $page['pageSize'] = $defaultPageSize;
    }
    return $page;
}

function get_param($params, $key, $default = '') 
{
    $param = $default;
    if(isset($params[$key])) {
        $param = $params[$key];
    }
    return $param;
}