<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = ($model->title != null) ? $model->title : 'Урок';

$this->params['breadcrumbs'][] = [
        'label' => 'Курсы',
        'url' => ['course/index'],
    ];
$this->params['breadcrumbs'][] = [
        'label' => 'Курс',
        'url' => ['course/details/'.$course],
    ];
$this->params['breadcrumbs'][] = [
        'label' => 'Модуль',
        'url' => ['course/module/'.$model->module_id, 'course_id'=>$course],
    ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="examtest-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'lessonForm',
        'method' => 'post',
        // 'action' => ['course/create-lesson'],
        // 'options' => [
            // 'enctype' => 'multipart/form-data'
        // ],
    ]); ?>

        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'module_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'place')->input('number') ?>
        <?= $form->field($model, 'video')->textInput() ?>
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

        <hr>
        <div class="links">
            <span class="addLink" style="cursor: pointer;">Добавить материалы</span>
            <div class="list">
                <?php foreach (json_decode($model->links, true) as $i => $val) : ?>
                    <?= ( $i == 0 ) ? '<br>' : '' ?>
                    <div class="item">
                        <div class="delete" style="cursor: pointer;">X</div>
                        <input type="text" class="form-control" name="Lesson[links][<?=$i?>][name]" value="<?=$val['name']?>" placeholder="Название">
                        <input type="text" class="form-control" name="Lesson[links][<?=$i?>][link]" value="<?=$val['link']?>" placeholder="Ссылка">
                    </div><br>
                <?php endforeach; ?>
            </div>
        </div>
        <hr>

        <?= $form->field($model, 'free')->checkbox(['value'=>1]) ?>
        <?= $form->field($model, 'publish')->checkbox(['value'=>1]) ?>
        <button type="submit" name="save" class="btn save btn-success">Сохранить</button>
        <?php if ($model->id > 0) :?>
            <?php
            if ($model->examtest_id > 0) {
                $text = 'Редактировать тест';
                $link = 'exam/test/'.$model->examtest_id;
            } else {
                $text = 'Добавить тест';
                $link = 'exam/test';
            }
            $link = [$link, 'course_id'=>$course, 'module_id'=>$model->module_id, 'lesson_id'=>$model->id];
            echo Html::a($text, $link, ['class' => 'btn btn-primary']);
            ?>
            <?php
            if ($model->examwrite_id > 0) {
                $text = 'Редактировать письменное задание';
                $link = 'exam/write/'.$model->examwrite_id;
            } else {
                $text = 'Добавить письменное задание';
                $link = 'exam/write';
            }
            $link = [$link, 'course_id'=>$course, 'module_id'=>$model->module_id, 'lesson_id'=>$model->id];
            echo Html::a($text, $link, ['class' => 'btn btn-primary']);
            ?>
        <?php endif; ?>

    <?php ActiveForm::end(); ?>
</div>

<?php

$js = <<<JS
    $('.addLink').click(function () {
        let count = $('.links .list .item').length;
        let item = (count == 0) ? '<br>' : '';
        item += `<div class="item">`
            + `<div class="delete" style="cursor: pointer;">X</div>`
            + `<input type="text" class="form-control" name="Lesson[links][`+count+`][name]" placeholder="Название">`
            + `<input type="text" class="form-control" name="Lesson[links][`+count+`][link]" placeholder="Ссылка">`
        + `</div><br>`;
        $('.links .list').append(item);
    });

    $('.links .list').on('click', '.item .delete', function () {
        $(this).parent().remove();
    });
JS;
$this->registerJs($js);

?>
