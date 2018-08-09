<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Admin extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('admin/Admin_model', 'admin');
    }

    /**
     * 创建管理员账号
     */
    // public function create() 
    // {
    //     $params = $this->input->post();

    //     $data = array(
    //         'username' => '',
    //         'password' => ''
    //     );
    //     foreach($data as $k => $v) {
    //         $data[$k] = get_param($params, $k);
    //     }
    //     if ($data['username'] == '') {
    //         $this->return_fail('用户名不能为空', PARAMS_INVALID);
    //     }
    //     if ($data['password'] == '' || strlen($data['password']) < 6) {
    //         $this->return_fail('密码不能少于6位', PARAMS_INVALID);
    //     }

    //     $result = $this->admin->register($data['username'], $data['password']);

    //     if($result['success']) {
    //         $this->save_sys_log('创建管理员账号 '.$data['username']);
    //     }

    //     $this->return_result($result);
    // }

    /**
     * 管理员登录
     */
    public function login() 
    {
        $params = $this->input->post();

        $data = array(
            'username' => '',
            'password' => ''
        );
        foreach($data as $k => $v) {
            $data[$k] = get_param($params, $k);
        }
        if ($data['username'] == '') {
            $this->return_fail('用户名不能为空', PARAMS_INVALID);
        }
        if ($data['password'] == '' || strlen($data['password']) < 6) {
            $this->return_fail('密码不能少于6位', PARAMS_INVALID);
        }

        $result = $this->admin->login($data['username'], $data['password']);

        if($result['success']) {
            $this->save_sys_log('管理员 '.$result['msg']['userName'].' 登录系统');
        }

        $this->return_result($result);
    }
}
