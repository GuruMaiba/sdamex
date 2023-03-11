<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\exam\correlate\Pair;

/* @var $this yii\web\View */
/* @var $model app\models\Examtest */
$this->title = 'Упражнение на соотношение';

$this->params['breadcrumbs'][] = ['label' => 'Список экзаменов', 'url' => ['fullexam/index']];
// $this->params['breadcrumbs'][] = ['label' => 'Текущий экзамен', 'url' => ['fullexam/create', 'id'=>1]];
$this->params['breadcrumbs'][] = [
    'label' => 'Задание',
    'url' => ['fullexam/exercise', 'id'=>$model->exercise_id]
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="examcorrelate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'correlateForm',
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data'],
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
        <?= $form->field($model, 'track')->fileInput() ?>
        <?php if ($model->audio_name != null) : ?>
        <audio controls controlsList="nodownload">
            <source src="/<?=Url::to('@audioFolder/'.$model->audio_name)?>" type="audio/mpeg">
        </audio>
        <?php endif; ?>
        <?= $form->field($model, 'qst_name')->textInput() ?>
        <?= $form->field($model, 'ans_name')->textInput() ?>
        <?= $form->field($model, 'pair_exp')->input('number') ?>
        <?= $form->field($model, 'qst_hidden')->checkbox() ?>
        <?= $form->field($model, 'publish')->checkbox() ?>

        <hr>
        <div class="btn btn-primary btnAddPair">Добавить соответствие</div>
        <button class="btn btn-success" type="submit" name="button">Сохранить</button>

        <div style="padding-left: 40px; margin-top: 15px;">
            <div class="pairs">
                <?= $this->render('_cpair', [
                    'model' => $model->pairs,
                    'i' => 1,
                    'active_themes' => json_decode($model->themes,true),
                    'all_themes' => $themes,
                ]) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
    <div class="debug"></div>
</div>

<?php

$iStandard = 1000000;
$pair = $this->render('_cpair', [
    'model' => [new Pair()],
    'i' => $iStandard,
    'str' => true,
    'all_themes' => $themes,
    ]);

$js = <<<JS
$('.btnAddPair').click(function() {
    var lng = ++$('.pairs .pair').length;
    let pair = $pair;
    $('.pairs').append(pair);
    iUpdatePair(lng, $iStandard);
});

$('.pairs').on('click', '.delete', function() {
    let parent = $(this).parents('.pair');
    let num = parseInt(parent.attr('number'), 10);
    $('.pairs .pair').each(function(index) {
        if ((num + 1) == $(this).attr('number')) {
            iUpdatePair(num, num + 1);
            ++num;
        }
    });
    parent.remove();
});

$('.container').on('click', '.searchTheme .list .item', function() {
    let id = $(this).attr('number');
    let inpTheme = $(this).parents('.pair').children('.pairThemes');
    let themes = inpTheme.val();
    if (themes == '')
        themes = [];
    else
        themes = JSON.parse(themes);

    if ($(this).hasClass('active'))
        themes.push(id);
    else
        themes = themes.filter(item => item != id);

    inpTheme.val(JSON.stringify(themes));
});

function iUpdatePair(iNew, iOld) {
    // ID
    $("input[name='Pairs["+iOld+"][id]']")
        .attr('name', "Pairs["+iNew+"][id]");
    // QST_TEXT
    $("textarea[name='Pairs["+iOld+"][qst_text]']")
        .attr({ 'name': "Pairs["+iNew+"][qst_text]" });
    // ANS_TEXT
    $("textarea[name='Pairs["+iOld+"][ans_text]']")
        .attr({ 'name': "Pairs["+iNew+"][ans_text]" });
    // THEMES
    $("input[name='Pairs["+iOld+"][themes]']")
        .attr({ 'name': "Pairs["+iNew+"][themes]" });
    // DIV - ID
    $('#pair_'+iOld).attr({'id':'pair_'+iNew, 'number':iNew});
}
JS;
$this->registerJs($js);

 ?>
