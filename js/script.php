<?php header("Content-type: application/javascript"); ?>
<?php session_start() ?>

    $(window).load(function(){
        $('.deletar').click(function(e){
            
            <?php var_dump($_SESSION['itemEditados'])?>;

        })
    });



