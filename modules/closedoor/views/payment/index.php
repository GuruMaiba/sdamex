<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\PayType;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Оплаченные транзакции';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <? //Html::a('Create Payment', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'options' => ['width' => 80],
            ],
            [
                'attribute' => 'user_id',
                'options' => ['width' => 80],
            ],
            [
                'attribute' => 'payment_id',
                'options' => ['width' => 150],
            ],
            [
                'attribute' => 'model_id',
                'options' => ['width' => 80],
            ],
            [
                // 'label' => 'Статус',
                'attribute' => 'type',
                'options' => ['width' => 100],
                'filter' => PayType::getTypesArr(),
                'filterInputOptions' => ['prompt' => 'Все', 'class' => 'form-control', 'id' => null],
                'value' => function($model) {
                    switch ($model->type) {
                        case PayType::COURSE:
                            return 'Курс';
                            break;

                        case PayType::WEBINAR:
                            return 'Вебинар';
                            break;

                        case PayType::LESSON:
                            return 'Урок';
                            break;
                    }
                },
            ],
            'desc',
            [
                'attribute' => 'updated_at',
                'options' => ['width' => 120],
                'format' => ['date', 'php:d.m.Y H:i'],
            ],
            'code',
            [
                'attribute' => 'amount',
                'options' => ['width' => 100],
            ],
            //'extra_options',
            // 'success',
            [
                'class' => 'yii\grid\ActionColumn',
                // 'header'=>'Действия',
                'headerOptions' => ['width' => '30'],
                'template' => '{view}',
            ], // column - action
        ],
    ]); ?>
</div>
