<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $table = 'user';

    public function __construct()
    {
        parent::__construct();
    }

    public function save($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->update($this->table, $data, array('id'=>$id));
        return $id;
    }

    public function get_by_condition($where)
    {
        $query = $this->db->get_where($this->table, $where);
        return $query->row_array(); // return single result
    }
}
