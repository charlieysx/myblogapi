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

    // public function get_friends_list()
    // {
    //     $typeList = $this->db->from(TABLE_FRIENDS_TYPE)->where('count > ', 0)->order_by('id', 'DESC')->get()->result_array();
    //     $result = array();
    //     foreach($typeList as $k => $v) {
    //         $friendsList = $this->db->from(TABLE_FRIENDS)
    //                                 ->select('friend_id as friendId, friends.name, url, create_time as createTime, update_time as updateTime,
    //                                         delete_time as deleteTime, status')
    //                                 ->where('type_id', $v['id'])
    //                                 ->get()
    //                                 ->result_array();
    //         $v['list'] = $friendsList;
    //         array_push($result, $v);
    //     }
    //     return success($result);
    // }

    public function get_friends_list($page, $pageSize)
    {
        $friendsDB = $this->db->select('friend_id as friendId, friends.name, url, create_time as createTime, update_time as updateTime,
                                        delete_time as deleteTime, friends.status as status, friends_type.name as typeName, type_id as typeId')
                                ->order_by('aid', 'DESC')
                                ->join(TABLE_FRIENDS_TYPE, 'type_id = friends_type.id');

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $friendsDB->count_all_results(TABLE_FRIENDS, FALSE),
            'list'=> $friendsDB->limit($pageSize, $page*$pageSize)->get()->result_array()
        );
        return success($data);
    }

    public function add_type($typeName)
    {
        // 如果已经存在这个类型名，就直接返回id
        $type = $this->db->from(TABLE_FRIENDS_TYPE)->where('name', $typeName)->get()->row_array();
        if ($type) {
            return success($type['id']);
        }

        $type = array(
            'name'=> $typeName
        );
        
        $this->db->insert(TABLE_FRIENDS_TYPE, $type);
        $id = $this->db->insert_id();

        return success($id);
    }

    public function add_friend($name, $url, $typeId)
    {
        $friend = array(
            'friend_id'=> create_id(),
            'name'=> $name,
            'url'=> $url,
            'type_id'=> $typeId,
            'create_time'=> time()
        );

        $type = $this->db->where('id', $typeId)->from(TABLE_FRIENDS_TYPE)->get()->row_array();

        $typeCount = intval($type['count']) + 1;
        
        $this->db->where('id', $typeId)->update(TABLE_FRIENDS_TYPE, array('count'=> $typeCount));
        $this->db->insert(TABLE_FRIENDS, $friend);

        return success('添加成功');
    }

    public function had_friend($friendId)
    {
        $isEx = $this->db->where('friend_id', $friendId)->count_all_results(TABLE_FRIENDS);
        if ($isEx) {
            return success();
        }
        return fail();
    }

    public function modify_friend($friendId, $name, $url, $typeId)
    {
        $friend = array(
            'name'=> $name,
            'url'=> $url,
            'type_id'=> $typeId,
            'update_time'=> time()
        );
        $oldType = $this->db->where('friend_id', $friendId)
                            ->from(TABLE_FRIENDS)
                            ->select('friends_type.count as count, friends_type.id as id')
                            ->join(TABLE_FRIENDS_TYPE, 'type_id = friends_type.id')
                            ->get()
                            ->row_array();
        $oldCount = intval($oldType['count']) - 1;

        $newType = $this->db->where('id', $typeId)->from(TABLE_FRIENDS_TYPE)->get()->row_array();
        $newCount = intval($newType['count']) + 1;

        $this->db->where('id', $oldType['id'])->update(TABLE_FRIENDS_TYPE, array('count'=> $oldCount));
        $this->db->where('id', $typeId)->update(TABLE_FRIENDS_TYPE, array('count'=> $newCount));
        $this->db->where('friend_id', $friendId)->update(TABLE_FRIENDS, $friend);

        return success('更新成功');
    }

    public function del_friend($friendId)
    {
        $oldType = $this->db->where('friend_id', $friendId)
                            ->from(TABLE_FRIENDS)
                            ->select('friends_type.count as count, friends_type.id as id')
                            ->join(TABLE_FRIENDS_TYPE, 'type_id = friends_type.id')
                            ->get()
                            ->row_array();
        $oldCount = intval($oldType['count']) - 1;
        $this->db->where('id', $oldType['id'])->update(TABLE_FRIENDS_TYPE, array('count'=> $oldCount));
        $this->db->where('friend_id', $friendId)->delete(TABLE_FRIENDS);

        return success('删除成功');
    }

    public function get_friend_count()
    {
        $count_all = $this->db->from(TABLE_FRIENDS)->count_all_results();
        return success($count_all);
    }
}
