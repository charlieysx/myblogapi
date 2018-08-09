<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Qiniu extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->isAdmin = true;
        $this->check_token();
        $this->load->model('common/Qiniu_model', 'qiniu');
    }

    /**
     * 获取七牛token
     */
    public function get_qiniu_token() 
    {
        $params = $this->input->get();
        $bucket = get_param($params, 'bucket', 'blogimg');
        $withWater = get_param($params, 'withWater', true);
        $result = $this->qiniu->get_upload_token($bucket, $withWater, $this->accessToken);

        $this->return_result($result);
    }
}
