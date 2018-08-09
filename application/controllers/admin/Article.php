<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Article extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->isAdmin = true;
        $this->check_token();
        $this->load->model('admin/Article_model', 'article');
        $this->load->model('admin/Categories_model', 'categories');
        $this->load->model('admin/Comments_model', 'comments');
    }

    /**
     * 保存文章
     */
    public function save()
    {
        $params = $this->input->post();

        $article = array(
            'id'=> '',
            'content'=> '',
            'htmlContent'=> '',
            'title'=> '',
            'cover'=> '',
            'subMessage'=> '',
            'isEncrypt'=> ''
        );
        foreach($article as $k => $v) {
            $article[$k] = get_param($params, $k);
        }

        $number = is_number($article['isEncrypt']);
        if (!$number) {
            $article['isEncrypt'] = 0;
        } else {
            $article['isEncrypt'] = intval($article['isEncrypt']);
            if ($article['isEncrypt'] != 0 && $article['isEncrypt'] != 1) {
                $article['isEncrypt'] = 0;
            }
        }

        if ($article['id'] == '') {
            $a = $this->article->save_without_id($article['content'], $article['htmlContent'], $article['title'], $article['cover'], $article['subMessage'], $article['isEncrypt']);
            $article['id'] = $a['msg'];
        } else {
            // 检测文章是否存在
            $is = $this->article->had_article($article['id']);
            if (!$is['success']) {
                $this->return_result($is);
            }
            $this->article->save_with_id($article['id'], $article['content'], $article['htmlContent'], $article['title'], $article['cover'], $article['subMessage'], $article['isEncrypt']);
        }

        $this->save_sys_log('管理员 '.$this->userInfo['username'].' 保存了文章('.$article['id'].')');

        $this->return_success($article['id']);
    }

    /**
     * 发布文章
     */
    public function publish()
    {
        $params = $this->input->post();

        $article = array(
            'id'=> '',
            'content'=> '',
            'htmlContent'=> '',
            'title'=> '',
            'cover'=> '',
            'subMessage'=> '',
            'isEncrypt'=> '',
            'category'=> '',
            'tags'=> ''
        );
        foreach($article as $k => $v) {
            $article[$k] = get_param($params, $k);
        }

        if ($article['title'] == '') {
            $this->return_fail('标题不能为空');
        }

        if ($article['content'] == '') {
            $this->return_fail('文章内容不能为空');
        }

        if ($article['subMessage'] == '') {
            $this->return_fail('文章简介不能为空');
        }

        $number = is_number($article['isEncrypt']);
        if (!$number) {
            $article['isEncrypt'] = 0;
        } else {
            $article['isEncrypt'] = intval($article['isEncrypt']);
            if ($article['isEncrypt'] != 0 && $article['isEncrypt'] != 1) {
                $article['isEncrypt'] = 0;
            }
        }

        // 保存文章基本信息并获取id
        if ($article['id'] == '') {
            $save = $this->article->save_without_id($article['content'], $article['htmlContent'], $article['title'], $article['cover'], $article['subMessage'], $article['isEncrypt']);
        } else {
            // 检测文章是否存在
            $is = $this->article->had_article($article['id']);
            if (!$is['success']) {
                $this->return_result($is);
            }
            $save = $this->article->save_with_id($article['id'], $article['content'], $article['htmlContent'], $article['title'], $article['cover'], $article['subMessage'], $article['isEncrypt']);
        }

        $articleId = $save['msg'];

        // 保存分类
        $categoryId = $this->add_category($article['category']);
        $this->categories->save_category($articleId, $categoryId);

        // 保存标签
        $tags = $this->add_tags($article['tags']);
        $tcount = count($tags);
        for ($i = 0; $i < $tcount; ++$i) {
            $this->categories->add_article_tag_mapper($articleId, $tags[$i]);
        }

        // 发布
        $this->article->publish($articleId);

        $this->save_sys_log('管理员 '.$this->userInfo['username'].' 发布了文章('.$articleId.')');

        $this->return_success($articleId);
    }

    /**
     * 编辑文章（只有已发布的文章才可调用该接口）
     */
    public function modify()
    {
        $params = $this->input->post();

        $article = array(
            'id'=> '',
            'content'=> '',
            'htmlContent'=> '',
            'title'=> '',
            'cover'=> '',
            'subMessage'=> '',
            'isEncrypt'=> '',
            'category'=> '',
            'tags'=> ''
        );
        foreach($article as $k => $v) {
            $article[$k] = get_param($params, $k);
        }

        if ($article['id'] == '') {
            $this->return_fail('id不能为空');
        }

        if ($article['title'] == '') {
            $this->return_fail('标题不能为空');
        }

        if ($article['content'] == '') {
            $this->return_fail('文章内容不能为空');
        }

        if ($article['subMessage'] == '') {
            $this->return_fail('文章简介不能为空');
        }

        $number = is_number($article['isEncrypt']);
        if (!$number) {
            $article['isEncrypt'] = 0;
        } else {
            $article['isEncrypt'] = intval($article['isEncrypt']);
            if ($article['isEncrypt'] != 0 && $article['isEncrypt'] != 1) {
                $article['isEncrypt'] = 0;
            }
        }

        // 检测文章是否可编辑
        $is = $this->article->check_article($article['id']);
        if (!$is['success']) {
            $this->return_result($is);
        }

        // 保存分类
        $categoryId = $this->add_category($article['category']);
        $this->categories->save_category($article['id'], $categoryId);

        // 保存标签（添加新的，并删除旧的）
        $tagList = $this->categories->get_tag_by_article_id($article['id']);
        $oldCount = count($tagList['msg']);
        $oldTags = array();
        for ($i = 0; $i < $oldCount; ++$i) {
            array_push($oldTags, $tagList['msg'][$i]['tag_id']);
        }

        $tags = $this->add_tags($article['tags']);
        $tcount = count($tags);

        for ($i = 0; $i < $tcount; ++$i) {
            $index = array_search($tags[$i], $oldTags);
            if ($index !== false) {
                array_splice($oldTags, $index, 1);
            } else {
                $this->categories->add_article_tag_mapper($article['id'], $tags[$i]);
            }
        }

        $ncount = count($oldTags);

        for ($i = 0; $i < $ncount; ++$i) {
            $this->categories->del_article_tag_mapper_at($article['id'], $oldTags[$i]);
        }

        // 保存文章其他信息
        $this->article->save_with_id($article['id'], $article['content'], $article['htmlContent'], $article['title'], $article['cover'], $article['subMessage'], $article['isEncrypt']);

        $this->save_sys_log('管理员 '.$this->userInfo['username'].' 编辑了文章('.$article['id'].')');

        $this->return_success($article['id']);
    }

    /**
     * 删除文章
     */
    public function delete()
    {
        $params = $this->input->post();
        $articleId = get_param($params, 'id');

        if ($articleId == '') {
            $this->return_fail('id不能为空');
        }

        // 检测文章是否存在
        $is = $this->article->had_article($articleId);
        if (!$is['success']) {
            $this->return_result($is);
        }

        $this->categories->del_article_tag_mapper_a($articleId);
        $this->article->delete($articleId);
        $this->comments->delete_by_article($articleId);

        $this->save_sys_log('管理员 '.$this->userInfo['username'].' 删除了文章('.$articleId.')');

        $this->return_success('已删除');
    }

    private function add_category($data)
    {
        $cid = get_param($data, 'id');
        $cname = get_param($data, 'name');

        if ($cid != '') {
            $had = $this->categories->had_category('', $cid);
            if ($had['success']) {
                return $cid;
            }
        }
        
        if ($cname != '') {
            $cid = $this->categories->add_category($cname);
            return $cid['msg'];
        }

        $defaultCategory = $this->categories->get_default_category();
        return $defaultCategory['msg']['id'];
    }

    private function add_tags($data)
    {
        if ($data == '' || !is_array($data)) {
            return array();
        }

        $len = count($data);
        $tags = array();
        for ($i = 0; $i < $len; ++$i) {
            $tid = get_param($data[$i], 'id');
            $tname = get_param($data[$i], 'name');

            if ($tid != '') {
                $had = $this->categories->had_tag('', $tid);
                if ($had['success']) {
                    if (!in_array($tid, $tags)) {
                        array_push($tags, $tid);
                    }
                    continue;
                }
            }
            
            if ($tname != '') {
                $tid = $this->categories->add_tag($tname);
                if (!in_array($tid, $tags)) {
                    array_push($tags, $tid['msg']);
                }
            }
        }
        return $tags;
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

        $this->return_result($article);
    }

    /**
     * 获取文章列表
     */
    public function get_article_list()
    {
        $params = $this->input->get();

        $by = get_param($params, 'by');
        $status = get_param($params, 'status');
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
                if ($status != '0' && $status != '1' && $status != '2') {
                    $status = false;
                }
                $result = $this->article->get_article_list_by_status($status, $pageOpt['page'], $pageOpt['pageSize']);
                break;
        }

        $this->return_result($result);
    }
}
