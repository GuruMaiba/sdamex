<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Examwrite */

$this->title = 'Проверка практических';
$this->params['breadcrumbs'][] = ['label' => 'Список экзаменов', 'url' => ['fullexam/index']];
$this->params['breadcrumbs'][] = ['label' => 'Список работ', 'url' => ['check/index', 'type'=>'write']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="check-practical">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <?php $form = ActiveForm::begin([
        'id' => 'checkForm',
        'method' => 'post',
        // 'action' => ['/admin/check/practical'],
        // 'options' => [
            // 'class' => 'form-horizontal',
            // 'enctype' => 'multipart/form-data'
        // ],
    ]); ?>
        <div class="task" title="Задание"><?=$model->write->task?></div>
        <div class="text"><?=Html::encode($model->write->text)?></div>

        <?php if ($model->write->audio_name != null) : ?>
        <audio controls controlsList="nodownload">
            <source src="/<?=Url::to('@audioFolder/'.$model->write->audio_name)?>" type="audio/mpeg">
        </audio>
        <?php endif; ?>

        <hr>
        <div class="answer"><?=$model->text?></div>
        <div class="answerDocs">
            <?php if (file_exists(Yii::getAlias("@fileWrites/$model->archive_file"))) : ?>
            <a target="_blank" href="/<?= Url::to("@fileWrites/$model->archive_file") ?>">Архив с выполненными заданиями</a>
            <?php else : ?>
            <a target="_blank" href="<?=$subject['link'].Url::to("@fileWrites/$model->archive_file")?>">Архив с выполненными заданиями</a>
            <?php endif; ?>
        </div>
        <?= $form->field($model, 'teacher_comment')->textarea() ?>
        <?= $form->field($model, 'exp')->input('number', ['min' => 0, 'max' => $model->exp]) ?>
        <? if ($model->write->exercise_id > 0) : ?>
        <?= $form->field($model, 'points')->input('number', ['min' => 0, 'max' => $model->points]) ?>
        <? endif; ?>
        
        <hr>
        <?php if ($themes != []) : ?>
        <?= $form->field($model->write, 'themes')->hiddenInput()->label('Уберите темы, которые выполнены с ошибкой'); ?>
        <?= $this->render('/theme/_themes', [
            'model' => $themes,
            'hide' => true,
            'active' => $wThemes
        ]); ?>
        <hr>
        <? endif; ?>
        <!-- echo Html :: csrfMetaTags(); -->
        <button class="btn btn-success send" type="submit" name="action" value="right">Засчитать как верную</button>
        <button class="btn btn-danger send" type="submit" name="action" value="error">Засчитать как c ошибкой</button>
    <?php ActiveForm::end(); ?>
</div>

<?php

$maxExp = $model->exp;
$maxPoints = $model->points;

$js = <<<JS
    $('.container').on('click', '.searchTheme .list .item', function () {
        let id = parseInt($(this).attr('number'));
        let themes = JSON.parse($('#write-themes').val());
        if ($(this).hasClass('active'))
            themes.push(id);
        else
            themes = themes.filter(item => item != id);
        $('#write-themes').val(JSON.stringify(themes));
    });

    $('.send').click(function (e) {
        let exp = $('#reply-exp').val();
            $('#reply-exp').val(exp = (exp == '' || exp < 0) ? 0 : parseInt(exp));
        let points = $('#reply-points').val();
            $('#reply-points').val(points = (points == '' || points < 0) ? 0 : parseInt(points));
        let maxExp = $maxExp;
        let maxPoints = $maxPoints;
        if ($('#reply-teacher_comment').val() == '' || exp > maxExp || points > maxPoints) {
            e.preventDefault();
            alert('Все поля обязательны для заполнения, число опыта и баллов должно находиться в дозволенном диапазоне!');
        }
    });
JS;

$this->registerJs($js);
// $this->registerJsFile('web/scripts/textSpan.js', ['depends'=>['app\assets\AdminAsset']]);

 ?>
