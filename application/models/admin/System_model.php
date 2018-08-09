<?php
defined('BASEPATH') or exit('No direct script access allowed');

class System_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_sys_log($page, $pageSize) 
    {
        $logDB = $this->db->order_by('time', 'DESC');

        $data = array(
            'page'=> $page,
            'pageSize'=> $pageSize,
            'count'=> $logDB->count_all_results(TABLE_SYS_LOG, FALSE),
            'list'=> $logDB->limit($pageSize, $page*$pageSize)->get()->result_array()
        );
        return success($data);
    }
}
