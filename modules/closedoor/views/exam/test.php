<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\exam\test\{Question, Answer};

/* @var $this yii\web\View */
/* @var $model app\models\Examtest */
$this->title = 'Создание тестовое задание';

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

<div class="examtest-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="createQuestions">
        <?php $form = ActiveForm::begin([
            'id' => 'questionsForm',
            'method' => 'post',
            // 'action' => ['/admin/examtest/add-questions'],
        ]); ?>
            <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'exercise_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'lesson_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'webinar_id')->hiddenInput()->label(false) ?>
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
            <?= $form->field($model, 'qst_exp')->input('number') ?>
            <?= $form->field($model, 'track')->fileInput() ?>
            <?php if ($model->audio_name != null) : ?>
            <audio controls controlsList="nodownload">
                <source src="/<?=Url::to("@audioFolder/".$model->audio_name)?>" type="audio/mpeg">
            </audio>
            <?php endif; ?>
            <?= $form->field($model, 'publish')->checkbox() ?>
            <?= $form->field($model, 'oneshot')->checkbox() ?>
            <hr />
            <div class="btn btn-primary btnAddQst">Добавить вопрос</div>
            <button class="btn btn-success" type="submit" name="button">Сохранить</button>
            <br />
            <br />
            <!-- echo Html :: csrfMetaTags(); -->
            <div class="questions">
                <?= $this->render('_tquestion', [
                    'model' => $model->questions,
                    'corr' => json_decode($model->correct_answers, true),
                    'themes' => $themes,
                    'i' => 1,
                ]) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="debug"></div>

</div>

<?php

$iStandard = 1000000;
$qst = $this->render('_tquestion', [
    'model' => [new Question()],
    'i' => $iStandard,
    'themes' => $themes,
    'str' => true,
]);
$ans = $this->render('_tanswer', [
    'model' => [new Answer()],
    'i' => $iStandard,
    'j' => $iStandard,
    'str' => true,
]);

$js = <<<JS
    $('.examtest-create').on('click', '.btnAddQst', function() {
        var lng = ++$('.questions .question').length;
        // var testID = $('#examtest-id').val();
        let qst = $qst;
        $('.questions').append(qst);
        iUpdateQuestion(lng, $iStandard);
    });

    $('.container').on('click', '.searchTheme .list .item', function() {
        let id = $(this).attr('number');
        let inpTheme = $(this).parents('.question').children('.qstThemes');
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

    $('.questions').on('click', '.btnAddAns', function() {
        var lng = ++$(this).siblings('.answers').children('.answer').length;
        var qstNum = $(this).parents('.question').attr('number');
        var ans = $ans;
        $(this).siblings('.answers').append(ans);
        iUpdateAnswer(lng, $iStandard, qstNum);
    });

    $('.questions').on('click', '.delete', function() {
        let parent = $(this).parent();
        let num = parseInt(parent.attr('number'), 10);
        if (parent.hasClass('question')) {
            $('.questions .question').each(function(index) {
                if ((num + 1) == $(this).attr('number')) {
                    iUpdateQuestion(num, num + 1);
                    ++num;
                }
            });
        } else if (parent.hasClass('answer')) {
            let qst = $(this).parents('.question');
            $('#question_'+qst.attr('number')+' .answer').each(function(index) {
                if ((num + 1) == $(this).attr('number')) {
                    iUpdateAnswer(num, num + 1, qst.attr('number'), qst.attr('number'));
                    ++num;
                }
            });
        }
        parent.remove();
    });

    function iUpdateQuestion(iNew, iOld) {
        // ID
        $("input[name='Test[questions]["+iOld+"][id]']")
            .attr('name', "Test[questions]["+iNew+"][id]");
        // TEXT-LABLE
        $('#question_'+iOld+' .control-label')
            .text('Вопрос-'+iNew)
            .attr('for', "question_"+iNew+"-text_edit");
        // PLACE
        $("input[name='Test[questions]["+iOld+"][place]']")
            .val(iNew)
            .attr('name', "Test[questions]["+iNew+"][place]");
        // POINTS
        $("input[name='Test[questions]["+iOld+"][points]']")
            .attr('name', "Test[questions]["+iNew+"][points]");
        // TEXT
        $('#question_'+iOld+'-text')
            .attr({
                'id': 'question_'+iNew+'-text',
                'name': "Test[questions]["+iNew+"][text]"
            });
        // THEMES
        $("input[name='Test[questions]["+iOld+"][themes]']")
            .attr('name', "Test[questions]["+iNew+"][themes]");
        // MULTIPLE_ANSWER
        $("input[name='Test[questions]["+iOld+"][multiple_answer]']")
            .attr('name', "Test[questions]["+iNew+"][multiple_answer]");
        // HARD
        $("input[name='Test[questions]["+iOld+"][hard]']")
            .attr('name', "Test[questions]["+iNew+"][hard]");
        // Перебираем ответы и меняем номер вопроса
        $(".answers .answer").each(function() {
            let num = $(this).attr('number');
            iUpdateAnswer(num, num, iNew, iOld);
        });
        // DIV - ID
        $('#question_'+iOld).attr({'id':'question_'+iNew, 'number':iNew});
    }

    function iUpdateAnswer(iNew, iOld, iQst, iQOld = $iStandard) {
        // ID
        $("input[name='Test[questions]["+iQOld+"][answers]["+iOld+"][id]']")
            .attr('name', "Test[questions]["+iQst+"][answers]["+iNew+"][id]");
        // CORRECT
        $("input[name='Test[questions]["+iQOld+"][answers]["+iOld+"][correct]']")
            .attr('name', "Test[questions]["+iQst+"][answers]["+iNew+"][correct]");
        // TEXT-LABLE
        $('#answer_'+iOld+' .control-label')
            .text('Ответ-'+iNew)
            .attr('for', "qst_"+iQst+"-ans_"+iNew+"-text_edit");
        // TEXT
        $('#qst_'+iQOld+"-ans_"+iOld+'-text')
            .attr({
                'id': 'qst_'+iQst+"-ans_"+iNew+'-text',
                'name': "Test[questions]["+iQst+"][answers]["+iNew+"][text]"
            });
        // DIV - ID
        $('#answer_'+iOld).attr({'id':'answer_'+iNew, 'number':iNew});
    }
JS;
$this->registerJs($js);
// $this->registerJsFile('web/scripts/textSpan.js', ['depends'=>['app\assets\AdminAsset']]);

 ?>
