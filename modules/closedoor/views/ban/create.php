<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BanUser */

$this->title = 'Create Ban User';
$this->params['breadcrumbs'][] = ['label' => 'Ban Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ban-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
