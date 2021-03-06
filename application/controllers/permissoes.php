<?php

class Permissoes extends CI_Controller {
    

    /**
     * author: Ramon Silva 
     * email: silva018-mg@yahoo.com.br
     * 
     */
    
  function __construct() {
      parent::__construct();
      if ((!$this->session->userdata('session_id')) || (!$this->session->userdata('logado'))) {
          redirect('mapos/login');
      }

      if(!$this->permission->checkPermission($this->session->userdata('permissao'),'cPermissao')){
        $this->session->set_flashdata('error','Você não tem permissão para configurar as permissões no sistema.');
        redirect(base_url());
      }

      $this->load->helper(array('form', 'codegen_helper'));
      $this->load->model('permissoes_model', '', TRUE);
      $this->data['menuConfiguracoes'] = 'Permissões';
  }
	
	function index(){
		$this->gerenciar();
	}

	function gerenciar(){
        
        $this->load->library('pagination');
        
        
        $config['base_url'] = base_url().'index.php/permissoes/gerenciar/';
        $config['total_rows'] = $this->permissoes_model->count('permissoes');
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

		  $this->data['results'] = $this->permissoes_model->get('permissoes','idPermissao,nome,data,situacao','',$config['per_page'],$this->uri->segment(3));
       
	    $this->data['view'] = 'permissoes/permissoes';
       	$this->load->view('tema/topo',$this->data);

       
		
    }
	
    private $permissoes = array(
        'Cliente', 
        'Produto', 
        'Servico', 
        'Os', 
        'Venda', 
        'Comanda', 
        'Arquivo', 
        'Lancamento'
    );


    function gerarPermissao($modulo){

        return array(
            array('a'.$modulo, 'Adicionar '.$modulo),
            array('e'.$modulo, 'Editar '.$modulo),
            array('d'.$modulo, 'Excluir '.$modulo),
            array('v'.$modulo, 'Visualizar '.$modulo),
            array('r'.$modulo, 'Relatório '.$modulo),
        );
    }

    function gerarPermissoes(){
        $ret =  array();
        foreach($this->permissoes as $permissao)
            $ret[] = $this->gerarPermissao($permissao);

        return $ret;
    }

    function adicionar() {

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        
        $this->data['result']['permissoes'] = $this->gerarPermissoes();

        $this->form_validation->set_rules('nome', 'Nome', 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            
            $nomePermissao = $this->input->post('nome');
            $cadastro = date('Y-m-d');
            $situacao = 1;


            $permissoes = array();
            foreach($this->gerarPermissoes() as $modulo){
                foreach($modulo as $p) $permissoes[$p[0]] = $this->input->post($p[0]);
            }

            $more = array('cUsuario', 'cEmitente', 'cPermissao', 'cBackup', 'rFinanceiro');
            foreach($more as $each) $permissoes[$each] = $this->input->post($each);

            $permissoes = serialize($permissoes);


            $data = array(
                'nome' => $nomePermissao,
                'data' => $cadastro,
                'permissoes' => $permissoes,
                'situacao' => $situacao
            );

            if ($this->permissoes_model->add('permissoes', $data) == TRUE) {

                $this->session->set_flashdata('success', 'Permissão adicionada com sucesso!');
                redirect(base_url() . 'index.php/permissoes/adicionar/');
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'permissoes/adicionarPermissao';
        $this->load->view('tema/topo', $this->data);

    }

    function editar() {

        
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            
            $nomePermissao = $this->input->post('nome');
            
            $situacao = $this->input->post('situacao');
            
            $permissoes = array();
            foreach($this->gerarPermissoes() as $modulo){
                foreach($modulo as $p) $permissoes[$p[0]] = $this->input->post($p[0]);
            }

            $more = array('cUsuario', 'cEmitente', 'cPermissao', 'cBackup', 'rFinanceiro');
            foreach($more as $each) $permissoes[$each] = $this->input->post($each);

            $permissoes = serialize($permissoes);

            $data = array(
                'nome' => $nomePermissao,
                'permissoes' => $permissoes,
                'situacao' => $situacao
            );

            if ($this->permissoes_model->edit('permissoes', $data, 'idPermissao', $this->input->post('idPermissao')) == TRUE) {
                $this->session->set_flashdata('success', 'Permissão editada com sucesso!');
                redirect(base_url() . 'index.php/permissoes/editar/'.$this->input->post('idPermissao'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um errro.</p></div>';
            }
        }

        $this->data['result'] = $this->permissoes_model->getById($this->uri->segment(3));
        $this->data['permissions'] = $this->gerarPermissoes();

        $this->data['view'] = 'permissoes/editarPermissao';
        $this->load->view('tema/topo', $this->data);

    }
	
    function desativar(){

        
        $id =  $this->input->post('id');
        if ($id == null){

            $this->session->set_flashdata('error','Erro ao tentar desativar permissão.');            
            redirect(base_url().'index.php/permissoes/gerenciar/');
        }

        $data = array(
          'situacao' => false
        );

        if($this->permissoes_model->edit('permissoes',$data,'idPermissao',$id)){
          $this->session->set_flashdata('success','Permissão desativada com sucesso!');  
        }
        else{
          $this->session->set_flashdata('error','Erro ao desativar permissão!');  
        }         
        
                  
        redirect(base_url().'index.php/permissoes/gerenciar/');
    }
}


/* End of file permissoes.php */
/* Location: ./application/controllers/permissoes.php */