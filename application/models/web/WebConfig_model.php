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
                            ->select('blog_name as blogName, avatar, sign, github')
                            ->get()
                            ->row_array();
        
        if (!$config) {
            $config = false;
        }
        
       return success($config);
    }

    public function get_about_me()
    {
        $config = $this->db->from(TABLE_PAGES)
                            ->select('html')
                            ->where('type', 'about')
                            ->get()
                            ->row_array();
        if (!$config || !isset($config['html'])) {
            $config = array(
                'html'=> ''
            );
        }

        return success($config);
    }

    public function get_resume()
    {
        $config = $this->db->from(TABLE_PAGES)
                            ->select('html')
                            ->where('type', 'resume')
                            ->get()
                            ->row_array();
        if (!$config || !isset($config['html'])) {
            $config = array(
                'html'=> ''
            );
        }

        return success($config);
    }

    public function get_qr_code() {
        $config = $this->db->from(TABLE_BLOG_CONFIG)
                            ->select('wxpay_qrcode as wxpayQrcode, alipay_qrcode as alipayQrcode')
                            ->get()
                            ->row_array();
        
        if (!$config) {
            $config = false;
        }
        
       return success($config);
    }
}
