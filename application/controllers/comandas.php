<?php

class Comandas extends CI_Controller {
    

    /**
     * author: Ramon Silva 
     * email: silva018-mg@yahoo.com.br
     * 
     */
    
    function __construct() {
        parent::__construct();
        
        if((!$this->session->userdata('session_id')) || (!$this->session->userdata('logado'))) {
            redirect('mapos/login');
        }
		
		$this->load->helper(array('form','codegen_helper'));
		$this->load->model('vendas_model','',TRUE);
        $this->load->model('clientes_model','',TRUE);
        $this->data['menuComandas'] = 'Comandas';
	}	
	
	function index(){
        $this->where='faturado = 0';
        $this->faturado = 0;
        $this->base_url='gerenciar';

		$this->gerenciar();
	}

    function fechadas($var=0){
        $this->where='faturado = 1';
        $this->faturado = 1;
        $this->base_url='fechadas';
		$this->gerenciar();
	}

    function todas(){
        $this->where='';
        $this->faturado = -1;
        $this->base_url='todas';

		$this->gerenciar();
	}

	function gerenciar(){
        if(!isset($this->where)) $this->where='faturado = 0';
        if(!isset($this->faturado)) $this->faturado = 0;
        if(!isset($this->base_url)) $this->base_url='';
        
        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'vComanda')){
           $this->session->set_flashdata('error','Você não tem permissão para visualizar comandas.');
           redirect(base_url());
        }

        $this->load->library('pagination');
        
        if(strlen($this->base_url) < 1)$this->base_url='gerenciar';
                
        $config['base_url'] = base_url().'index.php/comandas/'.$this->base_url.'/';
        $config['total_rows'] = $this->vendas_model->count('vendas', 2, $this->faturado);
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        	
        $this->pagination->initialize($config); 	

		$this->data['results'] = $this->vendas_model->get('vendas','*',$this->where,$config['per_page'],$this->uri->segment(3), false, 'array', 2);
       
	    $this->data['view'] = 'comandas/comandas';
       	$this->load->view('tema/topo',$this->data);
      
		
    }

    function clientes($idCliente=''){
        
        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'vComanda')){
          $this->session->set_flashdata('error','Você não tem permissão para visualizar Comandas.');
          redirect(base_url());  
        }

        
        
        if($idCliente){
            $this->data['results'] = $this->clientes_model->get_with_comandas('clientes', '*', "clientes.idClientes = $idCliente", 100)[0];
            $this->data['view'] = 'comandas/comandasCliente';
        }else{
            $this->data['result'] = $this->clientes_model->get_with_comandas('clientes', '*', '', 100);
            $this->data['view'] = 'comandas/clientes';
        }
       	$this->load->view('tema/topo',$this->data);   
    }
	
    function adicionar(){

        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'aComanda')){
          $this->session->set_flashdata('error','Você não tem permissão para adicionar Comandas.');
          redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        
        if ($this->form_validation->run('comandas') == false) {
           $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {

            $dataVenda = $this->input->post('dataVenda');

            try {
                
                $dataVenda = explode('/', $dataVenda);
                $dataVenda = $dataVenda[2].'-'.$dataVenda[1].'-'.$dataVenda[0];


            } catch (Exception $e) {
               $dataVenda = date('Y/m/d'); 
            }

            $data = array(
                'dataVenda' => $dataVenda,
                'descricao' => $this->input->post('descricao'),
                'tipo' => 2,
                'faturado' => 0
            );

            $client_id = $this->input->post('clientes_id');
            if(is_numeric($client_id)) $data['clientes_id'] = $client_id;

            if (is_numeric($id = $this->vendas_model->add('vendas', $data, true)) ) {
                $this->session->set_flashdata('success','Comanda iniciada com sucesso, adicione os produtos.');
                redirect('comandas/editar/'.$id);

            } else {
                
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }
         
        $this->data['view'] = 'comandas/adicionarComanda';
        $this->load->view('tema/topo', $this->data);
    }
    

    
    function editar() {

        if(!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))){
            $this->session->set_flashdata('error','Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'eComanda')){
          $this->session->set_flashdata('error','Você não tem permissão para editar Comandas');
          redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('comandas') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {

            $dataVenda = $this->input->post('dataVenda');

            try {
                
                $dataVenda = explode('/', $dataVenda);
                $dataVenda = $dataVenda[2].'-'.$dataVenda[1].'-'.$dataVenda[0];


            } catch (Exception $e) {
               $dataVenda = date('Y/m/d'); 
            }

            $data = array(
                'dataVenda' => $dataVenda,
                'descricao' => $this->input->post('descricao'),
            );

            $client_id = $this->input->post('clientes_id');
            if(is_numeric($client_id)) $data['clientes_id'] = $client_id;

            if ($this->vendas_model->edit('vendas', $data, 'idVendas', $this->input->post('idVendas')) == TRUE) {
                $this->session->set_flashdata('success','Comanda editada com sucesso!');
                redirect(base_url() . 'index.php/comandas/editar/'.$this->input->post('idVendas'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->vendas_model->getById($this->uri->segment(3), 2);
        $this->data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $this->data['view'] = 'comandas/editarComanda';
        $this->load->view('tema/topo', $this->data);
   
    }

    public function visualizar(){

        if(!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))){
            $this->session->set_flashdata('error','Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }
        
        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'vComanda')){
          $this->session->set_flashdata('error','Você não tem permissão para visualizar vendas.');
          redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->data['result'] = $this->vendas_model->getById($this->uri->segment(3), 2);
        $this->data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        
        $this->data['view'] = 'comandas/visualizarComanda';
        $this->load->view('tema/topo', $this->data);
       
    }
	
    function excluir(){

        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'dComanda')){
          $this->session->set_flashdata('error','Você não tem permissão para excluir comandas');
          redirect(base_url());
        }
        
        $id =  $this->input->post('id');
        if ($id == null){

            $this->session->set_flashdata('error','Erro ao tentar excluir comanda.');            
            redirect(base_url().'index.php/comandas/gerenciar/');
        }

        $this->db->where('vendas_id', $id);
        $this->db->delete('itens_de_vendas');

        $this->db->where('idVendas', $id);
        $this->db->delete('vendas');           

        $this->session->set_flashdata('success','Comanda excluída com sucesso!');            
        redirect(base_url().'index.php/comandas/gerenciar/');

    }

    public function autoCompleteProduto(){
        
        if (isset($_GET['term'])){
            $q = strtolower($_GET['term']);
            $this->vendas_model->autoCompleteProduto($q);
        }

    }

    public function autoCompleteCliente(){

        if (isset($_GET['term'])){
            $q = strtolower($_GET['term']);
            $this->vendas_model->autoCompleteCliente($q);
        }

    }

    public function autoCompleteUsuario(){

        if (isset($_GET['term'])){
            $q = strtolower($_GET['term']);
            $this->vendas_model->autoCompleteUsuario($q);
        }

    }



    public function adicionarProduto(){

        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'eComanda')){
          $this->session->set_flashdata('error','Você não tem permissão para editar Comandas.');
          redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'trim|required|xss_clean');
        $this->form_validation->set_rules('idProduto', 'Produto', 'trim|required|xss_clean');
        $this->form_validation->set_rules('idVendasProduto', 'Vendas', 'trim|required|xss_clean');
        
        if($this->form_validation->run() == false){
           echo json_encode(array('result'=> false)); 
        }
        else{

            $preco = $this->input->post('preco');
            $quantidade = $this->input->post('quantidade');
            $subtotal = $preco * $quantidade;
            $produto = $this->input->post('idProduto');
            $data = array(
                'quantidade'=> $quantidade,
                'subTotal'=> $subtotal,
                'produtos_id'=> $produto,
                'vendas_id'=> $this->input->post('idVendasProduto'),
            );

            if($this->vendas_model->add('itens_de_vendas', $data) == true){
                $sql = "UPDATE produtos set estoque = estoque - ? WHERE idProdutos = ?";
                $this->db->query($sql, array($quantidade, $produto));
                
                echo json_encode(array('result'=> true));
            }else{
                echo json_encode(array('result'=> false));
            }

        }
        
      
    }

    function excluirProduto(){

            if(!$this->permission->checkPermission($this->session->userdata('permissao'),'eComanda')){
              $this->session->set_flashdata('error','Você não tem permissão para editar Comandas');
              redirect(base_url());
            }

            $ID = $this->input->post('idProduto');
            if($this->vendas_model->delete('itens_de_vendas','idItens',$ID) == true){
                
                $quantidade = $this->input->post('quantidade');
                $produto = $this->input->post('produto');


                $sql = "UPDATE produtos set estoque = estoque + ? WHERE idProdutos = ?";

                $this->db->query($sql, array($quantidade, $produto));
                
                echo json_encode(array('result'=> true));
            }
            else{
                echo json_encode(array('result'=> false));
            }           
    }



    public function faturar() {

        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'eComanda')){
              $this->session->set_flashdata('error','Você não tem permissão para editar Comandas');
              redirect(base_url());
            }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
 

        if ($this->form_validation->run('receita_comanda') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {

            $recebimento = $this->input->post('recebimento');

            try {
            
                if($recebimento != null){
                    $recebimento = explode('/', $recebimento);
                    $recebimento = $recebimento[2].'-'.$recebimento[1].'-'.$recebimento[0];

                }
            } catch (Exception $e) {
               $vencimento = date('Y/m/d'); 
            }

            $data = array(
                'descricao' => set_value('descricao'),
                'valor' => $this->input->post('valor'),
                'data_pagamento' => $recebimento,
                'baixado' => $this->input->post('recebido'),
                'forma_pgto' => $this->input->post('formaPgto'),
                'tipo' => $this->input->post('tipo')
            );

            if ($this->vendas_model->add('lancamentos',$data) == TRUE) {
                
                $venda = $this->input->post('vendas_id');

                $this->db->set('faturado',1);
                $this->db->set('valorTotal',$this->input->post('valor'));
                $this->db->where('idVendas', $venda);
                $this->db->update('vendas');

                $this->session->set_flashdata('success','Comanda faturada com sucesso!');
                $json = array('result'=>  true);
                echo json_encode($json);
                die();
            } else {
                $this->session->set_flashdata('error','Ocorreu um erro ao tentar faturar comanda.');
                $json = array('result'=>  false);
                echo json_encode($json);
                die();
            }
        }

        $this->session->set_flashdata('error','Ocorreu um erro ao tentar faturar comanda.');
        $json = array('result'=>  false);
        echo json_encode($json);
        
    }


}

