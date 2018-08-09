<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Test_model extends Base_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function test() {
        
        return success('test');
    }
}