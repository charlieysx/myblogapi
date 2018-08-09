<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Comments_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function had_comments($id)
    {
        $comments = $this->db->from(TABLE_COMMENTS)->where('id', $id)->get()->row_array();
        if ($comments) {
            return success($comments);
        }
        return fail('评论不存在');
    }

    public function add($articleId, $parentId, $replyId, $name, $content, $sourceContent)
    {
        $comments = array(
            'name'=> $name,
            'content'=> $content,
            'source_content'=> $sourceContent,
            'create_time'=> time(),
            'article_id'=> $articleId,
            'reply_id'=> $replyId,
            'parent_id'=> $parentId,
            'is_author'=> 1
        );

        $this->db->insert(TABLE_COMMENTS, $comments);

        return success('评论成功');
    }

    public function get_comments($articleId)
    {
        $list = $this->db->select('id, parent_id as parentId, article_id as articleId, reply_id as replyId, name, content,
                                    create_time as createTime, is_author as isAuthor')
                            ->order_by('create_time', 'DESC')
                            ->where('status', '0')
                            ->where('article_id', $articleId)
                            ->where('parent_id', '0')
                            ->from(TABLE_COMMENTS)
                            ->get()
                            ->result_array();

        foreach($list as $k => $v) {
            $children = $this->db->select('id, parent_id as parentId, article_id as articleId, reply_id as replyId, name, content,
                                            create_time as createTime, is_author as isAuthor')
                                ->order_by('create_time', 'ASC')
                                ->where('status', '0')
                                ->where('article_id', $articleId)
                                ->where('parent_id', $v['id'])
                                ->from(TABLE_COMMENTS)
                                ->get()
                                ->result_array();
            $list[$k]['children'] = $children;
        }
        return success($list);
    }

    public function get_comments_count($articleId)
    {
        $count_all = $this->db->from(TABLE_COMMENTS)->where('article_id', $articleId)->count_all_results();
        return success($count_all);
    }

    public function get_all_comments_count()
    {
        $count_all = $this->db->from(TABLE_COMMENTS)->count_all_results();
        return success($count_all);
    }

    public function get_all_comments($page, $pageSize)
    {
        $commentsDB = $this->db->select('comments.id as id, parent_id as parentId, article_id as articleId, reply_id as replyId, name, comments.content as content,
                                        comments.create_time as createTime, is_author as isAuthor, article.title as articleTitle, comments.status as status')
                            ->order_by('comments.create_time', 'DESC')
                            ->join(TABLE_ARTICLE, 'article.id = article_id');
        
        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $commentsDB->count_all_results(TABLE_COMMENTS, FALSE),
            'list'=> $commentsDB->limit($pageSize, $page*$pageSize)->get()->result_array()
        );

        return success($data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id)
                ->or_where('parent_id', $id)
                ->or_where('reply_id', $id)
                ->delete(TABLE_COMMENTS);

        return success('已删除');
    }


    public function delete_by_article($articleId)
    {
        $this->db->where('article_id', $articleId)->delete(TABLE_COMMENTS);

        return success('已删除');
    }
}
