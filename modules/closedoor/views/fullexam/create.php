<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
// use vova07\imperavi\Widget;

/* @var $this yii\web\View */
/* @var $model app\models\Examtest */
$this->title = 'Создать экзамен';
if ($model->name != null && $model->name != '')
    $this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Экзамены', 'url' => ['index']];
// $this->params['breadcrumbs'][] = [
//     'label' => 'Задание',
//     'url' => ['examsection/exercise', 'id'=>$model->exercise_id]
// ];
$this->params['breadcrumbs'][] = $this->title;

$marks = json_decode($model->marks, true);
?>

<div class="fullexamCreate">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([ 'id' => 'fullexamForm' ]); ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'desc')->widget(\vova07\imperavi\Widget::className(), [
                'settings' => [
                    'minHeight' => 150,
                    'imageUpload' => Url::to(['/imperavi/image-upload']),
                    'imageDelete' => Url::to(['/imperavi/image-delete']),
                    'imageManagerJson' => Url::to(['/imperavi/images-get']),
                    'plugins' => [
                        'fontsize',
                        'fullscreen',
                    ],
                ],
                'plugins' => [
                    'imagemanager' => 'vova07\imperavi\bundles\ImageManagerAsset',
                ]
            ]) ?>
        <?= $form->field($model, 'subject_id')->dropDownList($model->subjects, []) ?>
        <?php if ($model->courses != []) : ?>
        <?= $form->field($model, 'course_id')->dropDownList($model->courses, [ 'prompt' => 'Курс не выбран' ]) ?>
        <?php endif; ?>
        <?= $form->field($model, 'marks')->hiddenInput() ?>
        <div class="marks">
            <? for ($i=2; $i < 6; $i++) : ?>
            <div class="item">
                <span><?=$i?></span>
                <input class="markInp" type="number" name="Marks[<?=$i?>][min]" placeholder="MIN" value="<?=$marks[$i][0]?>">
                <?php if ($i != 5) : ?>
                    <input class="markInp" type="number" name="Marks[<?=$i?>][max]" placeholder="MAX" value="<?=$marks[$i][1]?>">
                <?php else : ?>
                    - <span><?=($model->max_points)?$model->max_points:0?> <i>(макс. число заполняется автоматически)</i></span>
                <?php endif; ?>
            </div>
            <? endfor; ?>
        </div>
        <?= $form->field($model, 'publish')->checkbox() ?>
        <button class="btn btn-success send" type="submit" name="button">Сохранить</button>
        <a href="<?=Url::to(['delete-fullexam', 'id'=>$model->id])?>" class="btn btn-danger"
            onclick="return confirm('Вы уверены, что хотите удалить этот экзамен?');">Удалить</a>

        <?php if ($model->id > 0 && $model->subject_id > 0 && $model->publish) {
            if ($model->subject_id == 1) {
                foreach (Yii::$app->params['listSubs'] as $id => $subject) {
                    if ($subject['isActive'])
                        $sub = $subject;
                }
            } else
                $sub = Yii::$app->params['listSubs'][$model->subject_id];
            echo Html::a('Обзор', Url::to($sub['link'].'exams'), ['class' => 'btn btn-primary']);
        } ?>
    <?php ActiveForm::end(); ?>

    <?php if ($model->id > 0) : ?>
    <div style="padding-left: 40px; margin-top: 20px;">
        <h4>Разделы</h4>
        <hr>
        <div class="btn btn-primary btnCreateSection">Добавить секцию</div>
        <div class="sections" style="margin-top: 15px;">
            <?= $this->render('_section', [
                'model' => $model->sections,
            ]) ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="debug"></div>
</div>

<?php
$csrf = Yii::$app->request->getCsrfToken();

$js = <<<JS
    let csrf = '$csrf';

    $(document).ready(function(){
        $('.btnCreateSection').click(function() {
            let th = $(this);
            if (th.hasClass('disabled')) { return false; }
            th.addClass('disabled');

            let data = {};
                data['_csrf'] = csrf;
                data['id'] = $("#fullexam-id").val();

            $.post('/closedoor/fullexam/create-section', data)
                .done(function (req) {
                    if (req == 0)
                        globalError();
                    else
                        $('.sections').append(req);
                    th.removeClass('disabled');
                });
        });

        $('.sections').on('change', '.changeSection', function () {
            updateModel($(this), 'sec');
        });

        $('.sections').on('click', '.delSection', function () {
            deleteModel($(this), 'sec');
        });

        $('.sections').on('click', '.delExercise', function () {
            deleteModel($(this), 'exe');
        });

        $('.send').click(function (e) {
            if (!checkMark())
                e.preventDefault();
        });
    });

    function updateModel(th, type) {
        if (th.hasClass('disabled'))
            return false;

        var data = {};
        var url = 'error';
        th.addClass('disabled');

        if (type == 'sec') {
            let id = th.parents('.section').attr('number');
            data = $('#form_section_'+id).serialize();
            url = '/closedoor/fullexam/update-section';
        } else {
            let id = th.parents('.exercise').attr('number');
            data = $('#form_exercise_'+id).serialize();
            url = 'update-exercise';
        }
        $.post('/closedoor/fullexam/update-section', data)
            .done(function (req) {
                // console.log(req);
                if (req == 0)
                    globalError('Ошибка! Изменения не применились!');
                th.removeClass('disabled');
            }).fail(() => {globalError()});
    }

    function deleteModel(th, type) {
        if (th.hasClass('disabled') || !confirm('Подтвердите удаление! Будут удалены все привязанные задания!')) { return false; }
        th.addClass('disabled');
        var data = {};
        var url = '/closedoor/fullexam/';
        var parent = null;
        if (type == 'sec') {
            parent = th.parents('.section');
            url += 'delete-section';
        } else {
            parent = th.parent();
            url += 'delete-exercise';
        }
        data['id'] = parent.attr('number');

        $.post(url, data)
            .done(function (req) {
                // console.log(req);
                if (req == 0)
                    globalError('Ошибка! Данные не удалены!');
                else
                    parent.remove();
                th.removeClass('disabled');
            }).fail(() => {globalError()});
    }

    function checkMark() {
        let flag = true;
        $('.markInp').each(function (i) {
            if ($(this).val() == '')
                flag = false;
        });
        return flag;
    }
JS;
$this->registerJs($js);

 ?>
