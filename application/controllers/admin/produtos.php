<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of produtos
 *
 * @author Ricardo
 */
class produtos extends CI_Controller 
{
    
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('model_produtos','produtos');
        $this->load->library('NativeSession','nativesession');
    }
    
    public function index($status = '')
    {
        
        if(!$status)
        {
            $this->lista();
        }
        if($this->nativesession->get('itemDel') == new stdClass())
        {
            $this->nativesession->set('itemDel');
        }
        if($this->nativesession->get('itemAcidionado') == new stdClass())
        {
            $this->nativesession->set('itemAcidionado');
        }
        $itemEditado    = $this->nativesession->get('itemEditados');
        $itemDeletado   = $this->nativesession->get('itemDel');
        $itemAdicionado = $this->nativesession->get('itemAcidionado');
        
        $data['produtos']    = $itemEditado;
        $data['itemAdd']     = $itemAdicionado;
        $data['produtosDel'] = $itemDeletado;
        
        
        
        $this->load->view('admin/produtos/index',$data); 
    }

    private function lista()
    {
        $query = $this->produtos->findAll();
        
        if($query->num_rows() > 0)
        {
            $result =  $query->result();
            $this->nativesession->set('itemEditados',$result);
        }
    }
    public function cadastrar()
    {
        $post = $this->input->post();
        $this->form_validation->set_rules('nomeProduto', 'nomeProduto', 'required');
        $this->form_validation->set_rules('valorProduto', 'valorProduto', 'required');
        
        if($this->form_validation->run() != FALSE)
        {
            $novoIndice = (object) array(
                'name_pro'  => $post['nomeProduto'],
                'valor_pro' => $post['valorProduto'],
            );
            $db = $this->nativesession->get('itemAcidionado');
            $db[] = $novoIndice;
            $this->nativesession->set('itemAcidionado', $db);
        }
        
        redirect('admin/produtos/index/1');
    }
    
    public function deletar($id) 
    {
        $itemEditado = $this->nativesession->get('itemEditados');
        $itemExluido = $this->nativesession->get('itemDel');
         
        foreach ($itemEditado as $elementKey => $element) {
            foreach ($element as $valuekey => $value) {
                if($valuekey == 'id_pro' and $value == $id){
                    $itemExluido[] = $element;
                    unset($itemEditado[$elementKey]);
                }   
            }
        }
        usort($itemEditado, array($this,'cmp'));
        $itemEditado = $this->nativesession->set('itemEditados',$itemEditado);
        $itemExluido = $this->nativesession->set('itemDel', $itemExluido);
        redirect('admin/produtos/index/1');
    }
    
    public function cancelAdd($key) {
        $items   = $this->nativesession->get('itemAcidionado');
        unset($items[(string)$key]);
        $this->nativesession->set('itemAcidionado',$items);
        redirect('admin/produtos/index/1');
    }
    public function cmp($a, $b)
    {
        return strcmp($a->id_pro, $b->id_pro);
    }
    public function cancelDelete($key) 
    {
        $items   = $this->nativesession->get('itemEditados');
        $itemDel = $this->nativesession->get('itemDel');
        $items[]   = $itemDel[$key];
        
        unset($itemDel[$key]);
        usort($items, array($this, "cmp"));
        
        $this->nativesession->set('itemEditados', $items);
        $this->nativesession->set('itemDel', $itemDel);
        redirect('admin/produtos/index/1');
    }
    

    public function executeAcao() {
        $items = $this->nativesession->get('itemDel');
        $itemsAdd = $this->nativesession->get('itemAcidionado');
        
        /*
         * destroi sessões
         */
        $this->nativesession->delete('itemDel');
        $this->nativesession->delete('itemAcidionado');
        
        if(count($items) > 0)
        {
            $this->produtos->deleteInGroup($items);
        }
        if(count($itemsAdd) > 0)
        {
            $this->produtos->insert($itemsAdd);
        }
        
        redirect('admin/produtos/index');
    }
    
}
