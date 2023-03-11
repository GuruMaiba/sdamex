<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\{UserStatus, CodeType};

/* @var $this yii\web\View */
if ($model->username != null) {
    $this->title = $model->username;
} else {
    $this->title = 'Создать пользователя';
}
$this->params['breadcrumbs'][] = [
        'label' => 'Пользователи',
        'url' => ["user/index"],
    ];
$this->params['breadcrumbs'][] = $this->title;
$url = ($model->id) ? 'user/create/' . $model->id : 'user/create';
$this->registerCssFile( "/css/jquery.Jcrop.css", ['rel'=>'stylesheet'], 'jcropCSS' );
?>
<div class="userPage">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="crossPhoto" style="display:none;">
        <div class="crossPhotoImg">
            <img id="cropbox" style="max-width:100%;" src="" />
        </div>
        <div class="btn btn-danger cancel">Отменить</div>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'userForm',
        'method' => 'post',
        'action' => [$url],
        'options' => [
            'enctype' => 'multipart/form-data'
        ],
    ]); ?>
        <input type="hidden" name="Coords[X]" id="X" value="0" />
        <input type="hidden" name="Coords[Y]" id="Y" value="0" />
        <input type="hidden" name="Coords[W]" id="W" value="200" />
        <input type="hidden" name="Coords[H]" id="H" value="200" />
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'ava')->hiddenInput()->label(false) ?>
        <?php if ($model->ava != null): ?>
            <img src="/<?= Yii::getAlias("@uAvaSmall/$model->ava") ?>" style="max-width: 200px;">
        <?php endif; ?>
        <?= $form->field($model, 'image')->fileInput() ?>
        <?= $form->field($model, 'username')->textInput() ?>
        <?= $form->field($model, 'email')->textInput() ?>
        <?= $form->field($model, 'password')->textInput() ?>
        <?= $form->field($model, 'phone')->textInput() ?>
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'surname')->textInput() ?>
        <?= $form->field($model, 'phrase')->textarea(['rows'=>6]) ?>
        <?= $form->field($model, 'skype')->textInput() ?>
        <?php //$form->field($model, 'status')->dropDownList(UserStatus::getStatusArr()); ?>
        <?= $form->field($model, 'cash')->input('number') ?>
        <?= $form->field($model, 'teacher_class')->textInput() ?>
        <?php
        if ($model->role != 'MegaAdmin') {
            $option = [];
            if ($model->role == null) {
                $option = ['options' => ['user'=>['selected'=>true]] ];
            }
            echo $form->field($model, 'role')->dropDownList($model->roles, $option);
        } ?>
        <?php if ($model->role == 'promoter'): ?>
        <?= $form->field($model, 'seller_id')->input('number') ?>
        <?php endif; ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn save btn-success']) ?>
    <?php ActiveForm::end(); ?>
    
    <div class="courses">
        <h1>Доступ к курсам</h1>
        <div class="list">
            <?
            $gmt = (!empty($_COOKIE['GMT'])) ? ($_COOKIE['GMT']*3600) : 0;
            foreach ($courses as $course) :
                $subject = Yii::$app->params['listSubs'][$course['subject_id']];
                $isActive = false;
                $end = '';
                foreach ($accesses as $acc) {
                    if ($acc['course_id'] == $course['id']) {
                        $isActive = true;
                        $end = date('d.m.Y H:i', ($_COOKIE['GMT']*3600) + $acc['end_at']);
                        break;
                    }
                }
            ?>
            <div class="item <?=($isActive)?'active':''?>" numb="<?=$course['id']?>">
                <div class="end">
                    <input class="date" type="text" placeholder="12.12.2020" value="<?=$end?>">
                </div>
                <span class="name"><?=$course['title']?> (<?=$subject['lable']?>)</span>
            </div>
            <? endforeach; ?>
        </div>
    </div>

    <?php if (($model->role == 'teacher' || $model->role == 'mainTeacher') && $model->teacher_option) : ?>
    <div class="teacher">
        <h1>Настройки учителя</h1>

        <?php $form = ActiveForm::begin([
            'id' => 'teacherForm',
            'method' => 'post',
            'action' => ['user/teacher-option'],
        ]); ?>
            <?= $form->field($model->teacher_option, 'user_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model->teacher_option, 'video')->textInput() ?>

            <?= $form->field($model->teacher_option, 'subjects')->hiddenInput() ?>
            <div class="subjects">
                <?php foreach ($model->subjects as $sId => $sub) : ?>
                    <div class="item <?=($sub['isActive'])?'active':''?>" numb="<?=$sId?>"><?=$sub['name']?></div>
                <?php endforeach; ?>
            </div>

            <?= $form->field($model->teacher_option, 'specialization')->hiddenInput() ?>
            <div class="specialization">
                <?php foreach ($model->specialization as $val => $status) : ?>
                    <div class="item <?=$status?>"><?=$val?></div>
                <?php endforeach; ?>
            </div>

            <?= $form->field($model->teacher_option, 'about_me')->textarea(['rows'=>6]) ?>
            <?= $form->field($model->teacher_option, 'time_lock')->input('number') ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn save btn-success']) ?>
        <?php ActiveForm::end(); ?>
    </div>
    <?php endif; ?>

    <?php if ($model->role != 'user') : ?>
    <div class="promoter">
        <h1>Настройки промоутера</h1>

        <?php $form = ActiveForm::begin([ 'id' => 'inviteForm', ]); ?>
            <div class="newCode">
                <h3>Добавление и редактирование промокодов</h3>
                <hr>
                <?= $form->field($code, 'promoter_id')->hiddenInput(['value'=>$model->id])->label(false) ?>
                <?= $form->field($code, 'old_code')->hiddenInput()->label(false) ?>
                <?= $form->field($code, 'code')->textInput(['placeholder'=>'Введите промокод', 'autocomplete'=>'off']) ?>
                <?php
                    $option = [];
                    if (!$code->type)
                        $option = ['options' => [CodeType::ALL=>['selected'=>true]] ];
                    echo $form->field($code, 'type')->dropDownList(CodeType::getTypesArr(), $option);

                    echo $form->field($code, 'reward')->textInput([
                        'type' => 'number',
                        'placeholder'=>'В рублях',
                        'autocomplete'=>'off'
                    ]);
                ?>
                <?= $form->field($code, 'str_date')->textInput(['placeholder'=>'20.12.2020', 'autocomplete'=>'off']) ?>
                <div class="btn btn-success saveCode">Сохранить</div>
                <div class="btn btn-danger delCode hidden">Удалить</div>
                <hr>
            </div>
        <?php ActiveForm::end(); ?>

        <div class="codes">
            <?php if (count($model->promoter_codes) > 0) {
                echo $this->render('_invitecode', [
                    'codes' => $model->promoter_codes
                ]);
            } ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$csrf = Yii::$app->getRequest()->getCsrfToken();
$js = <<<JS
let csrf = '$csrf';
$(document).ready(function () {
    $('#adminuser-phone').mask('+0 (000) 000-00-00');

    if (window.File && window.FileReader && window.FileList && window.Blob) {

        var control = document.getElementById("adminuser-image");
        var backup = $(".crossPhoto").html();

        control.addEventListener('change', function () {
            var file = control.files[0];
            var reader = new FileReader();
            var exp = file.type.toString().toLowerCase();

            if (file != null) {

                reader.addEventListener("load", function () {
                    var img = new Image;
                    $(".crossPhoto").html(backup);
                    img.onload = function () {
                        if (file.size < 10000000 && (exp.indexOf("image") !== -1)) {
                            if (img.height < 200 || img.width < 200) {
                                alert("Ваше изображение должно быть больше 200px со всех сторон!");
                            } else if (img.height == img.width) {
                                $('#X').val(0);
                                $('#Y').val(0);
                                $('#W').val(img.width);
                                $('#H').val(img.height);
                                $("#cropbox").attr('src', reader.result);
                                // $(".clip").click();
                            } else {
                                if ((img.width < img.height) && (img.width > $(window).width()/2) && ($(window).width() > 767)) {
                                    $(".crossPhotoImg").width("50%");
                                } else if (img.width < $(".crossPhoto").width()) {
                                    $(".crossPhotoImg").width(img.width);
                                }
                                $("#cropbox").attr('src', reader.result);
                                $(".crossPhoto").slideDown();
                                $('#cropbox').Jcrop({
                                    onSelect: updateCoords,
                                    aspectRatio: 1 / 1,
                                    setSelect: [0, 0, 200, 200],
                                    boxHeight: $("#cropbox").height(),
                                    boxWidth: $("#cropbox").width()
                                });

                                $('html, body').animate({
                                    scrollTop: $(".crossPhoto").offset().top
                                }, 700);
                            }
                        }
                    };
                    img.src = reader.result;
                }, false);

                if (file) {
                    reader.readAsDataURL(file);
                }
            }

        }, false);

    } else {
        alert('Извините, но Ваш браузер не поддерживает FileAPI. Зайдите с более новой версии браузера для изменения аватарки!');
    }

    $("body").on("click", ".cancel", function () {
        $('.crossPhoto').slideUp(500);
        $('#adminuser-image').val(null);
        setTimeout(function () {
            $(".crossPhoto").html(backup);
        }, 500);
    });

    $('.courses .item').click(function (e) {
        let th = $(this);

        if (th.hasClass('disable') || e.target.className == 'date')
            return false;

        let userId = parseInt($('#adminuser-id').val());
        let courseId = parseInt(th.attr('numb'));

        if (!(userId > 0) || !(courseId > 0))
            return false;

        th.addClass('disable');
        $.post( '/closedoor/user/add-course', {
                '_csrf': csrf,
                'user_id': userId,
                'course_id': courseId,
            })
            .done(function( data, status, jqXHR ) {
                // console.log(data);
                if (data != 0) {
                    if (data == 1) {
                        th.removeClass('active');
                    } else {
                        th.children('.end').children('.date').val(data);
                        th.addClass('active');
                    }
                } else
                    globalError();

                th.removeClass('disable');
            })
            .fail(function( jqXHR, status, errorThrown ){
                ajaxError(errorThrown, jqXHR);
            });
    });

    $('.courses .item .date').change(function () {
        let th = $(this);

        let userId = parseInt($('#adminuser-id').val());
        let courseId = parseInt(th.parents('.item').attr('numb'));

        if (!(userId > 0) || !(courseId > 0))
            return false;

        th.attr('disabled','disabled');
        $.post( '/closedoor/user/access-time', {
                '_csrf': csrf,
                'user_id': userId,
                'course_id': courseId,
                'date': th.val()
            })
            .done(function( data, status, jqXHR ) {
                console.log(data);
                if (data == 0)
                    globalError();

                th.removeAttr("disabled");
            })
            .fail(function( jqXHR, status, errorThrown ){
                ajaxError(errorThrown, jqXHR);
            });
    });

    $('.subjects .item').click(function () {
        $(this).toggleClass('active');
    });
    
    $('.specialization .item').click(function () {
        $(this).toggleClass('active');
    });

    $("#teacherForm .save").click(function () {
        let arrSubs = [];
        $('.subjects .item').each(function (i) {
            if ($(this).hasClass('active'))
                arrSubs.push($(this).attr('numb'));
        });
        $('#teacher-subjects').val(JSON.stringify(arrSubs));

        let arrSpec = [];
        $('.specialization .item').each(function (i) {
            if ($(this).hasClass('active'))
                arrSpec.push($(this).text());
        });
        $('#teacher-specialization').val(JSON.stringify(arrSpec));
    });

    $('.codes').on('click', '.code', function () {
        $('#code-old_code').val($(this).attr('id'));
        $('#code-code').val($(this).attr('id'));
        $('#code-reward').val($(this).attr('reward'));
        $('#code-type option[value='+$(this).attr('type')+']').prop('selected', true);
        $('#code-str_date').val($(this).attr('strdate'));
        $('.delCode').removeClass('hidden');
    });

    $('.promoter .saveCode').click(function () {
        let btns = $('.newCode .btn');
        if (btns.hasClass('disable'))
            return false;

        btns.addClass('disable');
        let old = $('#code-old_code').val();
        let data = $('#inviteForm').serialize();
        $.post( '/closedoor/user/code', data)
            .done(function( data, status, jqXHR ) {
                if (data != 0) {
                    cleanCodeForm();
                    if (old != '')
                        $('#'+old).remove();
                    $('.codes').prepend(data);
                } else
                    globalError('Промокод занят или Вы нарушили правила ввода!');

                btns.removeClass('disable');
            })
            .fail(function( jqXHR, status, errorThrown ){
                ajaxError(errorThrown, jqXHR);
            });
    });

    $('.promoter .delCode').click(function () {
        let btns = $('.newCode .btn');
        if (btns.hasClass('disable'))
            return false;

        btns.addClass('disable');
        let old = $('#code-old_code').val();
        $.post( '/closedoor/user/del-code', {'old_code': old})
            .done(function( data, status, jqXHR ) {
                cleanCodeForm();
                $('#'+old).remove();
                btns.removeClass('disable');
            })
            .fail(function( jqXHR, status, errorThrown ){
                ajaxError(errorThrown, jqXHR);
            });
    });

    function updateCoords(c) {
        $('#X').val(c.x);
        $('#Y').val(c.y);
        $('#W').val(c.w - 1);
        $('#H').val(c.h - 1);
    };

    function cleanCodeForm() {
        $('#code-old_code').val('');
        $('#code-code').val('');
        $('#code-reward').val(0);
        $('#code-type option[value=1]').prop('selected', true);
        $('#code-str_date').val('');
        $('.delCode').addClass('hidden');
    };
});
JS;
$this->registerJs($js);
$this->registerJsFile( '@scrLibs/jquery.Jcrop.js', ['depends' => ['app\assets\AdminAsset'],], 'jcropJS' );
$this->registerJsFile( '@scrLibs/jquery.mask.min.js', ['depends' => ['app\assets\AdminAsset'],], 'maskJS' );

?>
