<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Model.php';

class Common_model extends Base_Model
{
    public $userInfo = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function check_token($token, $isAdmin = false) 
    {

        // 检查token是否存在并且是否有效
        $this->userInfo = $this->db->where('access_token', $token)
                    ->from(TABLE_ADMIN)
                    ->get()
                    ->row_array();
        //查得到数据并且token在效期内
        $result = !empty($this->userInfo) && $this->userInfo['token_expires_in'] >= time();

        if($result) {
            return success();
        }
        return fail();
    }

    public function save_sys_log($message, $ip) 
    {

        $data = array(
            'time' => time(),
            'content' => $message,
            'ip' => $ip
        );
        
        $this->db->insert(TABLE_SYS_LOG, $data);
    }
}