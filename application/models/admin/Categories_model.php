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
     * 添加分类
     */
    public function add_category($categoryName, $canDel = 1)
    {
        $category = $this->db->from(TABLE_CATEGORY)->where('name', $categoryName)->get()->row_array();
        if ($category) {
            return success($category['id']);
        }

        $id = create_id();
        $category = array(
            'id'=> $id,
            'name'=> $categoryName,
            'create_time'=> time(),
            'can_del'=> $canDel
        );
        $this->db->insert(TABLE_CATEGORY, $category);

        return success($id);
    }

    /**
     * 添加标签
     */
    public function add_tag($tagName)
    {
        $tag = $this->db->from(TABLE_TAG)->where('name', $tagName)->get()->row_array();
        if ($tag) {
            return success($tag['id']);
        }

        $id = create_id();
        $tag = array(
            'id'=> $id,
            'name'=> $tagName,
            'create_time'=> time()
        );
        $this->db->insert(TABLE_TAG, $tag);

        return success($id);
    }

    /**
     * 修改分类
     */
    public function modify_category($categoryId, $categoryName) 
    {
        $category = $this->db->from(TABLE_CATEGORY)->where('id', $categoryId)->get()->row_array();
        if ($category['can_del'] == '0') {
            return fail('默认分类不可修改');
        }
        $category = array(
            'name'=> $categoryName,
            'update_time'=> time()
        );

        $this->db->where('id', $categoryId)->update(TABLE_CATEGORY, $category);

        return success('更新成功');
    }

    /**
     * 修改标签
     */
    public function modify_tag($tagId, $tagName) 
    {
        $tag = array(
            'name'=> $tagName,
            'update_time'=> time()
        );

        $this->db->where('id', $tagId)->update(TABLE_TAG, $tag);

        return success('更新成功');
    }

    /**
     * 删除分类
     */
    public function del_category($categoryId) 
    {
        $category = $this->db->from(TABLE_CATEGORY)->WHERE('id', $categoryId)->get()->row_array();
        if ($category['can_del'] == '0') {
            return fail('默认分类不可删除');
        }
        $this->db->where('id', $categoryId)->delete(TABLE_CATEGORY);

        return success('删除成功');
    }

    /**
     * 删除标签
     */
    public function del_tag($tagId) 
    {
        $this->db->where('id', $tagId)->delete(TABLE_TAG);

        return success('删除成功');
    }

    /**
     * 获取分类列表
     */
    public function get_category_list($page, $pageSize) 
    {
        $categoryList = $this->db->from(TABLE_CATEGORY)
                                ->select('id as categoryId, name as categoryName, create_time as createTime, update_time as updateTime,
                                        status, article_count as articleCount, can_del as canDel')
                                ->limit($pageSize, $page*$pageSize)
                                ->order_by('aid', 'DESC')
                                ->get()
                                ->result_array();
        return success($categoryList);
    }

    /**
     * 获取标签列表
     */
    public function get_tag_list($page, $pageSize) 
    {

        $tagList = $this->db->from(TABLE_TAG)
                                ->select('id as tagId, name as tagName, create_time as createTime, update_time as updateTime,
                                        status, article_count as articleCount')
                                ->limit($pageSize, $page*$pageSize)
                                ->order_by('aid', 'DESC')
                                ->get()
                                ->result_array();
        return success($tagList);
    }

    /**
     * 获取分类数量
     */
    public function get_category_count() 
    {
        $count_all = $this->db->from(TABLE_CATEGORY)->count_all_results();
        return success($count_all);
    }

    /**
     * 获取标签数量
     */
    public function get_tag_count() 
    {
        $count_all = $this->db->from(TABLE_TAG)->count_all_results();
        return success($count_all);
    }

    /**
     * 添加 文章-标签 关系映射
     */
    public function add_article_tag_mapper($articleId, $tagId) 
    {
        $article = $this->db->from(TABLE_ARTICLE)->where('id', $articleId)->get()->row_array();
        if (!$article) {
            return fail('文章不存在');
        }

        $tag = $this->db->from(TABLE_TAG)->where('id', $tagId)->get()->row_array();
        if (!$tag) {
            return fail('标签不存在');
        }

        $conditions = array(
            'article_id'=> $articleId,
            'tag_id'=> $tagId
        );
        $mapper = $this->db->from(TABLE_ARTICLE_TAG_MAPPER)->where($conditions)->get()->row_array();
        if ($mapper) {
            return success($mapper['id']);
        }

        $time = time();

        $mapper = array(
            'article_id'=> $articleId,
            'tag_id'=> $tagId,
            'create_time'=> $time
        );

        // 标签的文章数量加1
        $update = array(
            'article_count'=> intval($tag['article_count']) + 1,
            'update_time'=> $time
        );

        $this->db->where('id', $tagId)->update(TABLE_TAG, $update);

        // 将 文章-标签 关系插入数据库
        $this->db->insert(TABLE_ARTICLE_TAG_MAPPER, $mapper);
        $id = $this->db->insert_id();

        return success($id);
    }

    /**
     * 删除 指定的tagId的所有行记录
     */
    public function del_article_tag_mapper_t($tagId) 
    {
        $this->db->where('tag_id', $tagId)->delete(TABLE_ARTICLE_TAG_MAPPER);

        return success('删除成功');
    }

    /**
     * 删除 指定的articleId的所有行记录
     */
    public function del_article_tag_mapper_a($articleId) 
    {
        $tagList = $this->db->where('article_id', $articleId)->from(TABLE_ARTICLE_TAG_MAPPER)->get()->result_array();
        $len = count($tagList);

        if ($len > 0) {
            $time = time();
            for($i = 0; $i < $len; ++$i) {
                $tag = $this->db->where('id', $tagList[$i]['tag_id'])->from(TABLE_TAG)->get()->row_array();
                $update = array(
                    'article_count'=> intval($tag['article_count']) - 1,
                    'update_time'=> $time
                );
                $this->db->where('id', $tag['id'])->update(TABLE_TAG, $update);
            }

            $this->db->where('article_id', $articleId)->delete(TABLE_ARTICLE_TAG_MAPPER);
        }

        return success('删除成功');
    }

    /**
     * 删除 文章-标签 关联
     */
    public function del_article_tag_mapper_at($articleId, $tagId)
    {
        $tag = $this->db->where('id', $tagId)->from(TABLE_TAG)->get()->row_array();

        $update = array(
            'article_count'=> intval($tag['article_count']) - 1,
            'update_time'=> time()
        );
        $this->db->where('id', $tagId)->update(TABLE_TAG, $update);

        $conditions = array(
            'tag_id'=> $tagId,
            'article_id'=> $articleId
        );
        $this->db->where($conditions)->delete(TABLE_ARTICLE_TAG_MAPPER);

        return success('删除成功');
    }

    /**
     * 将categoryId下的文章全部移到默认分类下
     */
    public function move_article_2_default_category($categoryId) 
    {
        $defaultCategory = $this->get_default_category()['msg'];
        $time = time();
        if ($defaultCategory) {
            // 获取原分类的文章数量
            $count = $this->db->where('category_id', $categoryId)->from(TABLE_ARTICLE)->count_all_results();

            $update = array(
                'article_count'=> intval($defaultCategory['article_count']) + $count,
                'update_time'=> $time
            );
            $this->db->where('id', $defaultCategory['id'])->update(TABLE_CATEGORY, $update);

            $this->db->where('category_id', $categoryId)->update(TABLE_ARTICLE, array(
                'category_id'=> $defaultCategory['id'],
                'update_time'=> $time
            ));

            return success();
        }
        return fail('无默认分类');
    }

    /**
     * 获取默认分类
     */
    public function get_default_category() {
        $defaultCategory = $this->db->where('can_del', 0)->from(TABLE_CATEGORY)->get()->row_array();

        return success($defaultCategory);
    }

    /**
     * 根据文章id获取标签列表
     */
    public function get_tag_by_article_id($articleId) {
        $tagList = $this->db->from(TABLE_ARTICLE_TAG_MAPPER)->where('article_id', $articleId)->get()->result_array();

        return success($tagList);
    }

    /**
     * 保存分类
     */
    public function save_category($aid, $cid)
    {
        $time = time();
        $article = array(
            'category_id'=> $cid,
            'update_time'=> $time
        );

        $a = $this->db->where('id', $aid)->from(TABLE_ARTICLE)->get()->row_array();

        // 如果文章分类改变，则要将原分类的文章数量-1
        if (!$a['category_id'] || $a['category_id'] != $cid) {
            $c = $this->db->where('id', $a['category_id'])->from(TABLE_CATEGORY)->get()->row_array();

            $update = array(
                'article_count'=> intval($c['article_count']) - 1,
                'update_time'=> $time
            );

            $this->db->where('id', $a['category_id'])->update(TABLE_CATEGORY, $update);

            $category = $this->db->where('id', $cid)->from(TABLE_CATEGORY)->get()->row_array();
    
            $update = array(
                'article_count'=> intval($category['article_count']) + 1,
                'update_time'=> $time
            );

            $this->db->where('id', $cid)->update(TABLE_CATEGORY, $update);
        }
        $this->db->where('id', $aid)->update(TABLE_ARTICLE, $article);

        return success();
    }

    public function get_category($categoryId)
    {
        $category = $this->db->where('id', $categoryId)
                            ->select('id, name, article_count as articleCount')
                            ->from(TABLE_CATEGORY)
                            ->get()
                            ->row_array();
        return success($category);
    }

    public function get_tag($tagId)
    {
        $tag = $this->db->where('id', $tagId)
                            ->select('id, name, article_count as articleCount')
                            ->from(TABLE_TAG)
                            ->get()
                            ->row_array();
        return success($tag);
    }
}
