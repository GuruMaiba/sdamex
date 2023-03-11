<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\PayAsset;
use app\components\PayType;

PayAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Url::to(['/logo.ico'])]);

// Передаём переменную с путём к картинке, для изменения фона
// $customImg = false;
// if (isset($this->params['customImg'])) { $customImg = true; }

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="utf-8">
	<title><?= $this->title ?></title>
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <?= Html::csrfMetaTags() ?>

    <link rel="preload" href="/css/icons.css" as="style">

    <?php $this->head() ?>
</head>

<body id="top"><?php $this->beginBody() ?>

    <div class="bg">
		<div class="blackout"></div>
	</div>

	<div class="pagePayment">
		<div class="back" onclick="history.back();">Вернуться</div>
		<div class="signin">Войти</div>

		<!-- <div class="tabs">
			<div class="item course">Курсы</div>
			<div class="item webinar">Вебинары</div>
			<div class="item lesson">Уроки</div>
		</div> -->

		<div class="logo">
			<? if ($subject['isActive'] && $model['subject_id'] != 1) : ?>
			<span class="subject"><?=$subject['lable']?></span><br>
			<? endif; ?>
			<a href="/"><span class="sdamex">SDAMEX</span> <span class="pay">PAY</span></a>
		</div>

		<?php if ($type == 'course') : ?>

		<section class="tab course">
			<input id="course_id" type="hidden" value="<?=$model['id']?>">
			<div class="name">Курс / <?=$model['title']?></div>
			<div class="duration">
				<div class="item month active">1 МЕСЯЦ</div>
				<div class="item part">3 МЕСЯЦА</div>
				<div class="item full">ДО КОНЦА ГОДА</div>
			</div>
			<div class="cost">
				<span class="numb"><?=($isSale)?$model['costSale']:$model['cost']?></span><span class="rub">р</span>
				<span class="sale"><?=($isSale)?$model['cost']:''?></span>
			</div>
			<?php if ($isSale) : ?>
			<div class="code"><?= (empty($code)) ? 'Действует скидка на продление курса.' : "Скидка с применением инвайт-кода: $code" ?></div>
			<?php endif; ?>
			<div class="btn payCourse" duration="1">ОПЛАТИТЬ</div>
		</section>

		<? elseif ($type == 'webinar') : ?>

		<section class="tab webinar">
			<input id="webinar_id" type="hidden" value="<?=$model['id']?>">
			<div class="name"><?=$model['title']?></div>
			<hr>
			<div class="cost"><span class="numb"><?=$model['cost']?></span><span class="rub">р</span></div>
			<div class="btn payWebinar">ОПЛАТИТЬ</div>
		</section>

		<? elseif ($type == 'lesson') : ?>

		<section class="tab lesson">
			<label for="count_lessons">Введите необходимое количество занятий</label><br>
			<input id="count_lessons" type="number" value="1">
			<div class="cost"><span class="numb"><?=$cost?></span><span class="rub">р</span></div>
			<div class="btn payLesson">ОПЛАТИТЬ</div>
		</section>

		<? elseif ($type == 'success') : ?>

		<section class="tab success">
			<div class="icon"><i class="icon-diamond"></i></div>
			<h1 class="name">УСПЕШНАЯ ОПЛАТА</h1>
			<div class="message"><?=$message?></div>
		</section>

		<? elseif ($type == 'error') : ?>

		<section class="tab error">
			<div class="icon"><i class="icon-card"></i></div>
			<h1 class="name">ОШИБКА ОПЛАТЫ</h1>
			<div class="message"><?=$message?></div>
		</section>

		<? endif; ?>
	</div>

    <!-- <div id="preloader"> 
        <div id="loader"></div>
    </div> -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script type="text/javascript">
	let csrf = '<?=Yii::$app->getRequest()->getCsrfToken()?>';
	$(document).ready(function () {
		<? if ($type == 'course') :?>
		$('.course .duration .item').click(function () {
			if ($(this).hasClass('active'))
				return false;

			$('.course .duration .item').removeClass('active');
			$(this).addClass('active');
			let numb = $('.course .cost .numb');
			let sale = $('.course .cost .sale');
			let btn = $('.course .payCourse');
			
			if ($(this).hasClass('month')) {
				numb.text(<?=($isSale)?$model['costSale']:$model['cost']?>);
				sale.text('<?=($isSale)?$model['cost']:''?>');
				btn.attr('duration', 1);
			} else if ($(this).hasClass('part')) {
				numb.text(<?=$model['partSale']?>);
				sale.text(<?=$model['partPrice']?>);
				btn.attr('duration', 2);
			} else if ($(this).hasClass('full')) {
				numb.text(<?=$model['fullSale']?>);
				sale.text(<?=$model['fullPrice']?>);
				btn.attr('duration', 3);
			}
		});

		$('.course .payCourse').click(function () {
			payment($(this), {
				'_csrf': csrf,
				'id': $('#course_id').val(),
				'type': <?=PayType::COURSE?>,
				'duration': $(this).attr('duration')
			});
		});

		<? elseif ($type == 'webinar') :?>

		$('.webinar .payWebinar').click(function () {
			payment($(this), {
				'_csrf': csrf,
				'id': $('#webinar_id').val(),
				'type': <?=PayType::WEBINAR?>
			});
		});

		<? elseif ($type == 'lesson') :?>

		$('#count_lessons').change(function () {
			let count = $(this).val();
				count = (count == '') ? 0 : parseInt(count);
			if (count > 99)
				$(this).val(count = 99);
			else if (count < 1)
				$(this).val(count = 1);
			$('.lesson .cost .numb').text(<?=$cost?>*count);
		});

		$('.lesson .payLesson').click(function () {
			payment($(this), {
				'_csrf': csrf,
				'id': $('#count_lessons').val(),
				'type': <?=PayType::LESSON?>
			});
		});
		
		<? endif; ?>
	});

	function payment(th, data) {
		th = $(this);
		if (th.hasClass('disable'))
			return false;
		th.addClass('disable');
		$.post( '/pay/payment', data)
			.done(function( data, status, jqXHR ) {
				console.log(data);
				if (data.type == 'success') {
					if (data.link != undefined || data.link != '')
						window.location.href = data.link;
				} else if (data.type == 'error') {
					console.log(data.details);
					globalError(data.message);
				} else
					globalError();
				th.removeClass('disable');
			})
			.fail(function( jqXHR, status, errorThrown ){
				ajaxError(errorThrown, jqXHR);
				th.removeClass('disable');
			});
	}
</script>