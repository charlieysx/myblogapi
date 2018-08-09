<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Comments extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->isAdmin = true;
        $this->check_token();
        $this->load->model('admin/Comments_model', 'comments');
        $this->load->model('admin/Article_model', 'article');
    }

    public function add()
    {
        $params = $this->input->post();

        $data = array(
            'content' => '',
            'articleId' => '',
            'replyId' => '',
            'sourceContent' => ''
        );
        foreach($data as $k => $v) {
            $data[$k] = get_param($params, $k);
        }
        if ($data['content'] == '') {
            $this->return_fail('评论内容不能为空', PARAMS_INVALID);
        }

        // 检测文章是否存在
        $is = $this->article->had_article($data['articleId']);
        if (!$is['success']) {
            $this->return_result($is);
        }

        if ($data['replyId'] == 0) {
            $data['parentId'] = 0;
        } else {
            // 检测回复的评论是否存在
            $is = $this->comments->had_comments($data['replyId']);
            if (!$is['success']) {
                $this->return_result($is);
            }
            $comments = $is['msg'];
            if ($comments['article_id'] != $data['articleId']) {
                $this->return_fail('文章与评论不匹配');
            }
            if ($comments['parent_id'] == '0') {
                $data['parentId'] = $comments['id'];
            } else {
                $data['parentId'] = $comments['parent_id'];
            }
        }

        $this->comments->add($data['articleId'], $data['parentId'], $data['replyId'], $this->userInfo['username'], $data['content'], $data['sourceContent']);
        $this->return_success(($data['articleId'] == '-1' ? '留言' : '评论').'成功');
    }

    public function delete()
    {
        $params = $this->input->post();

        $commentsId = get_param($params, 'commentsId');
        // 检测评论是否存在
        $is = $this->comments->had_comments($commentsId);
        if (!$is['success']) {
            $this->return_result($is);
        }

        $this->comments->delete($commentsId);

        $this->return_success('删除成功');
    }

    public function get_comments()
    {
        $params = $this->input->get();

        $articleId = get_param($params, 'articleId');

        // 检测文章是否存在
        $is = $this->article->had_article($articleId);
        if (!$is['success']) {
            $this->return_result($is);
        }

        $list = $this->comments->get_comments($articleId);
        $count = $this->comments->get_comments_count($articleId);
        $result = array(
            'count'=> $count['msg'],
            'list'=> $list['msg']
        );
        $this->return_success($result);
    }

    public function get_all_comments()
    {
        $params = $this->input->get();

        $pageOpt = get_page($params);

        $result = $this->comments->get_all_comments($pageOpt['page'], $pageOpt['pageSize']);
        $this->return_result($result);
    }
}
