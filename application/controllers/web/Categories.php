<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Categories extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('web/Categories_model', 'categories');
    }

    /**
     * 获取分类列表
     */
    public function get_category_list() 
    {
        $list = $this->categories->get_category_list();
        $count = $this->categories->get_category_count();
        $result = array(
            'count'=> $count['msg'],
            'list'=> $list['msg']
        );
        $this->return_success($result);
    }

    /**
     * 获取标签列表
     */
    public function get_tag_list() 
    {
        $list = $this->categories->get_tag_list();
        $count = $this->categories->get_tag_count();
        $result = array(
            'count'=> $count['msg'],
            'list'=> $list['msg']
        );
        $this->return_success($result);
    }
}
