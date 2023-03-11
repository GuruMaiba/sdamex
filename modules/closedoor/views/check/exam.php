<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Theme;

/* @var $this yii\web\View */
/* @var $model app\models\Examwrite */

$this->title = 'Проверка полного экзамена';
$this->params['breadcrumbs'][] = ['label' => 'Список экзаменов', 'url' => ['fullexam/index']];
$this->params['breadcrumbs'][] = ['label' => 'Список работ', 'url' => ['check/index', 'type'=>'exam']];
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
        <?php foreach ($writes as $write) : ?>
            <div class="task" title="Задание"><?=$write['task']?></div>
            <div class="text"><?=$write['text']?></div>
            <?php if (!empty($write['audio_name'])) : ?>
            <audio controls controlsList="nodownload">
                <source src="/<?=Url::to('@audioFolder/'.$write['audio_name'])?>" type="audio/mpeg">
            </audio>
            <?php endif; ?>
            <br>
            <div class="answer"><?=Html::encode($answers[$write['id']]['answer'])?></div>
            <div class="answerDocs">
                <?php if (file_exists(Yii::getAlias("@fileWrites/".$answers[$write['id']]['file']))) : ?>
                <a target="_blank" href="/<?= Url::to("@fileWrites/".$answers[$write['id']]['file']) ?>">Архив с выполненными заданиями</a>
                <?php else : ?>
                <a target="_blank" href="<?=$subject['link'].Url::to("@fileWrites/".$answers[$write['id']]['file'])?>">Архив с выполненными заданиями</a>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="control-label">Опыт</label>
                <input class="form-control" type="number" name="Write[<?=$write['id']?>][exp]" min="0" max="<?=$write['exp']?>" value="<?=$write['exp']?>">
            </div>
            <div class="form-group">
                <label class="control-label">Баллы</label>
                <input class="form-control" type="number" name="Write[<?=$write['id']?>][points]" min="0" max="<?=$write['exercise']['fullexam_points']?>" value="<?=$write['exercise']['fullexam_points']?>">
            </div>
            <?php $themes = json_decode($write['themes'], true); ?>
            <?php if ($themes != []) : ?>
                <label class="control-label">Отключите ошибочные темы</label>
                <input type="hidden" name="Write[<?=$write['id']?>][themes]" value="<?=$write['themes']?>">
                <?= $this->render('/theme/_themes', [
                    'model' => Theme::find()->where(['in', 'id', $themes])->all(),
                    'hide' => true,
                    'active' => $themes
                ]); ?>
            <?php endif; ?>
            <div class="checkbox-inline">
                <label><input type="checkbox" name="Write[<?=$write['id']?>][right]" value='1'>Задание выполнено правильно</label>
            </div>
            <hr>
        <?php endforeach; ?>
        <?= $form->field($model, 'teacher_comment')->textarea() ?>
        <!-- echo Html :: csrfMetaTags(); -->
        <button class="btn btn-success send" type="submit">Завершить проверку</button>
    <?php ActiveForm::end(); ?>
</div>

<?php

$maxExp = 1;
$maxPoints = 1;

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
