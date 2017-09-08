<?php if($this->permission->checkPermission($this->session->userdata('permissao'),'aComanda')){ ?>
    <a href="<?php echo base_url();?>index.php/comandas/adicionar" class="btn btn-success"><i class="icon-plus icon-white"></i> Adicionar Comanda</a>
<?php } ?>




     
     <div class="span11" style="margin:0px">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="icon-list-alt"></i>
                </span>
                <h5>Comandas de Clientes</h5>
            </div>
            <div class="widget-content">
                <ul class="site-stats" style="text-align:left">
                    
                    <?php foreach($result as $cliente) { ?>
                        <li style="width:19%;margin:0px"><a href="<?php echo base_url()?>index.php/comandas/clientes/<?=$cliente[0]->idClientes;?>" ><i class="icon-user"></i> <small><?=$cliente[0]->nomeCliente;?></small></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>



	

<div class="span10">
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
<div>
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