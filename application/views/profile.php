<?php $this->load->view('header') ?>

<div style="margin-top: 400px;">

    <?php if($tipo == 0): ?>
    <a href="#"><button class="btn btn-default"> Ja Amigo</button></a>
    <?php elseif($tipo == 1): ?>
    <a href="<?= base_url('HomeController/aceptInvit/'  . $id)  ?>"><button class="btn btn-default">Aceita</button></a>
    <a href="<?= base_url('HomeController/cancelInvit/' . $id)  ?>"><button class="btn btn-default">Cancela</button></a>
    <?php elseif($tipo == 2): ?>
    <a href="<?= base_url('HomeController/cancelInvit/' . $id)  ?>"><button class="btn btn-default">Cancela</button></a>
    <?php else: ?>
    <a href="<?= base_url('HomeController/sendlInvit/'  . $id) ?>"><button class="btn btn-default">Envia</button></a>
    <?php endif; ?>
    
</div>


<div>
    <h1>todos os amigos</h1>
    <?php foreach ($friends as $friend): ?>
    <h1><?php echo $friend->id ?></h1>
    <?php endforeach; ?>
</div>
<?php $this->load->view('footer') ?>