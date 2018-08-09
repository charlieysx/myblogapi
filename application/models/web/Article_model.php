<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Article_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询文章是否存在
     */
    public function had_article($articleId)
    {
        $article = $this->db->from(TABLE_ARTICLE)->where('id', $articleId)->get()->row_array();
        if ($article) {
            return success();
        }
        return fail('文章不存在');
    }

    /**
     * 获取文章的分类、标签信息
     */
    private function get_article_info($article)
    {
        $category = $this->db->from(TABLE_CATEGORY)
                            ->where('id', $article['categoryId'])
                            ->select('id, name')
                            ->get()
                            ->row_array();
        $tags = $this->db->from(TABLE_ARTICLE_TAG_MAPPER)
                        ->where('article_tag_mapper.article_id', $article['id'])
                        ->select('tag.id, tag.name')
                        ->join(TABLE_TAG, 'tag.id = article_tag_mapper.tag_id')
                        ->get()
                        ->result_array();
        $result = array(
            'article'=> $article,
            'category'=> $category,
            'tags'=> $tags
        );

        return $result;
    }

    /**
     * 获取文章
     */
    public function get_article($articleId)
    {
        $article = $this->db->from(TABLE_ARTICLE)
                            ->where('id', $articleId)
                            ->select('id, title, cover, sub_message as subMessage, html_content as htmlContent, pageview, status, category_id as categoryId, is_encrypt as isEncrypt,
                                        publish_time as publishTime, create_time as createTime, update_time as updateTime, delete_time as deleteTime')
                            ->get()
                            ->row_array();
        
        $article['pageview'] = intval($article['pageview']) + 1;
        $update = array(
            'pageview'=> $article['pageview']
        );
        $this->db->where('id', $articleId)->update(TABLE_ARTICLE, $update);

        $result = $this->get_article_info($article);

        return success($result);
    }

    /**
     * 获取文章列表
     */
    public function get_article_list($page, $pageSize)
    {
        $articleDB = $this->db->select('id, title, cover, sub_message as subMessage, pageview, status, category_id as categoryId, is_encrypt as isEncrypt,
                                        publish_time as publishTime, create_time as createTime, update_time as updateTime, delete_time as deleteTime')
                            ->order_by('publish_time', 'DESC')
                            ->where('article.id != ', '-1')
                            ->where('status', '0');
        
        $count = $articleDB->count_all_results(TABLE_ARTICLE, FALSE);

        $list = $articleDB->limit($pageSize, $page*$pageSize)->get()->result_array();

        $result = array();
        foreach($list as $k => $v) {
            array_push($result, $this->get_article_info($v));
        }

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $count,
            'list'=> $result
        );

        return success($data);
    }

    /**
     * 通过分类获取文章列表
     */
    public function get_article_list_by_category($categoryId, $page, $pageSize)
    {
        $articleDB = $this->db->select('id, title, cover, sub_message as subMessage, pageview, status, category_id as categoryId, is_encrypt as isEncrypt,
                                        publish_time as publishTime, create_time as createTime, update_time as updateTime, delete_time as deleteTime')
                            ->order_by('publish_time', 'DESC')
                            ->where('status', '0')
                            ->where('article.id != ', '-1')
                            ->where('category_id', $categoryId);
        
        $count = $articleDB->count_all_results(TABLE_ARTICLE, FALSE);

        $list = $articleDB->limit($pageSize, $page*$pageSize)->get()->result_array();

        $result = array();
        foreach($list as $k => $v) {
            array_push($result, $this->get_article_info($v));
        }

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $count,
            'list'=> $result
        );

        return success($data);
    }

    /**
     * 通过标签获取文章列表
     */
    public function get_article_list_by_tag($tagId, $page, $pageSize)
    {
        $articleDB = $this->db->select('article.id as id, title, cover, sub_message as subMessage, pageview, article.status as status,
                                        category_id as categoryId, is_encrypt as isEncrypt,
                                        article.publish_time as publishTime, article.create_time as createTime, 
                                        article.update_time as updateTime, article.delete_time as deleteTime')
                            ->order_by('article.publish_time', 'DESC')
                            ->join(TABLE_ARTICLE_TAG_MAPPER, 'article_tag_mapper.article_id = article.id', 'LEFT')
                            ->where('article_tag_mapper.tag_id', $tagId)
                            ->where('article.id != ', '-1')
                            ->where('article.status', '0');
        
        $count = $articleDB->count_all_results(TABLE_ARTICLE, FALSE);

        $list = $articleDB->limit($pageSize, $page*$pageSize)->get()->result_array();

        $result = array();
        foreach($list as $k => $v) {
            array_push($result, $this->get_article_info($v));
        }

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $count,
            'list'=> $result
        );

        return success($data);
    }

    /**
     * 获取文章数量
     */
    public function get_article_count() 
    {
        $count_all = $this->db->from(TABLE_ARTICLE)->where('status', '0')->where('article.id != ', '-1')->count_all_results();
        return success($count_all);
    }

    public function get_pre_next_article($article)
    {
        $pre = $this->db->from(TABLE_ARTICLE)
                        ->select('id, title')
                        ->where('status', '0')
                        ->where('publish_time >= ', $article['publishTime'])
                        ->where_not_in('id', array($article['id'], '-1'))
                        ->order_by('publish_time', 'ASC')
                        ->get()
                        ->row_array();
        $next = $this->db->from(TABLE_ARTICLE)
                        ->select('id, title')
                        ->where('status', '0')
                        ->where('publish_time <= ', $article['publishTime'])
                        ->where_not_in('id', array($article['id'], '-1'))
                        ->order_by('publish_time', 'DESC')
                        ->get()
                        ->row_array();
        
        $result = array(
            'pre'=> $pre,
            'next'=> $next
        );

        return success($result);
    }

    /**
     * 按文章标题和简介搜索
     */
    public function search($searchValue, $page, $pageSize)
    {
        $articleDB = $this->db->select('id, title, cover, sub_message as subMessage, pageview, status, category_id as categoryId, is_encrypt as isEncrypt,
                                        publish_time as publishTime, create_time as createTime, update_time as updateTime, delete_time as deleteTime')
                            ->order_by('publish_time', 'DESC')
                            ->where('status', '0')
                            ->where('article.id != ', '-1')
                            ->group_start()
                                ->like('title', $searchValue, 'both')
                                ->or_like('sub_message', $searchValue, 'both')
                            ->group_end();
        
        $count = $articleDB->count_all_results(TABLE_ARTICLE, FALSE);

        $list = $articleDB->limit($pageSize, $page*$pageSize)->get()->result_array();

        $result = array();
        foreach($list as $k => $v) {
            array_push($result, $this->get_article_info($v));
        }

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $count,
            'list'=> $result
        );

        return success($data);
    }
}
