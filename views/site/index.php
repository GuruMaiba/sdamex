<?php

use app\widgets\ModalWidget;
// use yii\helpers\Html;
use yii\helpers\Url;
//<?= ModalWidget::widget() > <!-- ['message' => 'Good morning'] -->

/* @var $this yii\web\View */

$this->params['introBG'] = Url::to("@imgFolder/student-bg.jpg");
$this->params['intro'] = [
	'menu' => [
		'subjects' => 'Предметы',
		'aboutUs' => 'О нас',
		'feedback' => 'Обратный звонок', 
	],
	'title' => 'СДАЙ ЭКЗАМЕН НА ОТЛИЧНО',
	'btn' => [
		'href' => '#subjects',
		'name' => 'ПРЕДМЕТЫ'
	]
];
?>

<div class="mainPage">
   <!-- subjects
   ================================================== -->
   <section id="subjects" class="subjects">

   	<div class="row section-intro">
   		<div class="col-twelve with-bottom-line">
   			<h5>предметы</h5>
   			<h1>Начинай учиться уже сегодня</h1>

   			<p class="lead">Мы работаем над каждым предметом с особой скрупулёзностью, желая выдать качественную, а самое главное интересную подготовку к экзаменам. Список предметов будет расширяться по мере развития проекта...</p>
   		</div>   		
   	</div>

   	<div class="row subjects-content">

   		<div class="q-and-a block-1-2 block-tab-full group">

		   <?php
			 foreach (Yii::$app->params['listSubs'] as $id => $sub) :
				$bg = null;
				switch ($sub['name']) {
					case 'RUSSIAN':
						$bg = Url::to('@imgFolder/russian.jpg');
						break;

					case 'MATHEMATICS':
						$bg = Url::to('@imgFolder/math.jpg');
						break;
					
					default:
						$bg = Url::to('@imgFolder/nasa1.jpg');
						break;
				}
				if ($sub['isActive'] && $id != 1) :
		   ?>
		   <a class="bgrid" href="<?=$sub['link']?>" style="background-image: url(<?=$bg?>);">
				<div class="blackout"></div>

				<div class="text">
					<h3><?=$sub['lable']?></h3>
				</div>
			</a>
			<?php
				endif;
			endforeach;
		   ?>

   		</div> <!-- /q-and-a --> 

		<!-- <div class="courseCost">Стоимость курсов не превышает 2800 рублей!</div> -->
   		
   	</div> <!-- /subjects-content --> 
   </section> <!-- /subjects --> 

	<!-- aboutUs Section
   ================================================== -->
   <section id="aboutUs" class="aboutUs">  

   	<div class="row section-intro">
   		<div class="col-twelve with-bottom-line">

   			<h5>О НАС</h5>
   			<h1>SDAMEX</h1>

   			<p class="lead">
				Самый крупный проект подготовки к экзаменам в России 2020.
				Наша цель - подарить возможности тысячам студентов.
				Мы создаем систему, способную беспрепятственно подготовить любого заинтересованного студента к экзаменам.
			</p>

   		</div>   		
   	</div>

   	<div class="row aboutUs-content">

   		<div class="left-side">

   			<div class="item">

			   <div class="pin"><i class="icon-ios-videocam"></i></div>

   				<h5>Вебинары</h5>

   				<p>Онлайн-трансляции на большую аудиторию, где каждый ученик сможет найти интересующую его тему не только в рамках школьной программы, но и по мотивации, психологии и поиску себя.</p>
   					
   			</div>

   			<div class="item">

			   <div class="pin"><i class="icon-ios-film"></i></div>

	   			<h5>Видео-уроки</h5>

	   			<p>Видео-уроки, детально раскрывающие все темы, необходимые для сдачи экзамена на 100%.</p>
   					
   			</div>
   				
   		</div> <!-- /left-side -->
   		
   		<div class="right-side">
   				
   			<div class="item">

			   <div class="pin"><i class="icon-pencil-1"></i></div>

   				<h5>Умные тестирования</h5>

   				<p>Встроенный программный интеллект отображает ученику степень его текущей подготовки, указывает, что именно нужно изучить для достижения 100 баллов.</p>
   					
   			</div>

   			<div class="item">

			   <div class="pin"><i class="icon-social-skype"></i></div>

   				<h5>Частные уроки</h5>

   				<p>Возможность взять частный урок с преподавателем, чтобы разобрать свои особенно слабые темы.</p>
   					
   			</div>

   		</div> <!-- /right-side -->  

   		<div class="image-part"></div>  			

   	</div> <!-- /aboutUs-content --> 

   </section> <!-- /aboutUs-->

   <!-- teacher
   ================================================== -->
   <section id="feedback" class="feedback">

   	<div class="row feedback-content">

   		<div class="col-twelve">

   			<h1 class="h01">Обратный звонок</h1>
			   
			<div class="thanks hidden">Благодарим за заявку,<br> в скором времени мы с Вами свяжемся!</div>

			<div class="form-block">
				<p class="lead">Заполните поля, и в скором времени мы с Вами свяжемся.</p>

				<form id="comm-form" class="comm-form" novalidate="true" action="/teacher/send-request" method="post">
					<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
					<input type="text" value="" name="phone" class="phone" id="mc-phone" placeholder="Телефон*" required="">
					<input type="text" value="" name="time" class="time" id="mc-time" placeholder="Удобное время для связи (МСК)" required="">
				</form>

				<div class="button large round send" style="display:inline-block;">ОТПРАВИТЬ</div>
			</div>

			<h3 class="h03">Хочешь стать учителем? Тебе -> <a href="/teacher">сюда</a>!</h3>
   		</div>

   	</div> <!-- /feedback-content -->

   </section> <!-- /feedback -->
