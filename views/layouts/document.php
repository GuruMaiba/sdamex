<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\DocumentAsset;

DocumentAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Url::to(['/logo.ico'])]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<nav>
    <a href="/">
    <div class="logo">
        <img src="<?=Url::to(['@imgFolder/bl-or-logo.svg'])?>">
        <div class="name">SDAMEX <span>DOCUMENTS</span></div>
    </div>
    </a>
</nav>

<div class="container"> <?= $content ?> </div>

<footer>

</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
