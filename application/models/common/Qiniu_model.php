<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qiniu_model extends Base_Model
{
    private $accessKey = "七牛accessKey";
    private $secretKey = "七牛secretKey";

    public function __construct()
    {
        parent::__construct();
    }

    public function get_upload_token($bucket, $withWater, $token)
    {
        if($bucket != 'blogimg') {
            return fail('bucket 目前只能是 blogimg', PARAMS_INVALID);
        }
        $id = md5(uniqid().$token).create_id();
        $deadline = time() + 3600;
        $args['scope'] = $bucket;
        $args['deadline'] = $deadline;
        $args['returnBody'] = '{"imgUrl": "http://blogimg.codebear.cn/$(key)"}'; // 这里要改成自己的域名或其他，上传成功后是返回这个json数据，url就是图片的地址
        $b = json_encode($args);
        $result = array(
            'token'=> $this->signWithData($b)
        );
        
        return success($result);
    }

    private function signWithData($data)
    {
        $encodedData = $this->base64_urlSafeEncode($data);
        return $this->sign($encodedData) . ':' . $encodedData;
    }

    private function base64_urlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

    private function sign($data)
    {
        $hmac = hash_hmac('sha1', $data, $this->secretKey, true);
        return $this->accessKey . ':' . $this->base64_urlSafeEncode($hmac);
    }
}
