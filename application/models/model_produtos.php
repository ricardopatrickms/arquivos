<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_produtos
 *
 * @author Ricardo
 */
class model_produtos extends CI_Model {
    
    private $tblName = 'produtos';
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    
    public function findAll() 
    {
        $query = $this->db->get($this->tblName);
        return $query;
    }
    
    public function insert($items) 
    {
        foreach ($items as $item){
            $this->db->insert($this->tblName, $item);
        }
    }
    
    public function deleteInGroup($items)
    {
        $this->db->trans_start();
        
        foreach ($items as $item){
            $this->db->where('id_pro',$item->id_pro);
            $this->db->delete($this->tblName);
        }
        
        $this->db->trans_complete();
    }
    
    public function findById($id)
    {
        $this->db->where('id_pro',$id);
        $query = $this->db->get($this->tblName);
        return $query;
    }
}
