<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Список всех упражнений';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="examsPage">

    <h3>Упражнения</h3>

    <hr>
    <div class="btns">
        <a href="<?=Url::to(['fullexam/create'])?>" class="btn btn-success">Создать</a>    
    </div>

    <ul class="list-group" style="margin-top: 15px; width: 50%;">
        <?php //foreach ($model as $key => $exam) :?>
            <!-- <li class="list-group-item"><a href="<?=1//Url::to(['fullexam/create/'.$exam['id']])?>"><?=1//$exam['name']?></a></li> -->
        <?php //endforeach;?>
    </ul>
    <!--  -->
</div>

<?php
$js = <<<JS
JS;
$this->registerJs($js);
 ?>
