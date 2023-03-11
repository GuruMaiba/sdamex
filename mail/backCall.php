<?php

// use yii\helpers\Html;

 ?>

<h1 style="color: #ff5c57; line-height:1;">SDAMEX</h1>
<h3 >Запрос на обратный звонок!</h3>
<hr>
Телефон: <?=$phone?> <br>
<? if (!empty($time)) : ?>
Удобное время: <?=$time?>
<? endif; ?>