<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\UserStatus;

/* @var $this yii\web\View */
/* @var $model app\models\BanUser */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="ban-user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'status')->dropDownList([
        UserStatus::PERMANENT_BAN => 'Вечный бан',
        UserStatus::TEMPORARY_BAN => 'Временный бан',
    ], ['prompt' => 'Выберите тип блокировки пользователя']) ?>

    <div class="form-group field-banuser-timeBan">
        <label class="control-label" for="banuser-timeBan">Время блокировки</label>
        <?= Html::input('text', 'BanUser[timeBan]', $strban, [
            'id' => 'banuser-timeBan',
            'class' => 'form-control banuser-timeBan',
            'placeholder' => '1 year 2 month 3 week 4 day 5 hour 6 min 7 sec'
        ]); ?>
        <div class="help-block"></div>
    </div>

    <?= $form->field($model, 'cause')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
