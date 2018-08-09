<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Base_Model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->helper('array');
        $this->load->database();
    }
}
