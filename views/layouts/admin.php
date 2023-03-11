<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AdminAsset;
use mdm\admin\components\Helper;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="/logo.ico" type="image/x-icon">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menu = [];
    if (Yii::$app->user->can('admin'))
        $menu[] = ['label' => 'Пользователи', 'url' => ['user/index']];
    if (Yii::$app->user->can('financier'))
        $menu[] = ['label' => 'Покупки', 'url' => ['payment/index']];
    if (Yii::$app->user->can('speaker') || Yii::$app->user->can('mainTeacher'))
        $menu[] = ['label' => 'Вебинары', 'url' => ['webinar/index']];
    if (Yii::$app->user->can('mainTeacher'))
        $menu[] = ['label' => 'Курсы', 'url' => ['course/index']];
    if (Yii::$app->user->can('assistant') || Yii::$app->user->can('moderator')) // mainTeacher
        $menu[] = ['label' => 'Тестирование', 'url' => ['fullexam/index']];
    if (Yii::$app->user->identity->role->name == 'checkTeacher')
        $menu[] = ['label' => 'Проверка практических', 'url' => ['check/index']];

    $menu[] = (Yii::$app->user->isGuest) ? (
        ['label' => 'Войти', 'url' => ['/account/login']]
    ) : (
        ['label' => 'Выйти (' . Yii::$app->user->identity->username . ')', 'url' => ['/account/logout'], 'class' => 'btn btn-link logout']
    );

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menu,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => 'Админка',
                'url' => ['/closedoor'],
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<!-- <script type="text/javascript">

</script> -->
