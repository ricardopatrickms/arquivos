<?php

class model_user extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function userInsert($data)
    {
        $this->db->insert('user', $data);
    }
    public function userLoguin($data)
    {
        $array  = array(
                'user_username' => $data['user_username'],
                'user_password' => $data['user_password'],
            );
        
        $this->db->select('user_id');
        $this->db->where($array);
        $result = $this->db->get('user');
        return $result;
    }
    public function getUser($id, $field)
    {
        $this->db->select($id);
        $this->db->select($field);
        $result = $this->db->get('user');
        return $result;
    }

    public function findAll($field)
    {
        if($field){
            $this->db->select($field);
            $result = $this->db->get('user');
            return $result;
        } else {
            $result = $this->db->get('user');
            return $result;
        }
    }
    public function getFieldById($id,$field)
    {
        $this->db->select($field);
        $this->db->where('user_id',$id);
        $result = $this->db->get('user');
        return $result;   
    }
    
    public function checkFriend($value1, $value2)
    {
        $this->db->where('user_one',$value1);
        $this->db->where('user_two',$value2);
        $this->db->or_where('user_one',$value2);
        $this->db->where('user_two',$value1);
        $result = $this->db->get('friends');
        
        if($result->num_rows() > 0){
            return true;
        } else {
             return false;
        }
    }
    
    public function checkInvite($userId, $myId)
    {
        $this->db->where('from', $userId);
        $this->db->where('to', $myId);
        $result = $this->db->get('frm_req');
        return $result;

    }
    public function listAllFriends($id)
    {
        $this->db->where('user_one', $id);
        $this->db->or_where('user_two', $id);
        $result = $this->db->get('friends');
        return $result;
    }

}
