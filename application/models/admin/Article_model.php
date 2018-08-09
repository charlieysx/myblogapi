<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Article_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询文章是否存在并且是否可编辑
     */
    public function check_article($articleId)
    {
        $article = $this->db->from(TABLE_ARTICLE)->where('id', $articleId)->get()->row_array();
        if ($article) {
            if ($article['status'] != '0') {
                return fail('文章不可调用该编辑接口');
            }
            return success();
        }
        return fail('文章不存在');
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
     * 保存(没有id)
     */
    public function save_without_id($content, $htmlContent, $title, $cover, $subMessage, $isEncrypt)
    {
        $id = create_id();
        $time = time();
        $article = array(
            'id'=> $id,
            'content'=> $content,
            'html_content'=> $htmlContent,
            'title'=> $title,
            'cover'=> $cover,
            'sub_message'=> $subMessage,
            'is_encrypt'=> $isEncrypt,
            'create_time'=> $time,
            'update_time'=> $time,
            'status'=> 2
        );

        $this->db->insert(TABLE_ARTICLE, $article);

        return success($id);
    }


    /**
     * 保存(有id)
     */
    public function save_with_id($id, $content, $htmlContent, $title, $cover, $subMessage, $isEncrypt)
    {
        $article = array(
            'content'=> $content,
            'html_content'=> $htmlContent,
            'title'=> $title,
            'cover'=> $cover,
            'sub_message'=> $subMessage,
            'is_encrypt'=> $isEncrypt,
            'update_time'=> time()
        );

        $a = $this->db->where('id', $id)->from(TABLE_ARTICLE)->get()->row_array();
        if ($a['status'] == '1') {
            $article['status'] = 2;
        }

        $this->db->where('id', $id)->update(TABLE_ARTICLE, $article);

        return success($id);
    }

    /**
     * 发布文章
     */
    public function publish($articleId)
    {
        $article = array(
            'publish_time'=> time(),
            'status'=> 0
        );

        $this->db->where('id', $articleId)->update(TABLE_ARTICLE, $article);

        return success($articleId);
    }

    /**
     * 获取文章
     */
    public function get_article($articleId)
    {
        $article = $this->db->from(TABLE_ARTICLE)
                            ->where('id', $articleId)
                            ->select('id, title, cover, sub_message as subMessage, content, html_content as htmlContent, pageview, status, category_id as categoryId, is_encrypt as isEncrypt,
                                        publish_time as publishTime, create_time as createTime, update_time as updateTime, delete_time as deleteTime')
                            ->get()
                            ->row_array();
        $category = $this->db->from(TABLE_CATEGORY)
                            ->where('id', $article['categoryId'])
                            ->select('id, name')
                            ->get()
                            ->row_array();
        $tags = $this->db->from(TABLE_ARTICLE_TAG_MAPPER)
                        ->where('article_tag_mapper.article_id', $articleId)
                        ->select('tag.id, tag.name')
                        ->join(TABLE_TAG, 'tag.id = article_tag_mapper.tag_id')
                        ->get()
                        ->result_array();

        $result = array(
            'article'=> $article,
            'category'=> $category,
            'tags'=> $tags
        );

        return success($result);
    }

    /**
     * 通过状态获取文章列表
     */
    public function get_article_list_by_status($status, $page, $pageSize)
    {
        $articleDB = $this->db->select('article.id as id, title, cover, pageview, article.status as status, is_encrypt as isEncrypt, category.name as categoryName,
                                        article.create_time as createTime, article.update_time as updateTime, 
                                        article.publish_time as publishTime, article.delete_time as deleteTime')
                            ->order_by('article.publish_time', 'DESC')
                            ->where('article.id != ', '-1')
                            ->join(TABLE_CATEGORY, 'category.id = article.category_id', 'LEFT');
        if ($status !== false) {
            $articleDB->where('article.status', $status);
        }

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $articleDB->count_all_results(TABLE_ARTICLE, FALSE),
            'list'=> $articleDB->limit($pageSize, $page*$pageSize)->get()->result_array()
        );

        return success($data);
    }

    /**
     * 通过分类获取文章列表
     */
    public function get_article_list_by_category($categoryId, $page, $pageSize)
    {
        $articleDB = $this->db->select('article.id as id, title, cover, pageview, article.status as status, is_encrypt as isEncrypt, category.name as categoryName,
                                    article.create_time as createTime, article.update_time as updateTime,
                                    article.publish_time as publishTime, article.delete_time as deleteTime')
                                ->where('article.category_id', $categoryId)
                                ->where('article.id != ', '-1')
                                ->order_by('article.update_time', 'DESC')
                                ->join(TABLE_CATEGORY, 'category.id = article.category_id', 'LEFT');

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $articleDB->count_all_results(TABLE_ARTICLE, FALSE),
            'list'=> $articleDB->limit($pageSize, $page*$pageSize)->get()->result_array()
        );

        return success($data);
    }

    /**
     * 通过标签获取文章列表
     */
    public function get_article_list_by_tag($tagId, $page, $pageSize)
    {
        $articleDB = $this->db->select('article.id as id, title, cover, pageview, article.status as status, is_encrypt as isEncrypt, category.name as categoryName,
                                        article.create_time as createTime, article.update_time as updateTime, 
                                        article.publish_time as publishTime, article.delete_time as deleteTime')
                                ->join(TABLE_CATEGORY, 'category.id = article.category_id', 'LEFT')
                                ->join(TABLE_ARTICLE_TAG_MAPPER, 'article_tag_mapper.article_id = article.id', 'LEFT')
                                ->order_by('article.update_time', 'DESC')
                                ->where('article.id != ', '-1')
                                ->where('article_tag_mapper.tag_id', $tagId);

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $articleDB->count_all_results(TABLE_ARTICLE, FALSE),
            'list'=> $articleDB->limit($pageSize, $page*$pageSize)->get()->result_array()
        );

        return success($data);
    }

    /**
     * 删除文章
     */
    public function delete($articleId)
    {
        $article = $this->db->from(TABLE_ARTICLE)->where('id', $articleId)->get()->row_array();

        $time = time();
        if ($article['status'] == '1') {
            $this->db->where('id', $articleId)->delete(TABLE_ARTICLE);
        } else {
            $data = array(
                'category_id'=> '',
                'status'=> '2',
                'delete_time'=> $time,
                'update_time'=> $time
            );
            if ($article['status'] == '2') {
                $data['status'] = '1';
            }
            $this->db->where('id', $articleId)->update(TABLE_ARTICLE, $data);
        }

        // 等于0表示已发布，才有分类
        if ($article['status'] == '0') {
            $c = $this->db->where('id', $article['category_id'])->from(TABLE_CATEGORY)->get()->row_array();

            $update = array(
                'article_count'=> intval($c['article_count']) - 1,
                'update_time'=> $time
            );
            $this->db->where('id', $article['category_id'])->update(TABLE_CATEGORY, $update);
        }
    }

    /**
     * 根据状态获取文章数量
     */
    public function get_article_count_by_status($status)
    {
        $count = $this->db->where('status', $status)->where('article.id != ', '-1')->count_all_results(TABLE_ARTICLE);

        return success($count);
    }
}
