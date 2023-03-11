<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Вебинары';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webinars">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <!-- <audio controls>
        <source src="<?= Url::to('@audioFolder/Test Audio.mp3') ?>" type="audio/mpeg">
    </audio> -->

    <?= Html::a('Создать Вебинар', ['details'], ['class' => 'btn btn-primary']) ?>

    <ul class="list-group" style="margin-top: 15px; width: 50%;">
    <?php if (count($model) > 0): ?>
        <?php foreach ($model as $webinar) :?>
            <a class="listenLink" href="<?=Url::to(['webinar/details', 'id' => $webinar['id']])?>">
            <li id="<?= $webinar['id'] ?>" class="list-group-item defaultAdminItem <?=($webinar['publish'])?'publish':''?>">
                <span class="delete">X</span>
                <span class="name"><?=$webinar['title']?></span>
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
            if (confirm('Вы уверены, что хотите удалить этот вебинар?')) {
                let data = {};
                    data['id'] = id;
                $.ajax({
                    url: "/closedoor/webinar/delete-webinar",
                    type: 'POST',
                    data: data,
                    success: function(data) {
                        if (data == 1) {
                            th.remove();
                        }
                    },
                    error: function() {
                        alert('Ошибка...');
                    }
                });
            }
        }
    });
JS;

$this->registerJs($js);
 ?>
