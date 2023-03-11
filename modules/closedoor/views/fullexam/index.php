<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Экзамены';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fullexamsPage">

    <h3><?= Html::encode($this->title) ?></h3>
    <hr>
    <div class="btns" >
        <a href="<?=Url::to(['fullexam/create'])?>" class="btn btn-success">Создать</a>    
        <a href="<?=Url::to(['theme/index'])?>" class="btn btn-primary">Редактировать темы</a>    
        <a href="<?=Url::to(['check/index'])?>" class="btn btn-primary">Проверка практических</a>    
    </div>

    <ul class="list-group" style="margin-top: 15px; width: 50%;">
    <?php if (count($model) > 0): ?>
        <?php foreach ($model as $exam) :?>
            <a class="listenLink" href="<?=Url::to(['fullexam/create/'.$exam['id']])?>">
            <li class="list-group-item defaultAdminItem <?=($exam['publish'])?'publish':''?>">
                <span class="delete">X</span>
                <span class="name"><?=$exam['name']?></span>
            </li>
            </a>
        <?php endforeach;?>
    <?php endif; ?>
    </ul>
</div>

<?php
$js = <<<JS
    // $('.listenLink').click(function(e) {
    //     let th = $(this);
    //     let id = th.attr('id');
    //     if ($(e.target).hasClass('delete')) {
    //         e.preventDefault();
    //         if (confirm('Вы уверены, что хотите удалить этот вебинар?')) {
    //             let data = {};
    //                 data['id'] = id;
    //             $.ajax({
    //                 url: "/admin/webinar/delete-webinar",
    //                 type: 'POST',
    //                 data: data,
    //                 success: function(data) {
    //                     if (data == 1) {
    //                         th.remove();
    //                     }
    //                 },
    //                 error: function() {
    //                     alert('Ошибка...');
    //                 }
    //             });
    //         }
    //     }
    // });
JS;
// $this->registerJs($js);
 ?>
