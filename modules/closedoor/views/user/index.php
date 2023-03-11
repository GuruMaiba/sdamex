<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\components\UserStatus;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Промоутеры', ['user/index', 'role'=> ($_GET['role'] == 'promoter') ? 'all' : 'promoter'],
                ['class' => ($_GET['role'] == 'promoter') ? 'btn btn-info' : 'btn btn-primary'
            ]) ?>
        <?= Html::a('Учителя', ['user/index', 'role'=> ($_GET['role'] == 'teacher') ? 'all' : 'teacher'],
            ['class' => ($_GET['role'] == 'teacher') ? 'btn btn-info' : 'btn btn-primary'
        ]) ?>
        <?= Html::a('Редактировать уровни', ['level/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                // 'label' => 'ID',
                'attribute' => 'id',
                'options' => ['width' => 80],
            ],
            'username',
            'email:email',
            [
                // 'label' => 'Фамилия',
                'attribute' => 'surname',
                'options' => ['width' => 150],
            ],
            [
                // 'label' => 'Статус',
                'attribute' => 'status',
                'filter' => UserStatus::getStatusArr(),
                'filterInputOptions' => ['prompt' => 'Все', 'class' => 'form-control', 'id' => null],
                'value' => function($model) {
                    switch ($model->status) {
                        case UserStatus::INACTIVE:
                            return 'Не подтверждена почта';
                            break;

                        case UserStatus::PERMANENT_BAN:
                            return 'Вечный бан';
                            break;

                        case UserStatus::TEMPORARY_BAN:
                            return 'Временный бан';
                            break;

                        case UserStatus::LOGIN_ERROR:
                            return 'Ошибка авторизации';
                            break;

                        case UserStatus::ACTIVE:
                            return 'Активный';
                            break;
                    }
                },
            ],
            // 'updated_at:datetime',
            [
                // 'label' => 'Last active',
                'attribute' => 'updated_at',
                'options' => ['width' => 150],
                'format' => ['date', 'php:d.m.Y H:i:s'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'headerOptions' => ['width' => '90'],
                'template' => '{profile} {update} {delete} {ban}',
                'buttons' => [
                    // 'profile' => function ($url,$model,$key) {
                    //     return Html::a(
                    //         '<span class="glyphicon glyphicon-eye-open"></span>',
                    //         Url::toRoute(['user/profile', 'id' => $model->id])
                    //     );
                    // },
                    'update' => function ($url,$model,$key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            Url::toRoute(['user/create', 'id' => $model->id])
                        );
                    },
                    'delete' => function ($url,$model,$key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            Url::toRoute(['user/delete', 'id' => $model->id]),
                            ['class'=>'del']
                        );
                    },
                    'ban' => function ($url,$model,$key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-lock"></span>',
                            Url::toRoute(['ban/create', 'id' => $model->id])
                        );
                    },
    	        ], // buttons
            ], // column - action
        ], // columns
    ]); ?>
</div>

<?php
// $js = <<<JS
//     $('.del').click(function (e) {
//         if (!confirm('Вы уверены, что хотите ПОЛНОСТЬЮ удалить этого пользователя?')) {
//             return false;
//         }
//     });
// JS;
// $this->registerJs($js);
?>
