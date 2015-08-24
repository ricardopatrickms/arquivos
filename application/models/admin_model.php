<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_model
 *
 * @author Ricardo
 */
class admin_model extends CI_Model 
{
    private $tblName;
    public function __construct() 
    {
        parent::__construct();
        $this->tblName = 'tbl_configuracoes';
    }
    
    public function loginAdmin($username = '',$password = '') 
    {
        $passwordMd5 = md5($password);
        $this->db->where('username',$username);
        $this->db->where('password',$passwordMd5);
        $result = $this->db->get($this->tblName);
        
        return $result;
    }
}
