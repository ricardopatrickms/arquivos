<?php if ( ! defined('BASEPATH')) exit('acesso nao permitido'); 


class HomeController extends CI_Controller {
    
    private $messageError;
    
    
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('principal');
        $this->load->library('form_validation');
        $this->load->model('model_user','user');
    }
    
    public function index()
    {
        $this->load->view('home');
    }
    
    public function register()
    {
        $this->messageError = "";
        
        $this->form_validation->
                set_rules('username', 'username', 'required|min_length[1]|max_length[255]');
        $this->form_validation->
                set_rules('password', 'password', 'required|min_length[1]|max_length[255]');
        
        if (!$this->form_validation->run()){
            $this->messageError = 'por favor preencha todos os campos';
        } else {
            if($this->input->post()){
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $data = array(
                        'user_username' => $username,
                        'user_password' => md5($password),
                    );
                $this->user->userInsert($data);
            }
        }
        
        if(!empty($this->messageError)){
            $this->messageError =  '<div class="alert alert-danger" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        '. $this->messageError .'</div>';
        }
        
        $data['message'] = $this->messageError;
        
        $this->load->view('register',$data);
    }
    
    public function loguin()
    {
        $this->messageError = "";
        
        $this->form_validation->
                set_rules('username', 'username', 'required|min_length[1]|max_length[255]');
        $this->form_validation->
                set_rules('password', 'password', 'required|min_length[1]|max_length[255]');
        
        if (!$this->form_validation->run()){
            $this->messageError = 'por favor preencha todos os campos';
        } else {
            if($this->input->post()){
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $data = array(
                        'user_username' => $username,
                        'user_password' => md5($password),
                    );
                $result = $this->user->userLoguin($data);
                if($result->num_rows() > 0){
                    $id = $result->row()->user_id;
                    $this->session->set_userdata('logged', $id);
                    $this->messageError = "loguin sucesso";
                } else {
                    $this->messageError = "loguin falhou";
                }
            }
        }
        
        if(!empty($this->messageError)){
            $this->messageError =  '<div class="alert alert-danger" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        '. $this->messageError .'</div>';
        }
        
        $data['message'] = $this->messageError;
        
        $this->load->view('loguin',$data);
    }
    
    public function members() 
    {
        $rows = $this->user->getUser('user_id','user_username');
        
        if($rows->num_rows() > 0){
            $data['rows'] = $rows->result();
            return $this->load->view('members',$data);
        }
        return $this->load->view('members');
    }
    public function profile($id)
    {
        $data['link'] = null;
        
        $data["id"] = $id;

        $my_id = $this->session->userdata('logged');

        if($my_id != $id){
            $isFriend = $this->user->checkFriend($id,$my_id);
 
            if($isFriend){
                $data['tipo'] = "0";
            } else {
                $invite_from = $this->user->checkInvite($id, $my_id);
                $invite_to   = $this->user->checkInvite($my_id, $id);

                if($invite_from->num_rows() > 0){
                    $data['tipo'] = "1";
                } else if ($invite_to->num_rows() > 0) {
                    $data['tipo'] = "2";
                } else {
                    $data['tipo'] = "3";
                }
            }
        }
        
        $listFriend = $this->user->listAllFriends($id);

        $data["friends"] = $listFriend->result();

        return $this->load->view('profile',$data);
    }
    
    public function aceptInvit($id) {
        $my_id = $this->session->userdata('logged');
        $isFriend = $this->user->checkFriend($id,$my_id);
        if(!$isFriend){
            $data = array(
                "user_one" => $id,
                "user_two" => $this->session->userdata("logged"),
            );
            $this->db->insert('friends', $data);
        }
        redirect("HomeController/members");
    }
    public function cancelInvit($id) {
        $myId = $this->session->userdata("logged");
        
        $this->db->where('from',$myId);
        $this->db->where('to',$id);
        $this->db->or_where('from',$id);
        $this->db->where('to',$myId);
        $this->db->delete('frm_req',$data);

        redirect("HomeController/members");
    }
    public function sendlInvit($id) {
        $data = array(
            "from" => $this->session->userdata("logged"),
            "to" => $id,
        );
        $this->db->insert('frm_req', $data);
        redirect("HomeController/members");
    }
    
    
    
}
