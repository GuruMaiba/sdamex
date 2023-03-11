<?php
    foreach ($codes as $invite) :
    $date = ($invite['end_at'] > 0) ? date('d.m.Y',$invite['end_at']) : 'бессрочно';
?>
    <div id="<?=$invite['code']?>" class="code"
        type="<?=$invite['type']?>"
        reward="<?=$invite['reward']?>"
        strdate="<?=($invite['end_at'] > 0) ? $date : null?>">
            <?=$invite['code']?> / <?=$date?>
    </div>
<?php endforeach; ?>