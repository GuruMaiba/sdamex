<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\MainAsset;

MainAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Url::to(['/logo.ico'])]);

// Передаём переменную с путём к картинке, для изменения фона
// $customImg = false;
// if (isset($this->params['customImg'])) { $customImg = true; }

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
	<title>SDAMEX | ОПЛАТА</title>
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <?= Html::csrfMetaTags() ?>

    <link rel="preload" href="/css/icons.css" as="style">

    <?php $this->head() ?>
</head>

<body id="top"><?php $this->beginBody() ?>

    <!-- header 
    ================================================== -->
    <header>

        <div class="back">Вернуться</div>

        <div class="tabs">
            <div class="item course">Курсы</div>
            <div class="item webinar">Вебинары</div>
            <div class="item lesson">Уроки</div>
        </div>

        <div class="signin">Войти</div>

    </header> <!-- /header -->

    <section class="tab course">
        <input id="course_id" type="hidden" value="1">
        <div class="name">ОГЭ</div>
        <div class="duration">
            <div class="item active">1 месяц</div>
            <div class="item">полгода</div>
            <div class="item">год</div>
        </div>
        <div class="cost">2000р</div>
        <div class="btn payCourse">Оплатить</div>
    </section>

    <section class="tab webinar">
        <input id="webinar_id" type="hidden" value="1">
        <div class="name">Как сдать ЕГЭ и остаться в живых!</div>
        <div class="cost">140р</div>
        <div class="btn payWebinar">Оплатить</div>
    </section>

    <section class="tab lesson">
        <lable for="count_lessons">Введите необходимое количество занятий</lable>
        <input id="count_lessons" type="number" value="1">
        <div class="cost">140р</div>
        <div class="btn payLesson">Оплатить</div>
    </section>

    <div id="preloader"> 
        <div id="loader"></div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script type="text/javascript">
</script>