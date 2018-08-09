<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Categories extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->isAdmin = true;
        $this->check_token();
        $this->load->model('admin/Categories_model', 'categories');
    }

    /**
     * 添加分类
     */
    public function add_category() 
    {
        $params = $this->input->post();

        $categoryName = get_param($params, 'categoryName');

        if ($categoryName == '') {
            $this->return_fail('分类名称不能为空', PARAMS_INVALID);
        }

        $had = $this->categories->had_category($categoryName);
        if ($had['success']) {
            $this->return_fail('该分类已经存在，请勿重复添加', PARAMS_INVALID);
        }

        $canDel = get_param($params, 'canDel');
        $canDel = ($canDel == '' ? 1 : 0);

        $result = $this->categories->add_category($categoryName, $canDel);

        $this->return_result($result);
    }

    /**
     * 添加标签
     */
    public function add_tag() 
    {
        $params = $this->input->post();

        $tagName = get_param($params, 'tagName');

        if ($tagName == '') {
            $this->return_fail('标签名称不能为空', PARAMS_INVALID);
        }

        $had = $this->categories->had_tag($tagName);
        if ($had['success']) {
            $this->return_fail('该标签已经存在，请勿重复添加', PARAMS_INVALID);
        }

        $result = $this->categories->add_tag($tagName);

        $this->return_result($result);
    }

    /**
     * 修改分类
     */
    public function modify_category() 
    {
        $params = $this->input->post();

        $categoryId = get_param($params, 'categoryId');
        $categoryName = get_param($params, 'categoryName');

        if ($categoryId == '') {
            $this->return_fail('分类id不能为空', PARAMS_INVALID);
        }

        if ($categoryName == '') {
            $this->return_fail('分类名称不能为空', PARAMS_INVALID);
        }

        $had = $this->categories->had_category('', $categoryId);
        if (!$had['success']) {
            $this->return_fail('该分类不存在', PARAMS_INVALID);
        }

        $result = $this->categories->modify_category($categoryId, $categoryName);

        $this->return_result($result);
    }

    /**
     * 修改标签
     */
    public function modify_tag() 
    {
        $params = $this->input->post();

        $tagId = get_param($params, 'tagId');
        $tagName = get_param($params, 'tagName');

        if ($tagId == '') {
            $this->return_fail('标签id不能为空', PARAMS_INVALID);
        }

        if ($tagName == '') {
            $this->return_fail('标签名称不能为空', PARAMS_INVALID);
        }

        $had = $this->categories->had_tag('', $tagId);
        if (!$had['success']) {
            $this->return_fail('该标签不存在', PARAMS_INVALID);
        }

        $result = $this->categories->modify_tag($tagId, $tagName);

        $this->return_result($result);
    }

    /**
     * 删除分类
     */
    public function del_category() 
    {
        $params = $this->input->post();

        $categoryId = get_param($params, 'categoryId');

        if ($categoryId == '') {
            $this->return_fail('id不能为空', PARAMS_INVALID);
        }

        $had = $this->categories->had_category('', $categoryId);
        if (!$had['success']) {
            $this->return_fail('该分类不存在', PARAMS_INVALID);
        }

        $result = $this->categories->del_category($categoryId);

        if ($result['success']) {
            // 将该分类下的文章移到默认分类中
            $this->categories->move_article_2_default_category($categoryId);
        }

        $this->return_result($result);
    }

    /**
     * 删除标签
     */
    public function del_tag() 
    {
        $params = $this->input->post();

        $tagId = get_param($params, 'tagId');

        if ($tagId == '') {
            $this->return_fail('id不能为空', PARAMS_INVALID);
        }

        $had = $this->categories->had_tag('', $tagId);
        if (!$had['success']) {
            $this->return_fail('该标签不存在', PARAMS_INVALID);
        }

        // 同时删除所有该 标签-文章 的映射
        $this->categories->del_article_tag_mapper_t($tagId);
        // 删除标签
        $this->categories->del_tag($tagId);

        $this->return_success('删除成功');
    }

    /**
     * 获取分类列表
     */
    public function get_category_list() 
    {
        $params = $this->input->get();
        $pageOpt = get_page($params);
        $all = get_param($params, 'all');

        $count = $this->categories->get_category_count();
        if ($all == 'true') {
            $pageOpt['pageSize'] = $count['msg'];
        }

        $list = $this->categories->get_category_list($pageOpt['page'], $pageOpt['pageSize']);
        $result = array(
            'page'=> $pageOpt['page'],
            'pageSize'=> $pageOpt['pageSize'],
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
        $params = $this->input->get();
        $pageOpt = get_page($params);
        $all = get_param($params, 'all');

        $count = $this->categories->get_tag_count();
        if ($all == 'true') {
            $pageOpt['pageSize'] = $count['msg'];
        }

        $list = $this->categories->get_tag_list($pageOpt['page'], $pageOpt['pageSize']);
        $result = array(
            'page'=> $pageOpt['page'],
            'pageSize'=> $pageOpt['pageSize'],
            'count'=> $count['msg'],
            'list'=> $list['msg']
        );
        $this->return_success($result);
    }

    /**
     * 获取分类信息
     */
    public function get_category()
    {
        $params = $this->input->get();
        $categoryId = get_param($params, 'categoryId');

        if ($categoryId == '') {
            $this->return_fail('id不能为空', PARAMS_INVALID);
        }

        $had = $this->categories->had_category('', $categoryId);
        if (!$had['success']) {
            $this->return_fail('该分类不存在', PARAMS_INVALID);
        }

        $result = $this->categories->get_category($categoryId);

        $this->return_result($result);
    }

    /**
     * 获取标签信息
     */
    public function get_tag()
    {
        $params = $this->input->get();
        $tagId = get_param($params, 'tagId');

        if ($tagId == '') {
            $this->return_fail('id不能为空', PARAMS_INVALID);
        }

        $had = $this->categories->had_tag('', $tagId);
        if (!$had['success']) {
            $this->return_fail('该标签不存在', PARAMS_INVALID);
        }

        $result = $this->categories->get_tag($tagId);

        $this->return_result($result);
    }
}
