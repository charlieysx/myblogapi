<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Comments extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('web/Comments_model', 'comments');
        $this->load->model('web/Article_model', 'article');
        $this->load->library('Send_Email', null, 'sendQqEmail');
    }

    public function add()
    {
        $params = $this->input->post();

        $data = array(
            'name' => '',
            'email' => '',
            'content' => '',
            'sourceContent' => '',
            'articleId' => '',
            'replyId' => ''
        );
        foreach($data as $k => $v) {
            $data[$k] = get_param($params, $k);
        }
        if ($data['name'] == '') {
            $this->return_fail('昵称不能为空', PARAMS_INVALID);
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

        $this->comments->add($data['articleId'], $data['parentId'], $data['replyId'], $data['name'], $data['content'], $data['sourceContent'], $data['email']);

        $message = '来自 '.$data['name'].' 的'.($data['articleId'] == '-1' ? '留言' : '评论');
        $this->sendQqEmail->sendMsg('249900679@qq.com', $message, $data['sourceContent']);

        $this->return_success(($data['articleId'] == '-1' ? '留言' : '评论').'成功');
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
}
