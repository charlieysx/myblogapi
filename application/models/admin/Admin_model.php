<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // public function register($username, $password)
    // {
    //     // 检查用户名是否存在
    //     $isEx = $this->db->where('username', $username)->count_all_results(TABLE_ADMIN);
    //     if ($isEx) {
    //         return fail('该用户名已经注册');
    //     }

    //     $encrypt = cb_encrypt($password);

    //     $time = time();

    //     $data = array(
    //         'username' => $username,
    //         'password' => $encrypt['password'],
    //         'salt' => $encrypt['salt'],
    //         'user_id' => create_id(),
    //         'last_login_time' => $time,
    //         'access_token' => create_id(),
    //         'token_expires_in' => $time + WEEK,
    //         'create_time'=> $time
    //     );

    //     $this->db->insert(TABLE_ADMIN, $data);

    //     $adminInfo = $this->db->where('username', $username)
    //                         ->from(TABLE_ADMIN)
    //                         ->get()
    //                         ->row_array();

    //     $userResult = array(
    //         'userId' => $adminInfo['user_id'],
    //         'userName' => $adminInfo['username'],
    //         'lastLoginTime' => $adminInfo['last_login_time'],
    //         'token' => array(
    //             'accessToken' => $adminInfo['access_token'],
    //             'tokenExpiresIn' => $adminInfo['token_expires_in'],
    //             'exp' => WEEK
    //         )
    //     );

    //     return success($userResult);
    // }

    public function login($username, $password)
    {
        // 检查用户名是否存在
        $isEx = $this->db->where('username', $username)->count_all_results(TABLE_ADMIN);
        if ($isEx == 0) {
            return fail('该账号不存在');
        }

        $adminInfo = $this->db->where('username', $username)
                            ->from(TABLE_ADMIN)
                            ->get()
                            ->row_array();

        switch($adminInfo['status']) {
            case '0':
                break;
            default:
                return fail('账号异常');
        }

        if (!cb_passwordEqual($adminInfo['password'], $adminInfo['salt'], $password)) {
            return fail('密码错误');
        }

        $time = time();

        $data = array(
            'last_login_time' => $time,
            'access_token' => create_id(),
            'token_expires_in' => $time + WEEK
        );

        // 更新数据
        $this->db->where('username', $username)->update(TABLE_ADMIN, $data);

        $adminInfo = $this->db->where('username', $username)
                            ->from(TABLE_ADMIN)
                            ->get()
                            ->row_array();

        $userResult = array(
            'userId' => $adminInfo['user_id'],
            'userName' => $adminInfo['username'],
            'lastLoginTime' => $adminInfo['last_login_time'],
            'token' => array(
                'accessToken' => $adminInfo['access_token'],
                'tokenExpiresIn' => $adminInfo['token_expires_in'],
                'exp' => WEEK
            )
        );
        
        return success($userResult);
    }
}
