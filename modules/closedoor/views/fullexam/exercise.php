<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\ExamType;

/* @var $this yii\web\View */
$this->title = 'Новое задание';
if ($model->name != '')
    $this->title = strip_tags($model->name);

$this->params['breadcrumbs'][] = ['label' => 'Список экзаменов', 'url' => ['index']];
$this->params['breadcrumbs'][] = [ 'label' => 'Экзамен', 'url' => ['create', 'id'=>$fullexam_id]];
$this->params['breadcrumbs'][] = $this->title;
// $model->type = 4;
$exams = [];
$url = '#';
if ($model->type > 0) {
    switch ($model->type) {
        case 1:
            $exams = $model->tests;
            $url = 'exam/test';
            break;
        case 2:
            $exams = $model->correlates;
            $url = 'exam/correlate';
            break;
        case 3:
            $exams = $model->additions;
            $url = 'exam/addition';
            break;
        case 4:
            $exams = $model->writes;
            $url = 'exam/write';
            break;

        default:
            break;
    }
}

?>
<div class="exercise_page">
    <?php $form = ActiveForm::begin([
        'id' => 'formExercise',
        'method' => 'post',
        // 'action' => ['/admin/examsection/add-type'],
    ]); ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'section_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'place')->input('number', ['min' => 1, 'max' => 99, 'step' => 1]) ?>
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'hint')->textarea(['rows'=>6]) ?>
        <?= $form->field($model, 'fullexam')->checkbox() ?>
        <?= $form->field($model, 'fullexam_points')->input('number', ['min' => 1, 'max' => 999, 'step' => 1]) ?>
        <?php if (count($exams) == 0): ?>
            <?= $form->field($model, 'type')->dropDownList(ExamType::getTypesArr()) ?>
        <?php else : ?>
            <h3>Задание с типом "<?= ExamType::getTypeLable($model->type) ?>"</h3>
        <?php endif; ?>
        <?= $form->field($model, 'publish')->checkbox() ?>
        <button type="submit" name="save" class="btn btn-success">Сохранить</button>
        <a href="<?= Url::to(['fullexam/delete-exercise', 'id'=>$model['id']])?>" class="btn btn-danger btn-delete">Удалить</a>

    <?php ActiveForm::end(); ?>
    <br>
    <?php if($model->type > 0) {
        echo Html::a('Добавить упражнение',
            [$url, 'exercise_id'=>$model->id],
            ['class'=>'btn btn-primary addExercise']);
    } ?>
    <!-- <div class="deleteAll btn btn-danger">Удалить все упражнения</div> -->

    <div class="debug"> </div>

    <div class="exercises" style="margin-top: 10px;">
        <ul class="list-group">
            <? foreach ((array)$exams as $exam) :?>
            <li class="list-group-item <?php if ($exam['publish']) {echo 'publish';} ?>" number='<?=$exam['id']?>'>
                <span class="delete deleteExam">Х</span>
                <a href="<?= Url::to([$url."/$exam[id]?fullexam_id=$fullexam_id&exercise_id=$model[id]"])?>">Упражнения №<?=$exam['id']?></a>
            </li>
            <? endforeach; ?>
        </ul>
    </div>
</div>

<?php

$js = <<<JS
    $(document).ready(function() {
        $('.btn-delete').click(function(e) {
            if (!confirm('Вы уверены, что хотите удалить это задание? Все привязанные упражнения будут удалены!')) {
                e.preventDefault();
            }
        });

        $('.deleteAll').click(function(e) {
            if (confirm('Вы уверены, что хотите очистить это задание? Все упражнения будут удалены!')) {
                $(document).attr(
                    'location',
                    '/closedoor/examsection/clrexercise/'+$('#esexercise-id').val()
                );
            }
        });

        $('.deleteExam').click(function(e) {
            e.preventDefault();
            let th = $(this);
            if (confirm('Вы уверены, что хотите удалить это задание?')) {
                let data = {};
                    data['type'] = $model->type;
                    data['id'] = th.parent().attr('number');
                $.ajax({
                    url: '/closedoor/fullexam/delete-task',
                    type: 'POST',
                    data: data,
                    success: function (req) {
                        if (req != '0') {
                            th.parent().remove();
                        } else {
                            globalError('Что-то пошло не так!');
                        }
                    },
                    error: (jqXHR, status, errorThrown) => {ajaxError(errorThrown, jqXHR);}
                });
            }
        });
    });
JS;
$this->registerJs($js);

?>
