<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Темы';
$this->params['breadcrumbs'][] = ['label' => 'Список экзаменов', 'url' => ['fullexam/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="themesPage">

    <h3>Темы</h3>

    <hr>

    <?= $this->render('_themes', [ 'model' => $model, 'hide'=>false ]); ?>
</div>

<?php
$js = <<<JS
JS;
$this->registerJs($js);
 ?>
