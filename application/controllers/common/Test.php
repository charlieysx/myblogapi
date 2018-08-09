<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class test extends Base_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('common/Test_model', 'test');
    }

    public function test() {
        $this->return_result($this->test->test());
    }
}
