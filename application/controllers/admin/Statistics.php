<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Statistics extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->isAdmin = true;
        $this->check_token();
        $this->load->model('admin/Statistics_model', 'statistics');
        $this->load->model('admin/Article_model', 'article');
        $this->load->model('admin/Categories_model', 'categories');
        $this->load->model('admin/Comments_model', 'comments');
    }

    /**
     * 获取首页面板显示的统计信息
     */
    public function get_home_statistics() 
    {
        $publishCount = $this->article->get_article_count_by_status(0);
        $draftsCount = $this->article->get_article_count_by_status(2);
        $deletedCount = $this->article->get_article_count_by_status(1);
        $categoryCount = $this->categories->get_category_count();
        $tagCount = $this->categories->get_tag_count();
        $commentsCount = $this->comments->get_all_comments_count();

        $result = array(
            'publishCount'=> $publishCount['msg'],
            'draftsCount'=> $draftsCount['msg'],
            'deletedCount'=> $deletedCount['msg'],
            'categoryCount'=> $categoryCount['msg'],
            'tagCount'=> $tagCount['msg'],
            'commentsCount'=> $commentsCount['msg']
        );

        $this->return_success($result);
    }
}
