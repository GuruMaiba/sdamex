<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Курс';
if ($model->title != null) {
    $this->title = $model->title;
}

$this->params['breadcrumbs'][] = [
        'label' => 'Курсы',
        'url' => ["course/index"],
    ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'courseForm',
        'method' => 'post',
        // 'action' => ['course/create-course'],
        // 'options' => [
            // 'class' => 'form-horizontal',
            // 'enctype' => 'multipart/form-data'
        // ],
    ]); ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?php if ($model->ava != null): ?>
            <img style="max-width: 50%;" src="<?= Url::to(["@home/".Yii::getAlias("@crsAvaSmall/".$model->ava)]) ?>">
        <?php endif; ?>
        <?= $form->field($model, 'image')->fileInput() ?>
        <?= $form->field($model, 'author_id')->input('number') ?>
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
        <?= $form->field($model, 'author_desc')->widget(\vova07\imperavi\Widget::className(), [
                'settings' => [
                    'minHeight' => 150,
                    'plugins' => [
                        'fontsize',
                        'fullscreen',
                    ],
                ],
            ]) ?>
        <?= $form->field($model, 'subject_id')->dropDownList($model->subjects, []) ?>
        <?= $form->field($model, 'cost')->input('number') ?>
        <?= $form->field($model, 'free')->checkbox(['value'=>1]) ?>
        <?= $form->field($model, 'publish')->checkbox(['value'=>1]) ?>
        <button type="submit" name="save" class="btn save btn-success">Сохранить</button>
        <?php if ($model->id > 0) {
            echo Html::a('Создать Модуль',
                ['module', 'course_id'=>$model->id],
                ['class' => 'btn btn-primary']);
        } ?>
        <?php if ($model->id > 0 && $model->subject_id > 0 && $model->publish) {
            if ($model->subject_id == 1) {
                foreach (Yii::$app->params['listSubs'] as $id => $subject) {
                    if ($subject['isActive'])
                        $sub = $subject;
                }
            } else
                $sub = Yii::$app->params['listSubs'][$model->subject_id];
            echo Html::a('Обзор', Url::to($sub['link'].'course/'.$model->id), ['class' => 'btn btn-primary']);
        } ?>
    <?php ActiveForm::end(); ?>


    <div class="modules" style="margin-top: 10px;">
    <?php if (count($model->modules) > 0): ?>
        <ul class="list-group">
            <? foreach ($model->modules as $module) :?>
            <li class="list-group-item <?php if ($module['publish']) {echo 'publish';} ?>" number='<?=$module['id']?>'>
                <span class="delete deleteModule">Х</span>
                <a href="<?= Url::to(['course/module/'.$module['id'], 'course_id' => $model->id]) ?>"><?= $module['title'] ?></a>
            </li>
            <? endforeach; ?>
        </ul>
    <?php endif; ?>
    </div>
</div>

<?php

$js = <<<JS
    $('.deleteModule').click(function(e) {
        let th = $(this);
        let id = th.parent().attr('number');

        if (confirm('Вы уверены, что хотите удалить этот модуль?')) {
            let data = {};
                data['id'] = id;
            $.ajax({
                url: "/closedoor/course/delete-module",
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
