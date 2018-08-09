<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class WebConfig extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('web/WebConfig_model', 'webConfig');
        $this->load->model('web/Article_model', 'article');
        $this->load->model('web/Categories_model', 'categories');
    }

    /**
     * 获取博客信息
     */
    public function get_blog_info()
    {
        $result = $this->webConfig->get_web_config();
        $info = $result['msg'];

        $result = $this->article->get_article_count();
        $articleCount = $result['msg'];

        $result = $this->categories->get_category_count();
        $categoryCount = $result['msg'];

        $result = $this->categories->get_tag_count();
        $tagCount = $result['msg'];

        $info['articleCount']= $articleCount;
        $info['categoryCount']= $categoryCount;
        $info['tagCount']= $tagCount;

        $this->return_success($info);
    }

    /**
     * 获取关于我页面
     */
    public function get_about_me()
    {
        $result = $this->webConfig->get_about_me();
        $qrcode = $this->webConfig->get_qr_code();
        $result['msg']['qrcode'] = $qrcode['msg'];
        $this->return_result($result);
    }

    /**
     * 获取我的简历
     */
    public function get_resume()
    {
        $result = $this->webConfig->get_resume();
        $this->return_result($result);
    }
}
