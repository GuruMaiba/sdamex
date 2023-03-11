<?php

// use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Проверка практических задайний';
$this->params['breadcrumbs'][] = ['label' => 'Список экзаменов', 'url' => ['fullexam/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="examsPage">

    <h3><?=$this->title?></h3>

    <hr>
    <div class="btns">
        <a href="<?=Url::to(['check/index', 'type'=>'write'])?>" class="btn btn-<?=($type == 'write')?'success':'primary'?>">Практические</a>    
        <a href="<?=Url::to(['check/index', 'type'=>'exam'])?>" class="btn btn-<?=($type == 'exam')?'success':'primary'?>">Полные экзамены</a>    
    </div>

    <ul class="list-group" style="margin-top: 15px;">
        <?php if ($model) : ?>
        <?php foreach ($model as $pract) :?>
        <?php
            $name = 'Пользователь '.$pract['user_id'];
            if ($type == 'write') {
                $url = Url::to(['check/practical/'.$pract['id']]);
                if ($pract['write']['exercise_id'] > 0) {
                    $name .= ' - Тестирование ('
                        .$pract['write']['exercise']['section']['fullexam']['name'].'/'
                        .$pract['write']['exercise']['section']['name'].'/'
                        .$pract['write']['exercise']['name'].')';
                } else if ($pract['write']['lesson_id'] > 0) {
                    $name .= ' - Курс ('
                        .$pract['write']['lesson']['module']['course']['title'].'/'
                        .$pract['write']['lesson']['module']['title'].'/'
                        .$pract['write']['lesson']['title'].')';
                }
                
            } else {
                $url = Url::to(['check/exam/'.$pract['id']]);
                $name .= ' - (Экзамен '.$pract['fullexam']['name'].')';
            }
            ?>
            <li class="list-group-item">
                <a href="<?=$url?>"><?=$name?></a>
            </li>
        <?php endforeach;?>
        <?php else : ?>
            <li class="list-group-item">Все работы проверены!</li>
        <?php endif; ?>
    </ul>
</div>

<?php
// $js = <<<JS
// JS;
// $this->registerJs($js);
 ?>
