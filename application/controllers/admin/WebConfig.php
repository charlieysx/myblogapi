<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class WebConfig extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->isAdmin = true;
        $this->check_token();
        $this->load->model('admin/WebConfig_model', 'webConfig');
    }

    /**
     * 获取网站配置内容
     */
    public function get_web_config()
    {
        $result = $this->webConfig->get_web_config();
        $this->return_result($result);
    }

    /**
     * 修改网站配置
     */
    public function modify() 
    {
        $params = $this->input->post();

        $data = array(
            'blogName' => '',
            'avatar' => '',
            'sign' => '',
            'wxpayQrcode' => '',
            'alipayQrcode' => '',
            'github' => '',
            'settingPassword'=> '',
            'oldPassword' => '',
            'viewPassword' => ''
        );
        foreach($data as $k => $v) {
            $data[$k] = get_param($params, $k);
        }

        if ($data['settingPassword'] == 'true') {
            if ($data['viewPassword'] == '') {
                $this->return_fail('新密码不能为空', PARAMS_INVALID);
            }
        }

        $result = $this->webConfig->modify($data);

        if($result['success']) {
            $this->save_sys_log('管理员 '.$this->userInfo['username'].' 修改网站配置信息');
        }

        $this->return_result($result);
    }

    /**
     * 获取 关于我 页面内容
     */
    public function get_about_me()
    {
        $result = $this->webConfig->get_about_me();
        $this->return_result($result);
    }

    /**
     * 修改 关于我 页面内容
     */
    public function modify_about() 
    {
        $params = $this->input->post();

        $content = get_param($params, 'aboutMeContent');
        $htmlContent = get_param($params, 'htmlContent');

        $result = $this->webConfig->modify_about($content, $htmlContent);

        if($result['success']) {
            $this->save_sys_log('管理员 '.$this->userInfo['username'].'修改\'关于我\'页面');
        }

        $this->return_result($result);
    }

    /**
     * 获取 我的简历 页面内容
     */
    public function get_resume()
    {
        $result = $this->webConfig->get_resume();
        $this->return_result($result);
    }

    /**
     * 修改 我的简历 页面内容
     */
    public function modify_resume() 
    {
        $params = $this->input->post();

        $content = get_param($params, 'resumeContent');
        $htmlContent = get_param($params, 'htmlContent');

        $result = $this->webConfig->modify_resume($content, $htmlContent);

        if($result['success']) {
            $this->save_sys_log('管理员 '.$this->userInfo['username'].'修改\'我的简历\'页面');
        }

        $this->return_result($result);
    }
}
