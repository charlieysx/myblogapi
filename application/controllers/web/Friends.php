<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Friends extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('web/Friends_model', 'friends');
    }

    /**
     * 获取友链列表
     */
    public function get_friends_list() {
        $result = $this->friends->get_friends_list();
        $this->return_result($result);
    }
}
