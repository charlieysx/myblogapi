<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class System extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->isAdmin = true;
        $this->check_token();
        $this->load->model('admin/System_model', 'system');
    }

    /**
     * 获取系统日志
     */
    public function get_sys_log()
    {
        $params = $this->input->get();
        $pageOpt = get_page($params);

        $result = $this->system->get_sys_log($pageOpt['page'], $pageOpt['pageSize']);
        $this->return_result($result);
    }
}
