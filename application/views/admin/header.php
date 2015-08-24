<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('css/bootstrap.css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('js/bootstrap.js');?>">
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url('home'); ?>">Project name</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-left">
            <?php if($this->session->userdata('logged') == true): ?>
            <li><a href="#">profile</a></li>
            <li><a href="#">request</a></li>
            <li><a href="#">Friends</a></li>
            <li><a href="<?php echo base_url('members'); ?>">menbers</a></li>
            <li><a href="<?php echo base_url('sair'); ?>">logout</a></li>
            <?php else: ?>
            <li><a href="<?php echo base_url('loguin'); ?>">Loguin</a></li>
            <li><a href="<?php echo base_url('register'); ?>">Register</a></li>
            <?php endif; ?>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>
        </div>
      </div>
</nav>