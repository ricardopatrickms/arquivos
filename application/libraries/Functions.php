<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Functions {
    
    private $CI;
    
    public function __construct() {
        $this->CI =& get_instance();
    }
    
    public function logedIn(){
        if ($this->CI->session->userdata('userID') !== FALSE) {
            return true;
        }
        
        return false;
    }
}
