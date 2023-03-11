<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\UserStatus;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Уровни';
$this->params['breadcrumbs'][] = [
    'label' => 'Пользователи',
    'url' => ['user/index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="level-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <table class="table table-striped table-bordered">
        <colgroup>
            <col width="150">
            <col>
            <col width="90">
        </colgroup>
        <thead>
            <tr>
                <th><input type="number" class="form-control searchLevel" placeholder="LVL"></th>
                <th>Количество опыта</th>
                <th>MAX</th>
            </tr>
        </thead>
        <tbody>
        <? foreach($levels as $level) : ?>
            <?= $this->render('_lvlRow', [
                'level' => $level,
            ]) ?>
        <? endforeach; ?>
        </tbody>
    </table>

    <div class="btn btn-primary addLvl">Добавить уровень</div>
</div>

<?php
$csrf = Yii::$app->getRequest()->getCsrfToken();

$js = <<<JS
    $(document).ready(function () {
        let csrf = '$csrf';

        $('.searchLevel').change(function (e) {
            let lvl = $(this).val();
            $('.table .lvlRow').removeClass('hidden');
            if (lvl > 0) {
                $('.table .lvlRow').each(function () {
                    if (lvl != $(this).attr('data-key'))
                        $(this).addClass('hidden');
                });
            }
        });

        $('.table').on('change', '.exp', function () {
            let lvl = $(this).parents('.lvlRow').attr('data-key');
            let exp = $(this).val();
            $.post( 'change-exp', {'_csrf':csrf, 'lvl':lvl, 'newExp':exp})
                .done(function( data, status, jqXHR ) {
                    if (data == 0)
                        ajaxError(status, jqXHR, 'В парамметрах ошибка, попробуйте позже!');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        $('.table').on('click', '.max', function () {
            if ($(this).attr('checked') == 'checked')
                return false;

            let lvl = $(this).parents('.lvlRow').attr('data-key');
            $.post( 'change-max', {'_csrf':csrf, 'lvl':lvl})
                .done(function( data, status, jqXHR ) {
                    console.log(data);
                    if (data == 0)
                        ajaxError(status, jqXHR, 'В парамметрах ошибка, попробуйте позже!');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        $('.addLvl').click(function () {
            $.post( 'add', {'_csrf':csrf})
                .done(function( data, status, jqXHR ) {
                    $('.table tbody').append(data);
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });
    });
JS;
$this->registerJs($js);
?>
