<?php
function success_result($msg = "success", $data = array()){
    if($data == null) {
        $data = array();
    }
    return array(
        'status' => true,
        'code' => SUCCESS,
        'msg' => $msg,
        'data' => $data
    );
}

function fail_result($msg = "fail", $data = array(), $code = -1){
    if($data == null) {
        $data = array();
    }
    return array(
        'status' => false,
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    );
}

function success($msg = 'success') {
    return array(
        'success'=> true,
        'msg'=> $msg
    );
}

function fail($msg = 'fail', $code = -1) {
    return array(
        'success'=> false,
        'msg'=> $msg,
        'code'=> $code
    );
}
