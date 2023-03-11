<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\exam\{ETQuestion, ETAnswer};

/* @var $this yii\web\View */
/* @var $model app\models\Examtest */
$this->title = 'Упражнение с пропусками';

$this->params['breadcrumbs'][] = ['label' => 'Список экзаменов', 'url' => ['fullexam/index']];
// $this->params['breadcrumbs'][] = ['label' => 'Текущий экзамен', 'url' => ['fullexam/create', 'id'=>1]];
$this->params['breadcrumbs'][] = [
    'label' => 'Задание',
    'url' => ['fullexam/exercise', 'id'=>$model->exercise_id]
];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="examaddition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="debug"></div>

    <hr>
    <?= $this->render('/theme/_themes', [ 'model' => $themes, 'hide' => true ]); ?>
    <hr>
    <?php $form = ActiveForm::begin([
        'id' => 'additionForm',
        'method' => 'post',
        // 'action' => ['/admin/examaddition/create'],
    ]); ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'exercise_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'task')->textarea(['rows'=>2]) ?>
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
        <?= $form->field($model, 'word_exp')->input('number') ?>
        <?= $form->field($model, 'publish')->checkbox() ?>

        <button class="btn btn-success" type="submit" name="button">Сохранить</button>
    <?php ActiveForm::end(); ?>
</div>

<?php

// $js = <<<JS
// JS;
// $this->registerJs($js);
// $this->registerJsFile('web/scripts/textSpan.js', ['depends'=>['app\assets\AdminAsset']]);

 ?>
