<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of home
 *
 * @author Ricardo
 */
class home extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->load->view('admin/index');
    }
    
    public function login() 
    {
        $this->load->model('admin_model','admin');
        $this->form_validation->set_rules('admin_username', 'Admin Username', 'required|trim|xss_clean|strip_tags');
        $this->form_validation->set_rules('admin_password', 'Admin Password', 'required|trim|xss_clean|strip_tags');
        $username = $this->input->post('admin_username');
        $password = $this->input->post('admin_password');

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('admin/index');
        }
        else
        {
            $query = $this->admin->loginAdmin($username,$password);
            if($query->num_rows() > 0)
            {
                $admin_id = $query->row()->username;
                $admin_password = $query->row()->password;
                $this->session->set_userdata('admin_username',$admin_id);
                $this->session->set_userdata('admin_password',$admin_password);
                $this->load->view('admin/welcome');
            } else 
            {
                die('nao é admin');
            }        
        }
    }
}
