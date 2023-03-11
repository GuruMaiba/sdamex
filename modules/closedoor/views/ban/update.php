<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BanUser */

$this->title = 'Update Ban User: ' . $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'Ban Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Update';

$datetime = new DateTime(date("Y-m-d H:i:s", $model->ban_begin));
$interval = $datetime->diff(new DateTime(date("Y-m-d H:i:s", $model->ban_end)));
$strban = $interval->format('%y year %m month %a day %h hour %i min %s sec');

?>
<div class="ban-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'strban' => $strban,
    ]) ?>

</div>
