<?php
function cmp($a, $b)
{
    return strcmp($a->nomeProduto, $b->nomeProduto);
}
class Centros extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->m2n->check_nivel = 'centros';
		$this->load->model('model_centro','centro');
		$this->load->model('model_cadastro', 'cadastro');
		$this->load->model('model_pedidos', 'pedidos');
		$this->load->model('model_produtos', 'produtos');
		$this->load->model('model_estoque', 'estoque');
		$this->load->model('model_inventario', 'inventario');
		$this->load->model('model_menu', 'menu');
		$this->load->model('model_permissoes', 'permissoes');
                $this->load->model('model_logs_adm','logs_adm');
	}
	public function Index()
	{
		redirect('admin/centros/lista');
                
	}
	public function Lista()
	{
            $centros = $this->centro->getListaCompleta();
            $data['centros'] = $centros;
            $data['titulo']  = 'Centros';
            $data['pagina']  = 'centros';
            $data['submenu'] = 'lista';
            $this->load->view('admin/centros/lista',$data);
	}
        
	public function Adicionar()
	{
		$this->form_validation->set_rules('nome','nome','trim|required');
		$this->form_validation->set_rules('tipo','tipo','trim|required');
		$this->form_validation->set_rules('id','ID','trim|required|callback__check_id');
        $this->form_validation->set_rules('cep','CEP','trim|required');
        $this->form_validation->set_rules('endereco','Endereço','trim|required');
        $this->form_validation->set_rules('numero','Número','trim|required');
        $this->form_validation->set_rules('complemento','Complemento','trim');
        $this->form_validation->set_rules('bairro','Bairro','trim|required');
        $this->form_validation->set_rules('cidade','Cidade','trim|required');
        $this->form_validation->set_rules('estado','Estado','trim|required');
        $this->form_validation->set_rules('consignado','Consignado','trim|required');
		$this->form_validation->set_error_delimiters('<span class="help-inline">',"</span>");
		if ($this->form_validation->run())
		{
			$db_centro = array(
					'cen_status'          => '1',
					'cen_data_adicionado' => date('Y-m-d H:i:s'),
					'cen_idtipo'          => $this->input->post('tipo'),
					'cen_nome'            => $this->input->post('nome'),
					'cen_idcadastro'	  => $this->input->post('id'),
					'cen_desconto'	  	  => !is_null($this->input->post('desconto')) ? ($this->input->post('desconto') / 100) : (string)0,
					'cen_endereco'		  => $this->input->post('endereco'),
					'cen_numero'		  => $this->input->post('numero'),
					'cen_complemento'	  => $this->input->post('complemento'),
					'cen_bairro'		  => $this->input->post('bairro'),
					'cen_cep'			  => $this->input->post('cep'),
					'cen_cidade'		  => $this->input->post('cidade'),
					'cen_estado'		  => $this->input->post('estado'),
                                        'cen_consignado'	 => $this->input->post('consignado'),
				);
			$id = $this->centro->insertCentro($db_centro);
                        
                        //INSERE LOG
                        $this->logs_adm->addLog('Centro adicionado ID: ' .  $id , 'adicionarCentroDistribuicao');
                        
			$this->session->set_flashdata('item','<p class="alert alert-info">Centro adicionado com sucesso!</p>');
			redirect('admin/centros/lista');
		}
		else
		{
            $data['estados']  = estados();
			$data['tipos'] = $this->centro->getTipos();
			$data['titulo']  = 'Centros &rsaquo; Adicionar';
			$data['pagina']  = 'centros';
			$data['submenu'] = 'adicionar';
			$this->load->view('admin/centros/adicionar',$data);
		}
	}
	public function pedidos($tipo = '', $status = '', $centro ='', $dataDe='', $dataAte='', $offset = '0')
	{
		$this->load->model('model_pedidos','pedidos');
		$limit   = '50';
		$tipo    = $tipo    == '' ? '-1' : $tipo;
		$status  = $status  == '' ? '-1' : $status;
        $centro  = $centro  == '' ? '-1' : $centro;
		$dataAte = $dataAte == '' ? date('Y-m-d') : $dataAte;
		$dataDe  = $dataDe  == '' ? date('Y-m-d', strtotime(date("Y-m-d").'- 7 days')) : $dataDe;
		$data['dataAte'] = $dataAte;
		$data['dataDe'] = $dataDe;
        $data['centro'] = $centro;
		
		$filtroData = "date(pe_data_adicionado) >= '".date_string($dataDe, 'd/m/Y', 'Y-m-d')."' AND date(pe_data_adicionado) <= '".date_string($dataAte, 'd/m/Y', 'Y-m-d')."'";
		$this->pedidos->setFiltros($filtroData, "");
		$data['status'] = $status;
		$data['tipo'] = $tipo;
		$this->pedidos->setFiltros('pe_idfonte','3');
		if ($status > -1)
		{
			$this->pedidos->setFiltros('pe_status',$status);
		}
		if ($tipo > -1)
		{
			$this->pedidos->setFiltros('tipo',$tipo);
		}
        if ($centro > -1)
        {
            $this->pedidos->setFiltros('pe_idcentro',$centro);
        }
        $centros = $this->centro->getLista();
        $arr    = array('-1' => 'Todos');
        foreach($centros->result() AS $row){
            $arr[$row->id] = $row->nome;
        }
        $data['centros'] = $arr;
		$total_rows = $this->pedidos->getCountPedidoColecao();
		$pagination_display = pagination_display($total_rows,$offset,$limit);
		$pagination_config  = pagination_config($total_rows,$offset,'admin/centros/pedidos/'.$tipo.'/'.$status.'/'.$centro.'/'.$dataDe.'/'.$dataAte,$limit);
		$this->pagination->initialize($pagination_config);
		$pagination_links   = $this->pagination->create_links();
		$data['pedidos']    = $this->pedidos->obtemPedidoColecao($offset);
		$data['total_rows'] = $total_rows;
		$data['offset']     = $offset;
		$data['limit']      = $limit;
		$data['pagination_display'] = $pagination_display;
		$data['pagination_links']   = $pagination_links;
		$data['pagination_config']  = $pagination_config;
		$data['titulo'] = 'Pedidos';
		$data['pagina'] = 'centros';
		$data['submenu'] = 'pedidos';
		$this->load->view('admin/centros/pedidos', $data);
	}
	public function Ver($id_pedido)
	{
		$this->load->model('model_cadastro');
		$this->load->model('model_boleto');
		$this->load->model('model_pagamento','pagamento');
		$this->load->model('model_pedidos','pedidos');
		$pedido = $this->pedidos->obtemPedido($id_pedido);
		$entregas = $this->pedidos->getRastreiosByIdPedido($id_pedido)->result();
		if ( ! is_null($pedido) )
		{
			$data['pedido']          = $pedido;
			$data['pedidoItens']     = $this->pedidos->obtemItensPedido($id_pedido);
			$data['pedidoHistorico'] = $this->pedidos->obtemHistoricoPedido($id_pedido);
			$data['cliente']         = (object)$this->model_cadastro->dados($pedido->pe_idcadastro);
			$data['boleto']          = $this->model_boleto->getBoletoByIdPedido($pedido->pe_id);
			$data['entregas']        = $entregas;
			$data['status']          = $pedido->status;
			$data['pagamentoTipos'] = $this->pagamento->obtemPagamentos();
			$data['titulo'] = 'Pedidos &raquo; Visualização do Pedido: '.zero_esquerda($pedido->pe_id, 6);
			$data['pagina'] = 'centros';
			$data['submenu'] = 'pedidos';
			$this->load->view('admin/centros/ver',$data);
		}
		else
		{
			$redirect = @$_SERVER['HTTP_REFERER'];
			if ( ! empty($redirect))
			{
				redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				redirect('admin/centros/pedidos');
			}
		}
	}
        public function cancelarAdicao($key, $pedido){
            $adds = $this->session->userdata('itensAdicionados');
            unset($adds[$key]);
            $this->session->set_userdata('itensAdicionados', $adds);
            redirect(site_url('admin/centros/editarpedido/' . $pedido));
        }
        
        public function cancelarexclusao($key, $pedido){
            $adds = $this->session->userdata('itensExcluidos');
            unset($adds[$key]);
            $this->session->set_userdata('itensExcluidos', $adds);
            redirect(site_url('admin/centros/editarpedido/' . $pedido));
        }
        
        public function excluirItem($id, $pedido){
            $itensExcluidos = $this->session->userdata('itensExcluidos');
            $itensOriginais = $this->session->userdata('itensOriginais');
            $itensExcluidos[(string)$id] = $itensOriginais[(string)$id];
            $this->session->set_userdata('itensExcluidos', $itensExcluidos);
            redirect(site_url('admin/centros/editarpedido/' . $pedido));
        }
        
	public function EditarPedido($idPedido)
	{
		$this->load->model('model_cadastro');
		$this->load->model('model_boleto');
		$this->load->model('model_pagamento','pagamento');
		$this->load->model('model_pedidos','pedidos');
		$this->load->model('model_centro','centro');
		$this->load->model('model_centro', 'centro');
		$this->load->model('model_pedidos', 'pedidos');
                
        
        
                
                
                $id = $this->session->userdata('pedido_editado');
                if(!$id or $id != $idPedido){
                    $this->session->unset_userdata('itensOriginais');
                    $this->session->unset_userdata('itensEditados');
                    $this->session->unset_userdata('itensAdicionados');
                    $this->session->unset_userdata('itensExcluidos');
                    $this->session->set_userdata('pedido_editado', $idPedido);
                }   
                
		$pedido = $this->pedidos->obtemPedido($idPedido);
		if ( ! is_null($pedido) )
		{
			if ($pedido->pe_idfonte != '3') {
                            $this->session->set_flashdata('item','<p class="alert alert-danger">É possível editar apenas pedidos de reposição!</p>');
                            redirect('admin/centros/ver/'.$idPedido);
			}
			if ($pedido->pe_status != '0') {
                            $this->session->set_flashdata('item','<p class="alert alert-danger">É possível editar apenas pedidos pendentes!</p>');
                            redirect('admin/centros/ver/'.$idPedido);
			}
			/**
			 * Itens Originais do Pedido
			 */
			$itensOriginais = $this->session->userdata('itensOriginais');
			if (!is_array($itensOriginais) OR count($itensOriginais) == 0) {
				$pedidoItens = $this->getItensArray($idPedido);				
				$this->session->set_userdata('itensOriginais', $pedidoItens);
				$itensOriginais = $this->session->userdata('itensOriginais');
			}
                        
			/**
			 * Itens Editados pelo Usuário
			 * (os itens editados são gravados na sessão)
			 */
			$itensEditados = $this->session->userdata('itensEditados');
			$itensAdicionados = $this->session->userdata('itensAdicionados');
                        $itensExcluidos = $this->session->userdata('itensExcluidos');
			
			$data['edicao'] = ($itensEditados && count($itensEditados) > 0) ? TRUE : FALSE;
			$data['pedido'] = $pedido;
			#$desconto = $this->centro->getDescontoCd($pedido->pe_idcentro); // NÃO UTILIZADO
			if ($itensEditados && count($itensEditados) > 0) {
				$data['pedidoItens'] = array_diff_key($itensOriginais, $itensEditados);
			} else {
				$data['pedidoItens'] = $itensOriginais;
			}
                        
                        
			if ($itensExcluidos && count($itensExcluidos) > 0) {
				$data['pedidoItens'] = array_diff_key($data['pedidoItens'], $itensExcluidos);
			}
			$data['itensEditados']  = $itensEditados;
                        $data['itensAdicionados'] = $itensAdicionados;
                        $data['itensExcluidos'] = $itensExcluidos;
                        
                        
                        if(is_array($data['itensEditados'])){
                            usort($data['itensEditados'], "cmp");
                        }
                        
                        if(is_array($data['itensAdicionados'])){
                            usort($data['itensAdicionados'], "cmp");
                        }
                        
                        if(is_array($data['itensExcluidos'])){
                            usort($data['itensExcluidos'], "cmp");
                        }
                        
                        usort($data['pedidoItens'], "cmp");
                        
                        $pedido = $this->pedidos->obtemPedido($idPedido);
                        $data['desconto'] = $this->centro->getDescontoCd($pedido->pe_idcentro);
                        
			$data['cliente'] = (object) $this->model_cadastro->dados($pedido->pe_idcadastro);
			$data['cd'] = (object) $this->centro->getCentroById($pedido->pe_idcentro);
			$data['titulo'] = 'Pedidos &raquo; Edição do Pedido: ' . zero_esquerda($pedido->pe_id, 6);
			$data['pagina'] = 'centros';
			$data['submenu'] = 'pedidos';
			$this->load->view('admin/centros/editar_pedido',$data);
		}
		else
		{
                    $redirect = @$_SERVER['HTTP_REFERER'];
                    if ( ! empty($redirect))
                    {
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                    else
                    {
                        redirect('admin/centros/pedidos');
                    }
                }
                
	}
        public function concluirEdicao($idPedido){
            
            try {
                $itens_editados = $this->session->userdata('itensEditados');
                $itens_adicionados = $this->session->userdata('itensAdicionados');
                $itens_excluidos = $this->session->userdata('itensExcluidos');
                $itens_originais = $this->session->userdata('itensOriginais');
                // Limpa seção
                $this->session->unset_userdata('itensEditados');
                $this->session->unset_userdata('itensAdicionados');
                $this->session->unset_userdata('itensExcluidos');
                $this->session->unset_userdata('itensOriginais');
                $this->db->trans_begin();
                $res = $this->estoque->cancelarReserva($idPedido);
                if(!$res){
                    throw new Exception("Não foi possível cancelar a reserva do pedido $idPedido");
                }
                $res = $this->pedidos->editarPedido($itens_editados, $itens_adicionados, $itens_excluidos, $itens_originais, $idPedido);
                if(!$res){
                    throw new Exception("Não foi possível efetuar a edição do pedido $idPedido");
                }
                $res = $this->estoque->AdicionaReserva(1, $idPedido, TRUE);
                if(!$res){
                    throw new Exception("Não foi possível adicionar a reserva para o pedido $idPedido");
                }
                if($this->db->trans_status() === FALSE){
                    redirect(site_url('admin/centros/editarpedido/' . $idPedido));
                }
            }
            
            catch(Exception $e){
                $this->db->trans_rollback();
                $this->message->set($e->getMessage(), 'error');
                redirect(site_url('admin/centros/editarpedido/' . $idPedido));
            }
            $this->db->trans_commit();
            redirect(site_url('admin/centros/ver/' . $idPedido));   
        }
        
	public function confirmaredicao($idPedido){
		$this->load->model('model_cadastro');
		$this->load->model('model_boleto');
		$this->load->model('model_pagamento','pagamento');
		$this->load->model('model_pedidos','pedidos');
		$this->load->model('model_centro','centro');
		$this->load->model('model_centro', 'centro');
		$this->load->model('model_pedidos', 'pedidos');
                
                
		$pedido = $this->pedidos->obtemPedido($idPedido);
		if ( ! is_null($pedido) )
		{
			if ($pedido->pe_idfonte != '3') {
                            $this->session->set_flashdata('item','<p class="alert alert-danger">É possível editar apenas pedidos de reposição!</p>');
                            redirect('admin/centros/ver/'.$idPedido);
			}
			if ($pedido->pe_status != '0') {
                            $this->session->set_flashdata('item','<p class="alert alert-danger">É possível editar apenas pedidos pendentes!</p>');
                            redirect('admin/centros/ver/'.$idPedido);
			}
			/**
			 * Itens Originais do Pedido
			 */
			$itensOriginais = $this->session->userdata('itensOriginais');
                        
			if (!is_array($itensOriginais) OR count($itensOriginais) == 0) {
				$pedidoItens = $this->getItensArray($idPedido);				
				$this->session->set_userdata('itensOriginais', $pedidoItens);
				$itensOriginais = $this->session->userdata('itensOriginais');
			}
			/**
			 * Itens Editados pelo Usuário
			 * (os itens editados são gravados na sessão)
			 */
			$itensEditados =    $this->session->userdata('itensEditados');
			$itensAdicionados = $this->session->userdata('itensAdicionados');
			$itensExcluidos =   $this->session->userdata('itensExcluidos');
			
			$data['edicao'] = ($itensEditados && count($itensEditados) > 0) ? TRUE : FALSE;
			$data['pedido'] = $pedido;
			#$desconto = $this->centro->getDescontoCd($pedido->pe_idcentro); // NÃO UTILIZADO
                        
			if ($itensEditados && count($itensEditados) > 0) {
                            $array = array_diff_key($itensOriginais, $itensEditados);
			} else {
                            $array = $itensOriginais;
			}
                        
                        if(is_array($itensExcluidos) and count($itensExcluidos) > 0){ 
                            $array = array_diff_key($array, $itensExcluidos);
                        }
                        
                        if($itensEditados and count($itensEditados) > 0){
                            $array = array_merge($array, $itensEditados);
                        }
                        
                        if(is_array($itensAdicionados) and count($itensAdicionados)){
                            $array = array_merge($array, $itensAdicionados);
                        }
                        
                        $data["itens"] = $array;
                        usort($data['itens'], "cmp");
                        
                        
                        $pedido = $this->pedidos->obtemPedido($idPedido);
                        $data['desconto'] = $this->centro->getDescontoCd($pedido->pe_idcentro);
                        
			$data['cliente'] = (object) $this->model_cadastro->dados($pedido->pe_idcadastro);
			$data['cd'] = (object) $this->centro->getCentroById($pedido->pe_idcentro);
			$data['titulo'] = 'Pedidos &raquo; Confirmar dados do Pedido: ' . zero_esquerda($pedido->pe_id, 6);
			$data['pagina'] = 'centros';
			$data['submenu'] = 'pedidos';
			$this->load->view('admin/centros/confirmar_edicao',$data);
		}
	}
	public function FiltroLista()
	{
		$post = $this->input->post();
		if (empty($post))
		{
			$dataAte = date('Y-m-d');
			$dataDe = date('Y-m-d', strtotime(date("Y-m-d").'- 7 days'));
			redirect('admin/centros/pedidos/-1/-1/'.$dataDe.'/'.$dataAte.'/0');
		}
		else
		{
			$tipo = isset($post['tipo']) ? $post['tipo'] : '-1';
			$status = $post['status'];
			$dataDe = $post['dataDe'];
			$dataAte = $post['dataAte'];
            $centro = $post['centro'];
			$dataDe = date_string($dataDe, 'd/m/Y', 'Y-m-d');
			$dataAte = date_string($dataAte, 'd/m/Y', 'Y-m-d');
			redirect('admin/centros/pedidos/'.$tipo.'/'.$status.'/'.$centro.'/'.$dataDe.'/'.$dataAte);
		};
	}
	public function aprovar($idPedido)
	{
		$pedido = $this->pedidos->obtemPedidoById($idPedido);
		if ($pedido->pe_idfonte <> '3') {
			$this->session->set_flashdata('item','<p class="alert alert-error">ERRO: O pedido #'.zero_esquerda($idPedido, 6).' não pôde ser aprovado!</p>');
		    redirect('admin/centros/ver/'.$idPedido);
		}
		$this->db->trans_begin();
	 	#$this->estoque->AdicionaReserva($pedido->pe_idcentro, $pedido->pe_id,true);
	 	$this->pedidos->addStatus($idPedido, '3');
	 	$this->pedidos->addStatus($idPedido, '4');
    	$this->pedidos->aprovarPedido($idPedido);
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
			$this->session->set_flashdata('item','<p class="alert alert-error">ERRO: O pedido #'.zero_esquerda($idPedido, 6).' não pôde ser aprovado!</p>');
		    redirect('admin/centros/ver/'.$idPedido);
		}
		else
		{
		    $this->db->trans_commit();
			$this->session->set_flashdata('item','<p class="alert alert-info">O pedido #'.zero_esquerda($idPedido, 6).' foi aprovado com sucesso!</p>');
		    redirect('admin/centros/ver/'.$idPedido);
		}
	}
	public function cancelar($idPedido)
	{
            $this->db->trans_begin();
            if(!$this->pedidos->cancelar($idPedido, '8')) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('item','<p class="alert alert-error">ERRO: O pedido #'.zero_esquerda($idPedido, 6).' não pôde ser cancelado! Não foi possível cancelar a(s) reserva(s).</p>');
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('item','<p class="alert alert-error">ERRO: O pedido #'.zero_esquerda($idPedido, 6).' não pôde ser cancelado!</p>');
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('item','<p class="alert alert-info">O pedido #'.zero_esquerda($idPedido, 6).' foi cancelado com sucesso!</p>');
            }
            redirect('admin/centros/ver/'.$idPedido);
	}
	public function entregar($idPedido)
	{
		$pedido = $this->pedidos->obtemPedidoById($idPedido);
		if ($pedido->pe_idfonte <> '3') {
			$this->session->set_flashdata('item','<p class="alert alert-error">ERRO: O pedido #'.zero_esquerda($idPedido, 6).' não pôde ser entregue!</p>');
		    redirect('admin/centros/ver/'.$idPedido);
		}
		$this->db->trans_begin();
		$this->estoque->reporByIdPedido($idPedido);
		$this->estoque->baixarReservaByIdPedido($idPedido);
	 	$this->pedidos->addStatus($idPedido, '5');
	 	$this->pedidos->addStatus($idPedido, '6');
    	#$this->pedidos->aprovarPedido($idPedido);
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
			$this->session->set_flashdata('item','<p class="alert alert-error">ERRO: O pedido #'.zero_esquerda($idPedido, 6).' não pôde ser entregue!</p>');
		    redirect('admin/centros/ver/'.$idPedido);
		}
		else
		{
		    $this->db->trans_commit();
			$this->session->set_flashdata('item','<p class="alert alert-info">O pedido #'.zero_esquerda($idPedido, 6).' foi entregue com sucesso!</p>');
		    redirect('admin/centros/ver/'.$idPedido);
		}
	}
	public function Editar($id)
	{
		$this->form_validation->set_rules('nome','nome','trim|required');
		$this->form_validation->set_rules('tipo','tipo','trim|required');
		$this->form_validation->set_rules('id','ID','trim|required|callback__check_id');
        $this->form_validation->set_rules('cep','CEP','trim|required');
        $this->form_validation->set_rules('endereco','Endereço','trim|required');
        $this->form_validation->set_rules('numero','Número','trim|required');
        $this->form_validation->set_rules('complemento','Complemento','trim');
        $this->form_validation->set_rules('bairro','Bairro','trim|required');
        $this->form_validation->set_rules('cidade','Cidade','trim|required');
        $this->form_validation->set_rules('estado','Estado','trim|required');
		$this->form_validation->set_error_delimiters('<span class="help-inline">',"</span>");
		if ($this->form_validation->run())
		{
			$db_centro = array(
					'cen_idtipo' 		=> $this->input->post('tipo'),
					'cen_nome'   		=> $this->input->post('nome'),
					'cen_idcadastro' 	=> $this->input->post('id'),
					'cen_desconto'	  	=> !is_null($this->input->post('desconto')) ? $this->input->post('desconto') / 100 : (string)0,
					'cen_endereco'		=> $this->input->post('endereco'),
					'cen_numero'		=> $this->input->post('numero'),
					'cen_complemento'	=> $this->input->post('complemento'),
					'cen_bairro'		=> $this->input->post('bairro'),
					'cen_cep'			=> $this->input->post('cep'),
					'cen_cidade'		=> $this->input->post('cidade'),
					'cen_estado'		=> $this->input->post('estado'),
				);
			$this->centro->updateCentro($id, $db_centro);
                        
                        //INSERE LOG
                        $this->logs_adm->addLog('Centro editado ID: ' .  $id , 'editarCentroDistribuicao');
                        
			$this->session->set_flashdata('item','<p class="alert alert-info">Centro editado com sucesso!</p>');
			redirect('admin/centros/lista');
		}
		else
		{
			$this->db->select('
				cen_idtipo AS tipo, 
				cen_nome AS nome, 
				cen_idcadastro AS idcadastro,
				cen_desconto * 100 AS desconto,
				cen_endereco AS endereco,
				cen_numero AS numero,
				cen_complemento AS complemento,
				cen_bairro AS bairro, 
				cen_cep AS cep,
				cen_cidade AS cidade,
				cen_estado AS estado,
				cen_consignado AS consignado');
			$this->db->where('cen_id',$id);
			$centro = $this->db->get('centros');
			if ($centro->num_rows()>0)
			{
				$data['idCentro'] = $id;
            	$data['estados']  = estados();
				$data['nome'] = $centro->row()->nome;
				$data['tipo'] = $centro->row()->tipo;
				$data['id'] = $centro->row()->idcadastro;
				$data['desconto'] = $centro->row()->desconto;
				$data['endereco'] = $centro->row()->endereco;
				$data['numero'] = $centro->row()->numero;
				$data['complemento'] = $centro->row()->complemento;
				$data['bairro'] = $centro->row()->bairro;
				$data['cep'] = $centro->row()->cep;
				$data['cidade'] = $centro->row()->cidade;
				$data['estado'] = $centro->row()->estado;
				$data['consignado'] = $centro->row()->consignado;
				$data['tipos'] = $this->centro->getTipos();
				$data['titulo']  = 'Centros &rsaquo; Editar';
				$data['pagina']  = 'centros';
				$data['submenu'] = '';
				$this->load->view('admin/centros/editar',$data);
			}
			else
			{
				redirect('admin/centros/lista');
			}
		}
	}
	public function Excluir($id)
	{
		$this->db->select('cen_id');
		$this->db->where('cen_id',$id);
		$centro = $this->db->get('centros');
		if ($centro->num_rows()>0)
		{
			$db_centro = array(
					'cen_status' => '2',
					'cen_data_excluido' => date('Y-m-d H:i:s'),
				);
			$this->db->where('cen_id',$id);
			$this->db->update('centros',$db_centro);
                        
                        //INSERE LOG
                        $this->logs_adm->addLog('Centro excluido ID: ' .  $id , 'excluirCentroDistribuicao');
                        
			$this->session->set_flashdata('item','<p class="alert alert-info">Centro excluído com sucesso!</p>');
			redirect('admin/centros/lista');
		}
		else
		{
			redirect('admin/centros/lista');
		}
	}
	public function _check_id($id)
	{
		$isValid = $this->cadastro->checkId($id);
		if (!$isValid) {
            $this->form_validation->set_message('_check_id', 'ID Inválido!');
            //$this->session->set_flashdata('_check_id','<p class="alert alert-danger">ID Inválido!</p>');
		}
		return $isValid;
	}
	public function listaInventarios($idCentro)
	{
		$data['cd'] = $this->centro->getCentros($idCentro)->row();
        $inventarioModel = 	new Model_Inventario();
        $data['idCentro'] = $idCentro;
		$data['inventarios'] = $inventarioModel->getInventarios($idCentro);
		$data['titulo']  = 'Centros &rsaquo; Editar  &rsaquo; Inventários';
		$data['pagina']  = 'centros';
		$data['submenu'] = 'lista';
		$this->load->view('admin/inventario/lista', $data);
	}
	public function movimentacaoEstoque($idCentro)
	{        
		$dataDe = $this->session->userdata('inicial');
        $dataAte = $this->session->userdata('final');
        if (!isset($dataDe) OR !isset($dataAte)) {
			redirect('admin/centros/filtroMovimentacaoEstoque/'.$idCentro);
        }
        $dataDe  = !$dataDe ? date('Y-m-d', strtotime("Now -7 Days")) : $dataDe;
        $dataAte = !$dataAte ? date('Y-m-d') : $dataAte;
        $data['dataDe']  = $dataDe;
        $data['dataAte'] = $dataAte;        
		$data['cd'] = $this->centro->getCentros($idCentro)->row();
        $estoqueModel = 	new Model_Estoque();
        $data['idCentro'] = $idCentro;
		$data['estoque'] = $estoqueModel->getMovimentacaoEstoquePorProduto($idCentro, $dataDe, $dataAte);
		// var_dump($data['estoque']->result());die;
		
		$data['titulo']  = 'Centros &rsaquo; Editar  &rsaquo; Movimentação de Estoque';
		$data['pagina']  = 'centros';
		$data['submenu'] = 'lista';
		$this->load->view('admin/estoque/movimentacao', $data);
	}
	public function filtroMovimentacaoEstoque($idCentro)
	{
		$dataDe = $this->input->post('dataDe');
        $dataAte = $this->input->post('dataAte');
        $dataDe = date_string($dataDe, 'd/m/Y', 'Y-m-d');
        $dataAte = date_string($dataAte, 'd/m/Y', 'Y-m-d');
        $this->session->set_userdata('inicial', $dataDe);
        $this->session->set_userdata('final', $dataAte);
		#$produto = $this->input->post('produto');
		redirect('admin/centros/movimentacaoEstoque/'.$idCentro);
	}
	public function cancelarInventario($idInventario)
	{	
		$inventarioModel = 	new Model_Inventario();
        $permissoesModel = 	new Model_Permissoes();
		$id 		= $permissoesModel->SID_Admin();
		$inventario = $inventarioModel->getById($idInventario);
		if (!is_null($inventario)) {
			$this->db->trans_begin();
			$inventarioModel->excluirInventario($idInventario, $id);
			if ($this->db->trans_status() === FALSE)
			{
			    $this->db->trans_rollback();
				redirect('admin/centros/visualizarInventario/'.$idInventario);
			}
			else
			{
			    $this->db->trans_commit();
				redirect('admin/centros/listaInventarios/'.$inventario->inv_idcentro);
			}
		}
	}
	public function aprovarInventario($idInventario)
	{
		$inventarioModel = 	new Model_Inventario();
        $permissoesModel = 	new Model_Permissoes();
		$id 		= $permissoesModel->SID_Admin();
		$inventario = $inventarioModel->getById($idInventario);
		if (!is_null($inventario)) {
			$this->db->trans_begin();
			$inventarioModel->aprovarInventario($idInventario, $id);
			if ($this->db->trans_status() === FALSE)
			{
			    $this->db->trans_rollback();
				redirect('admin/centros/visualizarInventario/'.$idInventario);
			}
			else
			{
			    $this->db->trans_commit();
				redirect('admin/centros/listaInventarios/'.$inventario->inv_idcentro);
			}
		}
	}
	public function visualizarInventario($idInventario)
	{
        $menu = 			new Model_Menu();
        $permissoesModel = 	new Model_Permissoes();
        $estoqueModel = 	new Model_Estoque();
        $inventarioModel = 	new Model_Inventario();
		$this->form_validation->set_rules('produtos','produtos','');
		$this->form_validation->set_error_delimiters('','');
		$id = 			$permissoesModel->SID_Admin();
		#$estoque = 		$estoqueModel->getEstoqueOrder('267');
		$inventario = 	$inventarioModel->getById($idInventario);
		$itens = 		$inventarioModel->getInventarioEstoque($idInventario);
		$data['cd'] = $this->centro->getCentros($inventario->inv_idcentro)->row();
		#$data['produtos'] 	= $produtoArr;		
		$data['inventario'] = $inventario;
		$data['itens'] 		= $itens;
		$data['titulo']  = 'Centros &rsaquo; Editar  &rsaquo; Inventários  &rsaquo; Adicionar Inventário';
		$data['pagina']  = 'inventario';
		$data['submenu'] = 'editar';
		$this->load->view('admin/inventario/visualizar', $data);
		#var_dump($inventario, $itens->result());die;
	}
	public function AdicionarInventario($idCentro)
	{
		$filial = $this->centro->getCentros($idCentro);
		if ($filial->num_rows()>0)
		{
    		$inventarioModel = 	new Model_Inventario();
    		$estoqueModel = 	new Model_Estoque();
        	$permissoesModel = 	new Model_Permissoes();
			$idUsuario	= $permissoesModel->SID_Admin();
			$estoque 	= $estoqueModel->getEstoqueOrder($idCentro);
			$this->form_validation->set_rules('produtos','produtos','');
			$this->form_validation->set_rules('descricao','descricao','trim|required');
			$this->form_validation->set_rules('motivo','motivo','trim|required');
			$this->form_validation->set_error_delimiters('','');
			if ($this->form_validation->run())
			{
        		$produtos = $this->input->post('produtos');
        		$descricao = $this->input->post('descricao');
        		$motivo = $this->input->post('motivo');
				foreach ($estoque->result() AS $estoqueItem)
				{
					$estoqueItens[] = array(
						'invest_idproduto'			=> $estoqueItem->id_produto,
						'invest_quantidade_virtual'	=> (int)$estoqueItem->quantidade,
						'invest_quantidade_real'	=> (int)$produtos[$estoqueItem->id_produto],
						'invest_quantidade_ajuste'	=> (int)$produtos[$estoqueItem->id_produto] - (int)$estoqueItem->quantidade,
					);
				}
				
				$arr = array(
						'inv_idcentro' 				=> $idCentro,
						'inv_status' 				=> '0',
						'inv_data' 					=> date('Y-m-d H:i:s'),
						'inv_usuario_solicitacao'	=> $idUsuario,
						'inv_descricao' 			=> $descricao,
						'inv_motivo' 				=> $motivo,
					);
				$this->db->trans_begin();
				$idInventario = $inventarioModel->gerarInventario($idCentro, $arr, $estoqueItens);
				if ($this->db->trans_status() === FALSE)
				{
				    $this->db->trans_rollback();
				}
				else
				{
				    $this->db->trans_commit();
				}
				redirect('admin/centros/visualizarInventario/'.$idInventario);
			}
			else
			{
				$data['idFilial'] = $idCentro;
				$data['filial'] = $filial->row()->nome;
				$data['estoque'] = $estoque;
				$data['titulo'] = 'Inventário &rsaquo; Adicionar';
				$data['pagina'] = 'centros';
				$data['submenu'] = 'lista';
				$this->load->view('admin/inventario/adicionar',$data);
			}
		}
		else
		{
			redirect('admin/estoque');
		}
	}
	public function getProdutoItem($idItem) {
		$this->load->model('model_pedidos', 'pedidos');
		$this->load->model('model_centro', 'centro');
		$item = $this->pedidos->getItemPedido($idItem);
		$desconto = $this->centro->getDescontoCd($item->pe_idcentro);
                
                $estoque = $this->estoque->getStatus(1, $item->item_idproduto);
                
		$result = array(
			'idItem'        		=> $item->item_id
			,'idPedido' 			=> $item->item_idpedido
			,'idCentro' 			=> $item->pe_idcentro
			,'idProduto' 			=> zero_esquerda($item->item_idproduto, 5)
			,'nomeProduto' 			=> strtoupper($item->item_nome)
			,'quantidade' 			=> $item->item_quantidade
			,'valorUnitario' 		=> $item->item_preco
			,'subtotal' 			=> $item->item_subtotal
			,'subtotalFormat' 		=> number_format($item->item_subtotal, 2, ',', '.')
			,'valorUnitarioFormat'          => number_format($item->item_preco, 2, ',', '.')
			,'desconto' 			=> $desconto
                        ,'estoqueDisponivel'    	=> $estoque
		);
		echo json_encode($result);
	}
	public function editarItemPedido($idPedido, $action){
		$itensEditados    = $this->session->userdata('itensEditados');
                $itensAdicionados = $this->session->userdata('itensAdicionados');
		$idItem 		= $this->input->post('idItem');
		$idProduto 		= $this->input->post('idProduto');
		$nomeProduto            = $this->input->post('nomeProduto');
		$valorUnitario          = $this->input->post('valorUnitario');
		$quantidade             = $this->input->post('quantidade');
		$subtotal 		= $this->input->post('subtotal');
		$subtotalFormat = number_format($subtotal, 2, ',', '.');
		$valorUnitarioFormat = number_format($valorUnitario, 2, ',', '.');
		$newItem = array(
			'idItem' 			=> $idItem
			,'idPedido' 			=> $idPedido
			,'idCentro' 			=> ''
			,'idProduto' 			=> $idProduto
			,'nomeProduto' 			=> $nomeProduto
			,'quantidade' 			=> $quantidade
			,'valorUnitario'		=> $valorUnitario
			,'subtotal' 			=> $subtotal
			,'subtotalFormat' 		=> $subtotalFormat
			,'valorUnitarioFormat' 	=> $valorUnitarioFormat
			,'action'				=> $action
		);
                
                if($action == 'EDITAR'){
                    $itensEditados[$idItem] = (object)$newItem;
                }else{
                    $itensAdicionados[] = (object)$newItem;
                }
                
		$this->session->set_userdata('itensEditados', $itensEditados);
		$this->session->set_userdata('itensAdicionados', $itensAdicionados);
		// $this->pedidos->updateItem($idItem, $quantidade, $subtotal);
		$this->session->set_flashdata('item','<p class="alert alert-success">Item ' .$idProduto. ' editado com sucesso!</p>');
		redirect('admin/centros/EditarPedido/'.$idPedido);
	}
        
	public function cancelaEdicaoItemPedido($idItem, $idPedido)
	{
		$itensEditados = $this->session->userdata('itensEditados');
		if (is_array($itensEditados) && count($itensEditados) && array_key_exists($idItem, $itensEditados)) {
			unset($itensEditados[$idItem]);
			$this->session->set_userdata('itensEditados', $itensEditados);
		}
		redirect('admin/centros/EditarPedido/'.$idPedido);
	}
	public function getItensArray($idPedido)
	{
		$pedido = $this->pedidos->obtemPedido($idPedido);
		$itens = $this->pedidos->obtemItensPedido($idPedido);
		$desconto = $this->centro->getDescontoCd($pedido->pe_idcentro);
		$pedidoItens = array();
		foreach ($itens as $key => $item) {
			$itemArray = array(
				'idItem' 				=> $item->item_id
				,'idPedido' 			=> $item->item_idpedido
				,'idProduto' 			=> zero_esquerda($item->item_idproduto, 5)
				,'nomeProduto' 			=> strtoupper($item->item_nome)
				,'quantidade' 			=> $item->item_quantidade
				,'idCentro' 			=> $pedido->pe_idcentro
				,'subtotalFormat' 		=> number_format($item->item_subtotal, 2, ',', '.')
				,'valorUnitarioFormat' 	=> number_format($item->item_preco, 2, ',', '.')
				,'subtotal' 			=> $item->item_subtotal
				,'valorUnitario' 		=> $item->item_preco
				,'desconto' 			=> $desconto
			);
			$pedidoItens[$item->item_id] = (object)$itemArray;
		}
		return $pedidoItens;
	}
        public function buscarProdutos(){
            
            $itens = $this->session->userdata('itensOriginais');
            if(count($itens) > 0){
                $where = " AND pro_id NOT IN (";
                $f = true;
                foreach($itens as $id => $item){
                    if($f){
                        $where .= $item->idProduto;
                    }else{
                        $where .= ', ' . $item->idProduto;
                    }
                    $f = false;
                }
                $where .= ")";
            }else{
                $where = '';
            }
                        
            $q = $this->input->get_post('q');
            $page = $this->input->get_post('page');
                        
            $produtos = $this->db->query("
                SELECT
                    pro_id    AS id_produto,
                    pro_sku   AS sku,
                    pro_nome  AS nome,
                    pro_preco_venda AS preco,
                    sum(coalesce(es_quantidade, 0)) AS quantidade 
                FROM      m2n_loja_produtos
                LEFT JOIN m2n_loja_produtos_estoque
                    ON      pro_id = es_idproduto 
                    AND     es_idcentro = '1'                    
                WHERE     pro_estoque = '1' 
                    $where
                    AND   ( pro_nome LIKE '%$q%' OR pro_id = '$q') 
                GROUP BY  pro_id
            ");
            
            $first = TRUE;
                        
            echo '[';
            foreach($produtos->result() as $prod){
                #$retorno[$prod->id_produto] = zero_esquerda($prod->id_produto, 6) . " - " . $prod->nome;
                $estoque = $this->estoque->getStatus(1, $prod->id_produto);
                
                $qtde = $estoque;
                
                if($qtde > 0){
                    if(!$first) echo ", ";
                    echo '{"id" : "'.$prod->id_produto.'", "text" : "'. zero_esquerda($prod->id_produto, 6) . " - " . $prod->nome .'"}';
                    $first = FALSE;
                    //echo "<option value='".$prod->id_produto."'>". $prod->nome ."</option>";
                }
            }
            
            
            echo ']';
            #echo json_encode($retorno);            
        }
        
        public function getProduto($id){
            $produto = $this->produtos->consultarProduto($id);
            $estoque = $this->estoque->getStatus(1, $produto->pro_id); 
            $prod = array(
                "id" => $produto->pro_id,
                "valor" => $produto->pro_preco_venda,
                "nome" => $produto->pro_nome
            );
            $prod["estoque"] = $estoque;
            echo json_encode($prod);
        }
        // public function mesclarItensParaEdicao($idPedido)
	// {
	// 	$itens = getItensArray($idPedido);
	// 	$itensEditados = $this->session->userdata('itensEditados');
	// 	foreach ($itensEditados as $key => $item) {
	// 		if (isset($itens[$item->idItem]) {
	// 			switch ($newItem->action) {
	// 				case 'A':
	// 					$itens[$item->idItem] = $item;
	// 					break;
	// 				case 'D':
	// 					unset($itens[$item->idItem]);
	// 					break;
	// 				case 'E':
	// 					$itens[$item->idItem]-> = $item;
	// 					break;					
	// 			}
	// 		}
	// 	}
		
	// }
        
        public function Imprimir($id_pedido='')
	{
		$this->load->model('model_cadastro');
		$this->load->model('model_boleto');
		if (empty($id_pedido))
		{
			$pedidos = explode(',',$this->input->post('pedidos'));
			foreach ($pedidos AS $id_pedido)
			{
				$data['pedidos'][] = $this->getPedidoCompleto($id_pedido, $this->m2n->centro());
			}
		}
		else
		{
			$data['pedidos'][] = $this->getPedidoCompleto($id_pedido);
		}
                
		if (isset($data['pedidos']))
		{
			$data['titulo']  = 'Pedidos &rsaquo; Visualização do Pedido';
			$data['pagina']  = 'pedidos';
			$data['submenu'] = 'lista';
			$this->load->view('admin/centros/imprimir', $data);
		}
		else
		{
			$redirect = @$_SERVER['HTTP_REFERER'];
			if ( ! empty($redirect))
			{
				redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				redirect('admin/loja');
			}
		}
	}
        
        
	public function getPedidoCompleto($id_pedido=NULL, $idCentro=NULL)
	{
		if (is_null($id_pedido))
		{
			return null;
		}
		$pedido = $this->pedidos->obtemPedido($id_pedido,$idCentro);
		if ( ! is_null($pedido))
		{
			$data['pedido']            = $pedido;
			$data['pedidoItens']       = $this->pedidos->obtemItensPedido($id_pedido);
			$data['pedidoHistorico']   = $this->pedidos->obtemHistoricoPedido($id_pedido);
			$data['pedidoComentarios'] = $this->pedidos->obtemComentariosPedido($id_pedido);
			$data['pedidoPagamento']   = $this->pedidos->obtemPagamento($id_pedido);
			$data['statusEntrega']     = $this->pedidos->obtemStatusEntrega($id_pedido);
			$data['cliente']           = (object)$this->cadastro->dados($pedido->pe_idcadastro);
			$data['boleto']            = $this->model_boleto->getBoletoByIdPedido($pedido->pe_id);
			$data['boleto']            = '';
			return $data;
		}
		return null;
	}
}