<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = ($model->title != null) ? $model->title : 'Модуль';

$this->params['breadcrumbs'][] = [
        'label' => 'Курсы',
        'url' => ['course'],
    ];
$this->params['breadcrumbs'][] = [
        'label' => 'Курс',
        'url' => ['course/details/'.$model->course_id],
    ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'moduleForm',
        'method' => 'post',
        // 'action' => ['course/create-module'],
        // 'options' => [
            // 'enctype' => 'multipart/form-data'
        // ],
    ]); ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'course_id')->hiddenInput()->label(false) ?>

        <?php if ($model->ava != null): ?>
            <img style="max-width:50%;" src="<?= Url::to(["@home/".Yii::getAlias("@crsAvaModule/".$model->ava)]) ?>">
        <?php endif; ?>
        <?= $form->field($model, 'image')->fileInput() ?>

        <?= $form->field($model, 'place')->input('number') ?>
        <?= $form->field($model, 'title')->textInput() ?>
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

        <?= $form->field($model, 'free')->checkbox(['value'=>1]) ?>
        <?= $form->field($model, 'publish')->checkbox(['value'=>1]) ?>

        <button type="submit" name="save" class='btn save btn-success'>Сохранить</button>
        <?php if ($model->id > 0) {
            echo Html::a('Создать Урок', [
                'course/lesson/'.$lesson['id'],
                'course_id'=>$model->course_id,
                'module_id'=>$model->id
            ], ['class' => 'btn btn-primary']);
        } ?>
    <?php ActiveForm::end(); ?>

    <div class="lessons" style="margin-top: 10px;">
    <?php if (count($model->lessons) > 0): ?>
        <ul class="list-group">
            <? foreach ($model->lessons as $lesson) :?>
            <li class="list-group-item <?php if ($lesson['publish']) {echo 'publish';} ?>" number='<?=$lesson['id']?>'>
                <span class="delete deleteLesson">Х</span>
                <a href="<?= Url::to(['course/lesson/'.$lesson['id'], 'course_id'=>$model->course_id, 'module_id'=>$model->id]) ?>"><?= $lesson['title'] ?></a>
            </li>
            <? endforeach; ?>
        </ul>
    <?php endif; ?>
    </div>
</div>

<?php

$js = <<<JS
    $('.deleteLesson').click(function(e) {
        let th = $(this);
        let id = th.parent().attr('number');

        if (confirm('Вы уверены, что хотите удалить этот модуль?')) {
            let data = {};
                data['id'] = id;
            $.ajax({
                url: "/closedoor/course/delete-lesson",
                type: 'POST',
                data: data,
                success: function(data) {
                    if (data == 1)
                        th.parent().remove();
                    else
                        console.log('Что-то пошло не так!');
                },
                error: function() {
                    alert('Ошибка...');
                }
            });
        }
    });
JS;
$this->registerJs($js);

?>
