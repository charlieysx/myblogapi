<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Base_Controller extends CI_Controller
{
    protected $accessToken = '';
    protected $isAdmin = false;
    protected $userInfo = '';

    public function __construct() 
    {
        parent::__construct();

        $this->load->model('common/Common_model', 'common');

        $this->accessToken = $this->input->get_request_header('accessToken', '');
    }

    protected function response($data)
    {
        $this->output->set_status_header(SUCCESS)
                    ->set_header('Content-Type: application/json; charset=utf-8')
                    ->set_output(json_encode($data))
                    ->_display();
        exit;
    }

    protected function return_result($result) {
        if($result['success']) {
            $this->return_success($result['msg']);
        } else {
            $this->return_fail($result['msg'], $result['code']);
        }
    }

    protected function return_success($result, $success_msg = 'success') 
    {
        $result = success_result($success_msg, $result);
        $this->response($result);
    }

    protected function return_fail($msg, $code = -1) 
    {
        $result = fail_result($msg, '', $code);
        $this->response($result);
    }

    protected function check_token() {
        $result = $this->common->check_token($this->accessToken, $this->isAdmin);
        if(!$result['success']) {
            $this->return_fail('无效的token', TOKEN_INVALID);
        }
        $this->userInfo = $this->common->userInfo;
    }

    protected function get_ip() {
        //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }

    protected function save_sys_log($message) {
        $this->common->save_sys_log($message, $this->get_ip());
    }
}
