<?php $this->load->view('header') ?>


<div class="container" style="margin-top: 80px;">
    <!--  PANEL -->
    <div class="panel panel-default">
        <div class="panel-heading">
            NOME DA PAGINA
        </div>
        <div class="panel-body">
            <!--  FORMULARIO DE INSERÇÃO -->
            <div class="col-sm-12">
                <form method="post" action="<?php echo site_url('admin/produtos/cadastrar') ?>">
                    <div class="form-group col-sm-4" style="padding-left: 0">
                        <label class="sr-only" for="nomeProduto">Nome Produto</label>
                        <input  type="text" class="form-control" name="nomeProduto" placeholder="Nome">
                    </div>
                    <div class="form-group col-sm-4" style="padding-left: 0">
                        <label class="sr-only" for="valorProduto">Valor Produto</label>
                        <input type="text" class="form-control" name="valorProduto" placeholder="Valor">
                    </div>
                    <div class=" col-sm-4" style="padding-right: 0;padding-left: 0" >
                        <button type="submit" class="btn btn-default" name="cadastrar">Cadastrar</button>
                    </div>
                </form>
            </div><!-- FIM FORMULARIO DE INSERÇÃO -->
            
            <!-- TABELA DE ACÕES -->
            <?php if(count($produtosDel) > 0 or count($itemAdd) > 0): ?>
            <div class="table-responsive col-sm-12">
                <table class="table table-hover " style="text-align: center;">
                    <thead>
                        <tr style="font-weight: bold; ">
                            <td>Acao</td>
                            <td>ID</td>
                            <td>Nome</td>
                            <td>Valor</td>
                            <td width="20%"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($produtosDel) > 0):
                        foreach ($produtosDel as $key => $value): ?>
                        <tr class="danger">
                            <td>Deletar</td>
                            <td><?php echo $value->id_pro; ?></td>
                            <td><?php echo $value->name_pro; ?></td>
                            <td><?php echo $value->valor_pro; ?></td>
                            <td>
                                <a href="<?php echo site_url('admin/produtos/cancelDelete/' . $key) ?>">
                                    <button class="btn btn-danger">Cancel</button>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if(!empty($_SESSION['itemAcidionado'])): ?>
                            <?php foreach ($itemAdd as $key => $value): ?>
                            <tr class="success">
                                <td>Adicionar</td>
                                <td>--</td>
                                <td><?php echo $value->name_pro; ?></td>
                                <td><?php echo $value->valor_pro; ?></td>
                                <td>
                                    <a href="<?php echo site_url('admin/produtos/cancelAdd/'.$key) ?>">
                                        <button class="btn btn-danger">Cancel</button>
                                    </a>
                               </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot style="font-weight: bold;">
                        <td>Acao</td>
                        <td>ID</td>
                        <td>Nome</td>
                        <td>Valor</td>
                        <td width="20%"> 
                            <a href="<?php echo site_url('admin/produtos/executeAcao') ?>">
                                <button type="button" class="btn btn-success">Confirmar acoes</button>
                            </a>
                        </td>
                    </tfoot>
                </table>
            </div>    
            <?php endif;?><!-- FIM TABELA DE ACÕES -->
            
            <!--  TABELA RESULTADOS -->
            <?php if(count($produtos) > 0):?>
            <div class="table-responsive col-sm-12">
                <table class="table table-bordered table-hover " style="text-align: center;">
                    <thead>
                        <tr style="font-weight: bold; ">
                            <td>ID</td>
                            <td>Nome</td>
                            <td>Valor</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?php echo $produto->id_pro; ?></td>
                            <td><?php echo $produto->name_pro; ?></td>
                            <td><?php echo $produto->valor_pro; ?></td>
                            <td width="20%">
                                <a href="<?php echo site_url('admin/produtos/deletar/' . $produto->id_pro) ?>">
                                    <button type="button" class="btn btn-primary deletar">Deletar</button>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                    <tfoot style="font-weight: bold;">
                        <td>ID</td>
                        <td>Nome</td>
                        <td>Valor</td>
                        <td></td>
                    </tfoot>
                </table>
            </div>
            <?php endif;?><!--  FIM TABELA RESULTADOS -->
            
        </div>
    </div><!-- FIM  PANEL -->
</div>


<?php $this->load->view('footer') ?>

<script type="text/javascript">
    $(window).load(function(){
        $('.deletar').click(function(e){
            e.preventDefault();
            var id = <?php echo $_SESSION['id'] ?>
            alert(id);
            
        })
        
    });
    
</script>
