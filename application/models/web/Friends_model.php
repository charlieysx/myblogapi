<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Friends_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_friends_type()
    {
        $typeList = $this->db->from(TABLE_FRIENDS_TYPE)->order_by('id', 'DESC')->get()->result_array();

        return success($typeList);
    }

    public function get_friends_list()
    {
        $typeList = $this->db->from(TABLE_FRIENDS_TYPE)->where('count > ', 0)->order_by('id', 'DESC')->get()->result_array();
        $result = array();
        foreach($typeList as $k => $v) {
            $friendsList = $this->db->from(TABLE_FRIENDS)
                                    ->select('friend_id as friendId, friends.name, url, create_time as createTime, update_time as updateTime,
                                            delete_time as deleteTime, status')
                                    ->where('type_id', $v['id'])
                                    ->get()
                                    ->result_array();
            $v['list'] = $friendsList;
            array_push($result, $v);
        }
        return success($result);
    }

    public function get_friend_count()
    {
        $count_all = $this->db->from(TABLE_FRIENDS)->count_all_results();
        return success($count_all);
    }
}
