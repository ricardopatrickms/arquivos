<?php

class Principal extends CI_Model {
    
    public function __construct() {}
    
    public function logedIn()
    {
        if($this->session->userdata('logged') != FALSE) {
            $this->session->set_userdata('logged',true);
        } else {
            $this->session->set_userdata('logged',false);
        }
    }
}