</div>

<?php
$js = <<<JS
	$('#mc-phone').mask('+0 (000) 000-00-00');

	$('.feedback-content .send').click(function () {
		if ($(this).hasClass('disable'))
			return false;

		$(this).addClass('disable');
		let data = $('#comm-form').serialize();
		$.post("/site/back-call", data)
            .done(function (req) {
                // console.log(req);
                if (req == 1) {
					$('.feedback-content .form-block').remove();
					$('.feedback-content .thanks').removeClass('hidden');
				} else
                    globalError('В Ваших данные допущена ошибка!');
            }).fail(() => { globalError() });
		$(this).removeClass('disable');
	});
JS;
$this->registerJs($js);
$this->registerJsFile( '@scrLibs/jquery.mask.min.js', ['depends' => ['app\assets\MainAsset'],], 'maskJS' );

// Передача блока в layout
// $this->beginBlock('modals');

// echo ModalWidget::widget([
//     'blockId' => 'testModal',
//     'title'=>'ОБРАТНАЯ СВЯЗЬ',
//     'desc'=>'Gathering. Won&#39;t beast fowl fifth one which itself have created, <em>set</em> their form fourth. Above creepeth female stars doesn&#39;t seas, our doesn&#39;t, land created bearing years fowl wherein replenish light rule earth deep moveth creature. Moving, behold void spirit. Was living. Seed. Open behold fifth bearing whose you&#39;re be you&#39;ll. Dry.',
//     'form' => [
//         'id' => 'callMe',
//         'inputs' => [
//             '0' => [
//                 'type' => 'input',
//                 'label'=>'Имя',
//                 'placeholder'=>'Введите Ваше имя...',
//                 'name'=>'RunNiga',
//             ],
//             '1' => [
//                 'type' => 'input',
//                 'label'=>'Имя2',
//                 'placeholder'=>'Введите Ваше имя...',
//                 'name'=>'RunNiga2',
//             ],
//             '2' => [
//                 'type' => 'textarea',
//                 'label'=>'О себе',
//                 'placeholder'=>'Расскажите что-нибудь...',
//                 'name'=>'TextNiga',
//             ],
//             '3' => [
//                 'type' => 'checkbox',
//                 'label'=>'С условиями использования ознакомлен.',
//                 'name'=>'checkNiga'
//             ],
//         ],
//     ],
//     'buttons' => [
//         'cancel' => 'Отмена',
//         'confirm' => 'OK',
//         'send' => 'Отправить',
//     ],
//     ]);

// $this->endBlock();
?>
