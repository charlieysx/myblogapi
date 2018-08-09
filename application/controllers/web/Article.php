<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Article extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('web/Article_model', 'article');
        $this->load->model('web/Categories_model', 'categories');
        $this->load->model('web/WebConfig_model', 'webConfig');
    }

    /**
     * 获取文章信息
     */
    public function get_article()
    {
        $params = $this->input->get();
        $articleId = get_param($params, 'id');

        if ($articleId == '') {
            $this->return_fail('id不能为空');
        }

        // 检测文章是否存在
        $is = $this->article->had_article($articleId);
        if (!$is['success']) {
            $this->return_result($is);
        }

        $article = $this->article->get_article($articleId);
        $qrcode = $this->webConfig->get_qr_code();
        $article['msg']['qrcode'] = $qrcode['msg'];

        $pn = $this->article->get_pre_next_article($article['msg']['article']);
        $article['msg']['pn'] = $pn['msg'];

        $this->return_result($article);
    }

    /**
     * 获取文章列表
     */
    public function get_article_list()
    {
        $params = $this->input->get();

        $by = get_param($params, 'by');
        $categoryId = get_param($params, 'categoryId');
        $tagId = get_param($params, 'tagId');
        $pageOpt = get_page($params);

        $result = array();
        switch($by) {
            case 'category':
                if ($categoryId == '') {
                    $this->return_fail('分类id不能为空');
                }
                $had = $this->categories->had_category('', $categoryId);
                if (!$had['success']) {
                    $this->return_fail('不存在该分类');
                }
                $result = $this->article->get_article_list_by_category($categoryId, $pageOpt['page'], $pageOpt['pageSize']);
                break;
            case 'tag':
                if ($tagId == '') {
                    $this->return_fail('标签id不能为空');
                }
                $had = $this->categories->had_tag('', $tagId);
                if (!$had['success']) {
                    $this->return_fail('不存在该标签');
                }
                $result = $this->article->get_article_list_by_tag($tagId, $pageOpt['page'], $pageOpt['pageSize']);
                break;
            default:
                $result = $this->article->get_article_list($pageOpt['page'], $pageOpt['pageSize']);
                break;
        }

        $this->return_result($result);
    }

    /**
     * 获取归档文章列表
     */
    public function get_article_archives()
    {
        $params = $this->input->get();

        $pageOpt = get_page($params);

        $result = $this->article->get_article_list($pageOpt['page'], $pageOpt['pageSize']);

        $msg = $result['msg'];

        
        $list = array();
        // foreach($msg['list'] as $k => $v) {
        //     $time = date('Y', $v['article']['publishTime']).'年';
        //     if (!isset($list[$time])) {
        //         $list[$time] = array();
        //     }
        //     array_push($list[$time], $v);
        // }
        foreach($msg['list'] as $k => $v) {
            $year = date('Y', $v['article']['publishTime']).'年';
            if (!isset($list[$year])) {
                $list[$year] = array();
            }
            $month = date('m', $v['article']['publishTime']).'月';
            if (!isset($list[$year][$month])) {
                $list[$year][$month] = array();
            }
            array_push($list[$year][$month], $v);
        }

        $result['msg']['list'] = $list;

        $this->return_result($result);
    }

    /**
     * 按标题和简介搜索
     */
    public function search()
    {
        $params = $this->input->get();

        $searchValue = get_param($params, 'searchValue');
        $pageOpt = get_page($params);

        if ($searchValue == '') {
            $this->return_fail('搜索内容不能为空');
        }

        $result = $this->article->search($searchValue, $pageOpt['page'], $pageOpt['pageSize']);

        $this->return_result($result);
    }
}