<?php

// use yii\helpers\Html;

 ?>

<h1 style="color: #ff5c57; line-height:1;">SDAMEX</h1>
<h3 >Новая заявка на должность учителя</h3>
<hr>
Имя: <?=$name?> <br>
Email: <?=$email?> <br>
Телефон: <?=$phone?> <br>
<? if (!empty($time)) : ?>
Удобное время: <?=$time?>
<? endif; ?>