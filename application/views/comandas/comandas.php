<?php if($this->permission->checkPermission($this->session->userdata('permissao'),'aComanda')){ ?>
    <a href="<?php echo base_url();?>index.php/comandas/adicionar" class="btn btn-success"><i class="icon-plus icon-white"></i> Adicionar Comanda</a>
<?php } ?>

<?php

if(!$results){?>
	<div class="widget-box">
     <div class="widget-title">
        <span class="icon">
            <i class="icon-tags"></i>
         </span>
        <h5>Comandas</h5>

     </div>

<div class="widget-content nopadding">


<table class="table table-bordered ">
    <thead>
        <tr style="backgroud-color: #2D335B">
            <th>#</th>
            <th>Data da Comanda</th>
            <th>Comanda</th>
            <th>Faturado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td colspan="6">Nenhuma comanda Cadastrada</td>
        </tr>
    </tbody>
</table>
</div>
</div>
<?php } else{?>


<div class="widget-box">
     <div class="widget-title">
        <span class="icon">
            <i class="icon-tags"></i>
         </span>
        <h5>Comanda</h5>

     </div>

<div class="widget-content nopadding">


<table class="table table-bordered ">
    <thead>
        <tr style="backgroud-color: #2D335B">
            <th>#</th>
            <th>Data da Comanda</th>
            <th>Comanda</th>
            <th>Faturado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $r) {
            $dataVenda = date(('d/m/Y'),strtotime($r->dataVenda));
            if($r->faturado == 1){$faturado = 'Sim';} else{ $faturado = 'Não';}           
            echo '<tr>';
            echo '<td>'.$r->idVendas.'</td>';
            echo '<td>'.$dataVenda.'</td>';
            echo '<td>'.$r->descricao.'</a></td>';
            echo '<td>'.$faturado.'</td>';
            
            echo '<td>';
            if($this->permission->checkPermission($this->session->userdata('permissao'),'vComanda')){
                echo '<a style="margin-right: 1%" href="'.base_url().'index.php/comandas/visualizar/'.$r->idVendas.'" class="btn tip-top" title="Ver mais detalhes"><i class="icon-eye-open"></i></a>'; 
            }
            if($this->permission->checkPermission($this->session->userdata('permissao'),'eComanda')){
                echo '<a style="margin-right: 1%" href="'.base_url().'index.php/comandas/editar/'.$r->idVendas.'" class="btn btn-info tip-top" title="Editar comanda"><i class="icon-pencil icon-white"></i></a>'; 
            }
            if($this->permission->checkPermission($this->session->userdata('permissao'),'dComanda')){
                echo '<a href="#modal-excluir" role="button" data-toggle="modal" venda="'.$r->idVendas.'" class="btn btn-danger tip-top" title="Excluir Comanda"><i class="icon-remove icon-white"></i></a>'; 
            }

            echo '</td>';
            echo '</tr>';
        }?>
        <tr>
            
        </tr>
    </tbody>
</table>
</div>
</div>
	
<?php echo $this->pagination->create_links();}?>

<?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vComanda')){ ?>
    <a href="<?php echo base_url();?>index.php/comandas" class="btn btn-info"><i class="icon-white"></i> Comandas Abertas</a>
<?php } ?>

<?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vComanda')){ ?>
    <a href="<?php echo base_url();?>index.php/comandas/fechadas" class="btn btn-info"><i class="icon-white"></i> Comandas Finalizadas</a>
<?php } ?>

<?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vComanda')){ ?>
    <a href="<?php echo base_url();?>index.php/comandas/todas" class="btn btn-info"><i class="icon-white"></i> Todas as Comandas</a>
<?php } ?>

<?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vComanda')){ ?>
    <a href="<?php echo base_url();?>index.php/comandas/clientes" class="btn btn-info"><i class="icon-white"></i> Comandas de Clientes</a>
<?php } ?>


<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form action="<?php echo base_url() ?>index.php/comandas/excluir" method="post" >
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h5 id="myModalLabel">Excluir Comanda</h5>
  </div>
  <div class="modal-body">
    <input type="hidden" id="idVenda" name="id" value="" />
    <h5 style="text-align: center">Deseja realmente excluir esta Comanda?</h5>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
    <button class="btn btn-danger">Excluir</button>
  </div>
  </form>
</div>






<script type="text/javascript">
$(document).ready(function(){


   $(document).on('click', 'a', function(event) {
        
        var venda = $(this).attr('venda');
        $('#idVenda').val(venda);

    });

});

</script>