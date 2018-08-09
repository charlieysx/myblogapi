<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Categories_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询分类是否存在
     */
    public function had_category($categoryName = '', $categoryId = '') 
    {
        if ($categoryName != '') {
            $isEx = $this->db->where('name', $categoryName)->count_all_results(TABLE_CATEGORY);
        } else {
            $isEx = $this->db->where('id', $categoryId)->count_all_results(TABLE_CATEGORY);
        }
        if ($isEx) {
            return success();
        }
        return fail();
    }

    /**
     * 查询标签是否存在
     */
    public function had_tag($tagName = '', $tagId = '') 
    {
        if ($tagName != '') {
            $isEx = $this->db->where('name', $tagName)->count_all_results(TABLE_TAG);
        } else {
            $isEx = $this->db->where('id', $tagId)->count_all_results(TABLE_TAG);
        }
        if ($isEx) {
            return success();
        }
        return fail();
    }

    /**
     * 获取分类列表
     */
    public function get_category_list() 
    {
        $categoryList = $this->db->from(TABLE_CATEGORY)
                                ->select('id as categoryId, name as categoryName, create_time as createTime, update_time as updateTime,
                                        status, article_count as articleCount')
                                ->order_by('aid', 'DESC')
                                ->where('article_count > ', 0)
                                ->get()
                                ->result_array();
        return success($categoryList);
    }

    /**
     * 获取标签列表
     */
    public function get_tag_list() 
    {
        $tagList = $this->db->from(TABLE_TAG)
                                ->select('id as tagId, name as tagName, create_time as createTime, update_time as updateTime,
                                        status, article_count as articleCount')
                                ->order_by('aid', 'DESC')
                                ->where('article_count > ', 0)
                                ->get()
                                ->result_array();
        return success($tagList);
    }

    /**
     * 获取分类数量
     */
    public function get_category_count() 
    {
        $count_all = $this->db->from(TABLE_CATEGORY)->where('article_count > ', 0)->count_all_results();
        return success($count_all);
    }

    /**
     * 获取标签数量
     */
    public function get_tag_count() 
    {
        $count_all = $this->db->from(TABLE_TAG)->where('article_count > ', 0)->count_all_results();
        return success($count_all);
    }
}
