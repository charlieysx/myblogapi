<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'core/Base_Controller.php';

class Friends extends Base_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->isAdmin = true;
        $this->check_token();
        $this->load->model('admin/Friends_model', 'friends');
    }

    /**
     * 获取友链类型列表
     */
    public function get_friends_type()
    {
        $result = $this->friends->get_friends_type();
        $this->return_result($result);
    }

    /**
     * 获取友链列表
     */
    public function get_friends_list()
    {
        $params = $this->input->get();

        $pageOpt = get_page($params);

        $result = $this->friends->get_friends_list($pageOpt['page'], $pageOpt['pageSize']);
        $this->return_result($result);
    }

    /**
     * 添加友链
     */
    public function add_friend()
    {
        $params = $this->input->post();

        $data = array(
            'name' => '',
            'url' => '',
            'typeId' => '',
            'typeName' => ''
        );
        foreach($data as $k => $v) {
            $data[$k] = get_param($params, $k);
        }
        if ($data['name'] == '') {
            $this->return_fail('名称不能为空', PARAMS_INVALID);
        }
        if ($data['url'] == '') {
            $this->return_fail('链接不能为空', PARAMS_INVALID);
        }
        if ($data['typeId'] == '' && $data['typeName'] == '') {
            $this->return_fail('类型不能为空', PARAMS_INVALID);
        }
        if ($data['typeId'] == '') {
            // 如果类型id为空，说明是新增的类型，先添加进类型表
            $type = $this->friends->add_type($data['typeName']);
            $data['typeId'] = $type['msg'];
        }

        $result = $this->friends->add_friend($data['name'], $data['url'], $data['typeId']);

        $this->return_result($result);
    }

    /**
     * 编辑友链
     */
    public function modify_friend()
    {
        $params = $this->input->post();

        $data = array(
            'friendId' => '',
            'name' => '',
            'url' => '',
            'typeId' => '',
            'typeName' => ''
        );
        foreach($data as $k => $v) {
            $data[$k] = get_param($params, $k);
        }
        if ($data['friendId'] == '') {
            $this->return_fail('id不能为空', PARAMS_INVALID);
        }
        if ($data['name'] == '') {
            $this->return_fail('名称不能为空', PARAMS_INVALID);
        }
        if ($data['url'] == '') {
            $this->return_fail('链接不能为空', PARAMS_INVALID);
        }
        if ($data['typeId'] == '' && $data['typeName'] == '') {
            $this->return_fail('类型不能为空', PARAMS_INVALID);
        }
        $had = $this->friends->had_friend($data['friendId']);
        if (!$had['success']) {
            $this->return_fail('id不存在', PARAMS_INVALID);
        }
        if ($data['typeId'] == '') {
            // 如果类型id为空，说明是新增的类型，先添加进类型表
            $type = $this->friends->add_type($data['typeName']);
            $data['typeId'] = $type['msg'];
        }

        $result = $this->friends->modify_friend($data['friendId'], $data['name'], $data['url'], $data['typeId']);

        $this->return_result($result);
    }

    /**
     * 删除友链
     */
    public function del_friend()
    {
        $params = $this->input->post();

        $friendId = get_param($params, 'friendId');
        if ($friendId == '') {
            $this->return_fail('id不能为空', PARAMS_INVALID);
        }
        $had = $this->friends->had_friend($friendId);
        if (!$had['success']) {
            $this->return_fail('id不存在', PARAMS_INVALID);
        }
        $this->friends->del_friend($friendId);

        $this->return_success('删除成功');
    }
}
