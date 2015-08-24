<?php $this->load->view('header') ?>

<div style="height: 200px;">
    
</div>
<div class="container">
    <?php if(!empty($message)){
        echo $message ;            
    }?>
    <div class="col-sm-4 col-sm-push-4">
        <form method="post" action="">
          <div class="form-group">
            <label for="name">Nome</label>
            <input type="text" class="form-control" name="username" placeholder="Seu nome">
          </div>
          <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="text" class="form-control" id="password" name="password" placeholder="Sua senha">
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
</div>

<?php $this->load->view('footer') ?>