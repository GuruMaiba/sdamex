<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
if ($model->title != null) {
    $this->title = $model->title;
} else {
    $this->title = 'Создать вебинар';
}
$this->params['breadcrumbs'][] = [
        'label' => 'Вебинары',
        'url' => ["/webinar/index"],
    ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webinar">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'webinarForm',
        'method' => 'post',
        // 'action' => ['webinar/save-webinar'],
        'options' => [
            'enctype' => 'multipart/form-data'
        ],
    ]); ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?php if ($model->ava != null): ?>
            <img src="<?= Url::to(["@home/".Yii::getAlias("@webnAvaSmall/".$model->ava)]) ?>" style="max-width: 50%;">
        <?php endif; ?>
        <?= $form->field($model, 'image')->fileInput() ?>

        <?= $form->field($model, 'author_id')->input('number') ?>

        <?= $form->field($model, 'live_link')->textInput() ?>
        <?= $form->field($model, 'video_link')->textInput() ?>

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

        <?= $form->field($model, 'subject_id')->dropDownList($model->subjects, []) ?>

        <?php if (count($courses) > 0) : ?>
            <?php 
                $courses_id = [];
                foreach ($model->courses as $crs)
                    $courses_id[] = $crs->id;
            ?>
            <?= $form->field($model, 'courses_id')->hiddenInput(['value' => json_encode($courses_id)]) ?>

            <div class="courses">
                <?php
                foreach ($courses as $crs) :
                    $isActive = false;
                    foreach ((array)$model->courses as $actCrs) {
                        if ($actCrs->id == $crs->id) {
                            $isActive = true;
                            break;
                        }
                    }
                    $actSub = ($model->subject_id > 1) ? $model->subject_id : 1;
                    $sub = Yii::$app->params['listSubs'][$crs->subject_id];
                    ?>
                    <div class="item <?=($isActive)?'active':''?> <?=($actSub != $crs->subject_id)?'hidden':''?>"
                        numb="<?=$crs->id?>"
                        sub="<?=$crs->subject_id?>"><?="$crs[title] ($sub[lable])"?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <hr>
        <div class="links">
            <span class="addLink" style="cursor: pointer;">Добавить материалы</span>
            <div class="list">
                <?php foreach (json_decode($model->links, true) as $i => $val) : ?>
                    <?= ( $i == 0 ) ? '<br>' : '' ?>
                    <div class="item">
                        <div class="delete" style="cursor: pointer;">X</div>
                        <input type="text" class="form-control" name="Webinar[links][<?=$i?>][name]" value="<?=$val['name']?>" placeholder="Название">
                        <input type="text" class="form-control" name="Webinar[links][<?=$i?>][link]" value="<?=$val['link']?>" placeholder="Ссылка">
                    </div><br>
                <?php endforeach; ?>
            </div>
        </div>
        <hr>

        <?= $form->field($model, 'cost')->input('number') ?>

        <?= $form->field($model, 'strDate')->textInput(['placeholder'=>'31.12.2019 14:14']) ?>

        <?= $form->field($model, 'publish')->checkbox() ?>

        <?= Html::submitButton('Сохранить', ['class' => 'btn save btn-success']) ?>
        <?php if ($model->id > 0 && $model->subject_id > 0 && $model->publish) {
            if ($model->subject_id == 1) {
                foreach (Yii::$app->params['listSubs'] as $id => $subject) {
                    if ($subject['isActive'])
                        $sub = $subject;
                }
            } else
                $sub = Yii::$app->params['listSubs'][$model->subject_id];
            echo Html::a('Обзор', Url::to($sub['link'].'webinar/'.$model->id), ['class' => 'btn btn-primary']);
        } ?>
    <?php ActiveForm::end(); ?>
    <?php
        // if ($model->test != null) {
        //     $text = 'Редактировать тест';
        //     $link = ['test/create', 'id'=>$model->test_id];
        // } else {
        //     $text = 'Добавить тест';
        //     $link = ['test/create'];
        // }
        // echo Html::a($text, $link, ['class' => 'btn listenLink btn-primary']);

        // if ($model->write != null) {
        //     $text = 'Редактировать письменное задание';
        //     $link = ['write/create', 'id'=>$model->write_id];
        // } else {
        //     $text = 'Добавить письменное задание';
        //     $link = ['write/create'];
        // }
        // echo Html::a($text, $link, ['class' => 'btn listenLink btn-primary']);
     ?>
</div>

<?php

$js = <<<JS
    $('#webinar-subject_id').change(function () {
        let sub = parseInt($(this).val());
        $('.courses .item').removeClass('hidden active');
        $('.courses .item').each(function (i) {
            if ($(this).attr('sub') != sub)
                $(this).addClass('hidden');
        });
        $('#webinar-courses_id').val('');
    });

    $('.courses .item').click(function () {
        $(this).toggleClass('active');
    });

    $('.addLink').click(function () {
        let count = $('.links .list .item').length;
        let item = (count == 0) ? '<br>' : '';
        item += `<div class="item">`
            + `<div class="delete" style="cursor: pointer;">X</div>`
            + `<input type="text" class="form-control" name="Webinar[links][`+count+`][name]" placeholder="Название">`
            + `<input type="text" class="form-control" name="Webinar[links][`+count+`][link]" placeholder="Ссылка">`
        + `</div><br>`;
        $('.links .list').append(item);
    });

    $('.links .list').on('click', '.item .delete', function () {
        $(this).parent().remove();
    });

    $("#webinarForm .save").click(function () {
        let arrCrs = [];
        $('.courses .item').each(function (i) {
            if ($(this).hasClass('active'))
                arrCrs.push($(this).attr('numb'));
        });
        $('#webinar-courses_id').val(JSON.stringify(arrCrs));
    });
JS;
$this->registerJs($js);

?>
