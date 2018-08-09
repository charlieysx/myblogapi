<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Send_Email {
    //类库
    //保存为Send_Email.php。位于./application/libraries/Send_Email.php
    private $config = array();//私有属性：配置Email参数
    private $email = null;

    public function __construct(){
        //将原始的 CodeIgniter 对象通过引用方式赋给变量$CI。& get_instance()
        //使用 get_instance() 函数。这个函数返回一个CodeIgniter super object
        $CI =& get_instance();

        $CI->load->library('email');

        //Email实例对象
        $this->email = $CI->email;
        $this->initConfig();
    }

    public function initConfig(){
        //QQ邮箱开启SMTP服务方法：
        //PC端【登录QQ邮箱】->点击【QQ邮箱】顶部的【设置】->进入【邮箱设置】->点击【账户】->找到【POP3/IMAP/SMTP/Exchange/CardDAV/CalDAV服务】并开启服务->手机扫码登录QQ并授权获取授权码
        //邮件发送协议
        $this->config['protocol'] = 'smtp';
        //SMTP 服务器地址。
        $this->config['smtp_host'] = 'ssl://smtp.qq.com';
        //发件人邮箱地址
        $this->config['smtp_user'] = 'codebearsh@qq.com';
        //腾讯QQ邮箱开启POP3/SMTP服务或者IMAP/SMTP服务时的授权码
        $this->config['smtp_pass'] = 'kgjywqqadlnmcahd';
        //发件人名称
        $this->config['smtp_name'] = 'CodeBear博客';
        //SMTP 端口
        $this->config['smtp_port'] = 465;
        //邮件类型。html网页或者text纯文本
        $this->config['mailtype'] = 'html';
        //字符集
        $this->config['charset'] = 'utf-8';
        //是否验证邮件地址
        $this->config['validate'] = true;
        //Email 优先级. 1 = 最高. 5 = 最低. 3 = 正常
        $this->config['priority'] = 1;
        //换行符. (使用 "\r\n" to 以遵守RFC 822).
        $this->config['crlf'] = "\r\n";
        //换行符. (使用 "\r\n" to 以遵守RFC 822).
        $this->config['newline'] = "\r\n";
        //初始化参数
        $this->email->initialize($this->config);
    }

    /**
     * @param $email_user 收件人邮箱地址
     * @param $msg 发送邮件的正文内容
     * @return TRUE:成功  FALSE:失败
     * */
    public function sendMsg($email_user, $title, $msg){
        //设置：发件人邮箱地址、发件人名称
        $this->email->from($this->config['smtp_user'], $this->config['smtp_name']);
        //设置：收件人邮箱地址
        $this->email->to($email_user);
        //设置：邮件主题
        $this->email->subject($title);
        //发送邮件正文
        $this->email->message($msg);
        //发送EMAIL. 根据发送结果，成功返回TRUE,失败返回FALSE。
        return $this->email->send();
    }
}
?>