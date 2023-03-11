<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\exam\{EWQuestion};

/* @var $this yii\web\View */
/* @var $model app\models\Examwrite */

$this->title = 'Практическое упражнение';

if ($model->exercise_id > 0) {
    $this->params['breadcrumbs'][] = ['label' => 'Список экзаменов', 'url' => ['fullexam/index']];
    $this->params['breadcrumbs'][] = ['label' => 'Текущий экзамен', 'url' => ['fullexam/create', 'id'=>$ids['fullexam']]];
    $this->params['breadcrumbs'][] = [
        'label' => 'Задание',
        'url' => ['fullexam/exercise', 'id'=>$ids['exercise']]
    ];
} else if ($model->lesson_id > 0) {
    $this->params['breadcrumbs'][] = [
        'label' => 'Курсы',
        'url' => ['course/index']
    ];
    $this->params['breadcrumbs'][] = [
        'label' => 'Курс',
        'url' => ['course/details', 'id'=>$ids['course']]
    ];
    $this->params['breadcrumbs'][] = [
        'label' => 'Модуль',
        'url' => ['course/module', 'id'=>$ids['module'], 'course_id'=>$ids['course']]
    ];
    $this->params['breadcrumbs'][] = [
        'label' => 'Урок',
        'url' => ['course/lesson', 'id'=>$ids['lesson'], 'module_id'=>$ids['module'], 'course_id'=>$ids['course']]
    ];
} else if ($model->webinar_id > 0) {
    $this->params['breadcrumbs'][] = ['label' => 'Вебинары', 'url' => ['webinar/index']];
    $this->params['breadcrumbs'][] = [
        'label' => 'Вебинар',
        'url' => ['webinar/details', 'id'=>$ids['webinar']]
    ];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="examwrite-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="createQuestions">
        <?php $form = ActiveForm::begin([
            'id' => 'questionsForm',
            'method' => 'post',
            // 'action' => ['/admin/examwrite/add-questions'],
            // 'options' => [
                // 'class' => 'form-horizontal',
                // 'enctype' => 'multipart/form-data'
            // ],
        ]); ?>
            <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'exercise_id')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'lesson_id')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'webinar_id')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'task')->textarea(['rows'=>4]); ?>
            <?= $form->field($model, 'text')->widget(\vova07\imperavi\Widget::className(), [
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
            <?= $form->field($model, 'track')->fileInput(); ?>
            <?= $form->field($model, 'exp')->input('number') ?>
            <?php if ($model->audio_name != null) : ?>
            <audio controls controlsList="nodownload">
                <source src="/<?=Url::to('@audioFolder/'.$model->audio_name)?>" type="audio/mpeg">
            </audio>
            <?php endif; ?>
            <?= $form->field($model, 'publish')->checkbox() ?>
            <hr>
            <?= $form->field($model, 'themes')->hiddenInput()->label(false); ?>
            <?= $this->render('/theme/_themes', [
                'model' => $themes,
                'hide' => true,
                'active' => json_decode($model->themes, true)
            ]); ?>
            <hr>
            <!-- echo Html :: csrfMetaTags(); -->
            <button class="btn btn-success" type="submit" name="button">Сохранить</button>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="debug"></div>
</div>

<?php

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
JS;
$this->registerJs($js);
// $this->registerJsFile('web/scripts/textSpan.js', ['depends'=>['app\assets\AdminAsset']]);

 ?>
