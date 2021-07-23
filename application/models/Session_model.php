<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Session_model extends CI_Model
{
    private $table = 'session';

    public function __construct()
    {
        parent::__construct();
    }

    public function save($data = [])
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function delete($id = 0)
    {
        $this->db->delete($this->table, array('id' => $id));
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

    public function get_detail($session_id = null)
    {
        $this->db->select('s.name session_name,s.id session_id,description,start,duration,s.created as session_created,u.name user_name,u.ID as user_id, email');
        $this->db->from('session s');
        $this->db->join('user u', 'u.ID = s.userID', 'left');
        $this->db->where('s.ID', $session_id);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
}
