<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\UserStatus;

/* @var $this yii\web\View */
// if ($model->username != null) {
//     $this->title = $model->username;
// } else {
$this->title = 'Открыть доступ к курсу';
$this->title = ($model->id > 0) ? $this->title : $this->title.' (new)' ;
// }
$this->params['breadcrumbs'][] = [
        'label' => 'Пользователь',
        'url' => ["/user/create/".$model->learner_id],
    ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webinar">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'webinarForm',
        'method' => 'post',
        'action' => ['user/course/'.$model->course_id, 'user_id'=>$model->learner_id],
        'options' => [
            'enctype' => 'multipart/form-data'
        ],
    ]); ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'learner_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'course_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'module_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'lesson_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'strDateStart')->textInput() ?>
        <?= $form->field($model, 'strDateEnd')->textInput() ?>

        <?= Html::submitButton('Сохранить', ['class' => 'btn save btn-success']) ?>
    <?php ActiveForm::end(); ?>
</div>

<?php

// $js = <<<JS
//
// JS;
// $this->registerJs($js);

?>
