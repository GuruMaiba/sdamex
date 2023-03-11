<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Курсы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="courses">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <?= Html::a('Создать Курс', ['details'], ['class' => 'btn btn-primary']) ?>

    <ul class="list-group" style="margin-top: 15px; width: 50%;">
    <?php if (count($model) > 0): ?>
        <?php foreach ($model as $course) :?>
            <a id="<?= $course['id'] ?>" class="listenLink" href="<?=Url::to(['course/details', 'id' => $course['id']])?>">
            <li class="list-group-item defaultAdminItem <?=($course['publish'])?'publish':''?>">
                <span class="delete">X</span>
                <span class="name"><?=$course['title']?></span>
            </li>
            </a>
        <?php endforeach;?>
    <?php endif; ?>
    </ul>
</div>

<?php

$js = <<<JS
    $('.listenLink').click(function(e) {
        let th = $(this);
        let id = th.attr('id');
        if ($(e.target).hasClass('delete')) {
            e.preventDefault();
            if (confirm('Вы уверены, что хотите удалить этот курс?')) {
                let data = {};
                    data['id'] = id;
                $.ajax({
                    url: "delete-course",
                    type: 'POST',
                    data: data,
                    success: function(data) {
                        if (data == 1)
                            th.remove();
                        else
                            globalError();
                    },
                    error: (jqXHR, status, errorThrown) => {ajaxError(errorThrown, jqXHR);}
                });
            }
        }
    });
JS;

$this->registerJs($js);
 ?>
