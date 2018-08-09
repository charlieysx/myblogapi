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

    public function add($articleId, $parentId, $replyId, $name, $content, $sourceContent, $email)
    {
        $comments = array(
            'name'=> $name,
            'email'=> $email,
            'content'=> $content,
            'source_content'=> $sourceContent,
            'create_time'=> time(),
            'article_id'=> $articleId,
            'reply_id'=> $replyId,
            'parent_id'=> $parentId
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
}
