<?php $this->load->view('header') ?>

<div style="margin-top: 200px;">
    <table>
    <?php foreach ($rows as $row): ?>
        <?php if($this->session->userdata('logged') != $row->user_id): ?>
        <tr>
            <td><a href="profile/<?=$row->user_id ?>"><?php echo $row->user_username ?></a></td>
            
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </table>
</div>

<?php $this->load->view('footer') ?>
