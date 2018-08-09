<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class NotFound extends Base_Controller
{
    public function index()
    {
        $this->fail_response(fail_result("Error Not Found", null, NOT_FOUND), NOT_FOUND);
    }
}
