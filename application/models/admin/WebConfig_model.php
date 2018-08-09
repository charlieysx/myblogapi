<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WebConfig_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_web_config()
    {
        $config = $this->db->from(TABLE_BLOG_CONFIG)
                            ->select('blog_name as blogName, avatar, sign, wxpay_qrcode as wxpayQrcode,
                                    alipay_qrcode as alipayQrcode, github, salt')
                            ->get()
                            ->row_array();
        
        if ($config && $config['salt']) {
            $config['hadOldPassword'] = true;
        } else {
            $config['hadOldPassword'] = false;
        }
        if (!$config) {
            $config = false;
        }
        unset($config['salt']);
        
       return success($config);
    }

    public function modify($params)
    {
        $config = $this->db->from(TABLE_BLOG_CONFIG)
                            ->get()
                            ->row_array();

        if ($config) {
            if ($params['settingPassword'] == 'true') {
                // 如果有秘钥存在，说明已经有设置过密码了，要对密码进行比较
                if ($config['salt']) {
                    if (!cb_passwordEqual($config['view_password'], $config['salt'], $params['oldPassword'])) {
                        return fail('原密码错误');
                    }
                }
                // 加密新密码
                $encrypt = cb_encrypt($params['viewPassword']);
                $config['view_password'] = $encrypt['password'];
                $config['salt'] = $encrypt['salt'];
            }
            $config['blog_name'] = $params['blogName'];
            $config['avatar'] = $params['avatar'];
            $config['sign'] = $params['sign'];
            $config['wxpay_qrcode'] = $params['wxpayQrcode'];
            $config['alipay_qrcode'] = $params['alipayQrcode'];
            $config['github'] = $params['github'];

            $this->db->where('id', $config['id'])->update(TABLE_BLOG_CONFIG, $config);
        } else {
            $config = array();
            if ($params['settingPassword'] == 'true') {
                // 加密新密码
                $encrypt = cb_encrypt($params['viewPassword']);
                $config['view_password'] = $encrypt['password'];
                $config['salt'] = $encrypt['salt'];
            }
            $config['blog_name'] = $params['blogName'];
            $config['avatar'] = $params['avatar'];
            $config['sign'] = $params['sign'];
            $config['wxpay_qrcode'] = $params['wxpayQrcode'];
            $config['alipay_qrcode'] = $params['alipayQrcode'];
            $config['github'] = $params['github'];

            $this->db->insert(TABLE_BLOG_CONFIG, $config);
        }
        
        return success('更新成功');
    }

    public function get_about_me()
    {
        $config = $this->db->from(TABLE_PAGES)
                            ->select('type, md, html')
                            ->where('type', 'about')
                            ->get()
                            ->row_array();
        if (!$config) {
            $config = array();
        }
        if (!isset($config['md'])) {
            $config['md'] = '';
        }
        if (!isset($config['html'])) {
            $config['html'] = '';
        }

        return success($config);
    }

    public function modify_about($content, $htmlContent)
    {
        $config = $this->db->from(TABLE_PAGES)
                            ->where('type', 'about')
                            ->get()
                            ->row_array();
        if ($config) {
            $this->db->where('id', $config['id'])->update(TABLE_PAGES, array('md'=> $content, 'html'=> $htmlContent));
        } else {
            $this->db->insert(TABLE_PAGES, array('md'=> $content, 'html'=> $htmlContent, 'type'=> 'about'));
        }
        
        return success('更新成功');
    }

    public function get_resume()
    {
        $config = $this->db->from(TABLE_PAGES)
                            ->select('type, md, html')
                            ->where('type', 'resume')
                            ->get()
                            ->row_array();
        if (!$config) {
            $config = array();
        }
        if (!isset($config['md'])) {
            $config['md'] = '';
        }
        if (!isset($config['html'])) {
            $config['html'] = '';
        }

        return success($config);
    }

    public function modify_resume($content, $htmlContent)
    {
        $config = $this->db->from(TABLE_PAGES)
                            ->where('type', 'resume')
                            ->get()
                            ->row_array();
        if ($config) {
            $this->db->where('id', $config['id'])->update(TABLE_PAGES, array('md'=> $content, 'html'=> $htmlContent));
        } else {
            $this->db->insert(TABLE_PAGES, array('md'=> $content, 'html'=> $htmlContent, 'type'=> 'resume'));
        }
        
        return success('更新成功');
    }
}
