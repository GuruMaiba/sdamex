<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\MainAsset;

MainAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Url::to(['/logo.ico'])]);

// Передаём переменную с путём к картинке, для изменения фона
$introBG = Url::to('@imgFolder/nasa1.jpg');
if (isset($this->params['introBG'])) { $introBG = $this->params['introBG']; }

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
	<title><?= Html::encode($this->title) ?></title>
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <?= Html::csrfMetaTags() ?>

    <link rel="preload" href="/css/icons.css" as="style">

    <?php $this->head() ?>
</head>

<body id="top"><?php $this->beginBody() ?>

  <div class="modalBody">
    <?php
        if (isset($this->blocks['modals']))
            echo $this->blocks['modals'];
    ?>
  </div>

  <!-- header 
  ================================================== -->
  <header>

   	<div class="row">

   		<div class="logo">
			<a href="/">SDAMEX</a>
		</div>

	   	<nav id="main-nav-wrap">
			<ul class="main-navigation">
				<li class="current"><a class="smoothscroll"  href="#intro" title="">Главная</a></li>
				<? foreach ($this->params['intro']['menu'] as $id => $name) : ?>
				<li><a class="smoothscroll" href="#<?=$id?>"><?=$name?></a></li>
				<? endforeach; ?>
				<li class="highlight with-sep">
					<? if (Yii::$app->user->isGuest) : ?>
					<a href="/account/login" title="">Войти</a>
					<? else :
						$subs = Yii::$app->params['listSubs'];
						$profileLink = '';
						foreach ($subs as $id => $sub) {
							if ($id > 1 && $sub['isActive'])
								$profileLink = $sub['link']."personal/profile/".Yii::$app->user->identity->id;
						}
					?>
					<a href="<?=$profileLink?>" title=""><?=Yii::$app->user->identity->username?></a>
					<? endif; ?>
				</li>					
			</ul>
		</nav>

		<a class="menu-toggle" href="#"><span>Menu</span></a>
   		
   	</div>   	
   	
  </header> <!-- /header -->

  <!-- intro section
  ================================================== -->
  <section id="intro" style="background-image: url(<?=$introBG?>);">

   	<div class="shadow-overlay"></div>

   	<div class="intro-content">
   		<div class="row">

   			<div class="col-twelve">

	   			<!-- <div class='video-link'>
	   				<a href="#video-popup"><img src="images/play-button.png" alt=""></a>
	   			</div> -->

	   			<h5>добро пожаловать в SDAMEX</h5>
	   			<h1><?=$this->params['intro']['title']?></h1>

	   			<a class="button stroke smoothscroll" href="<?=$this->params['intro']['btn']['href']?>"><?=$this->params['intro']['btn']['name']?></a>

	   		</div>  
   			
   		</div>   		 		
   	</div>  	 	

  </section> <!-- /intro -->

  <?= $content ?>

  <!-- footer
   ================================================== -->
   <footer>

   	<div class="footer-main">

   		<div class="row">  

           <div class="col-six tab-full mob-full footer-logo-img">            

                <img src="<?=Url::to(['@imgFolder/wh-or-logo.svg'])?>" alt="">

            </div>
	      	<div class="col-six tab-full mob-full footer-info">            

	            <!-- <div class="footer-logo"></div> -->

	            <p>
                    По всем вопросам:<br>
            	    WhatsApp | Viber | Telegram<br>
		        	+7 (977) 834-60-15 <br> team@sdamex.ru
                </p>

            </div> <!-- /footer-info -->

	      	<!-- <div class="col-two tab-1-3 mob-1-2 site-links">

	      		<h4>Site Links</h4>

	      		<ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Terms</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>

	      	</div> -->
            <!-- /site-links -->  

	      	<!-- <div class="col-two tab-1-3 mob-1-2 social-links">

	      		<h4>Social</h4>

	      		<ul>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Dribbble</a></li>
                    <li><a href="#">Google+</a></li>
                    <li><a href="#">Skype</a></li>
                </ul>
	      	           	
	      	</div> -->
            <!-- /social --> 

	      	<!-- <div class="col-four tab-1-3 mob-full footer-subscribe">

	      		<h4>Subscribe</h4>

	      		<p>Keep yourself updated. Subscribe to our newsletter.</p>

	      		<div class="subscribe-form">
	      	
	      			<form id="mc-form" class="group" novalidate="true">

                        <input type="email" value="" name="dEmail" class="email" id="mc-email" placeholder="type email &amp; hit enter" required=""> 
	   		
			   			<input type="submit" name="subscribe" >
		   	
		   				<label for="mc-email" class="subscribe-message"></label>
			
                    </form>

	      		</div>	      		
	      	           	
	      	</div> -->
            <!-- /subscribe -->

        </div> <!-- /row -->

   	</div> <!-- /footer-main -->


    <div class="footer-bottom">
        <div class="row">

            <div class="col-twelve">
                <div class="copyright">
                    <span>© Copyright SDAMEX <?=date('Y', time())?>.</span> 
                    <span>Website developed <a href="https://vk.com/gurumaiba">GuruMaiba</a></span>		         	
                </div>

                <div id="go-top" style="display: none;">
                    <a class="smoothscroll" title="Back to Top" href="#top"><i class="icon-angle-double-up"></i></a>
                </div>         
            </div>

        </div> <!-- /footer-bottom -->     	

    </div>

  </footer>

  <div id="preloader"> 
    <div id="loader"></div>
  </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script type="text/javascript">
    // =====================================================

	$(window).on('load', function () {
		// Preloader
		$("#loader").fadeOut("slow", function(){
			$("#preloader").fadeOut("slow");
		});
	});


	/*----------------------------------------------------*/
	/*	Sticky Navigation
	------------------------------------------------------*/
	$(window).on('scroll', function() {
		var y = $(window).scrollTop(),
			topBar = $('header');

		if (y > 1)
			topBar.addClass('sticky');
		else
			topBar.removeClass('sticky');
	});


	/*-----------------------------------------------------*/
	/* Mobile Menu
	------------------------------------------------------ */  
	var toggleButton = $('.menu-toggle'),
		nav = $('.main-navigation');

	toggleButton.on('click', function(event){
		event.preventDefault();

		toggleButton.toggleClass('is-clicked');
		nav.slideToggle();
	});

	if (toggleButton.is(':visible')) nav.addClass('mobile');

	$(window).resize(function() {
		if (toggleButton.is(':visible')) nav.addClass('mobile');
		else nav.removeClass('mobile');
	});

	$('#main-nav-wrap li a').on("click", function() {   
		if (nav.hasClass('mobile')) {   		
			toggleButton.toggleClass('is-clicked'); 
			nav.fadeOut();   		
		}     
	});


	/*----------------------------------------------------*/
	/* Highlight the current section in the navigation bar
	------------------------------------------------------*/
	var sections = $("section"),
		navigation_links = $("#main-nav-wrap li a");	

	sections.waypoint( { 
		handler: function(direction) {
			var active_section;
				active_section = $('section#' + this.element.id);
			if (direction === "up") active_section = active_section.prev();
			var active_link = $('#main-nav-wrap a[href="#' + active_section.attr("id") + '"]');			

			navigation_links.parent().removeClass("current");
			active_link.parent().addClass("current");
		},
		offset: '25%'
	});

	/*----------------------------------------------------*/
	/* Smooth Scrolling
	------------------------------------------------------*/
	$('.smoothscroll').on('click', function (e) {
		e.preventDefault();

		var target = this.hash,
			targetJQ = $(target);

		$('html, body').stop().animate({
			'scrollTop': targetJQ.offset().top
		}, 800, 'swing', function () {
			window.location.hash = target;
		});
	});


	/*----------------------------------------------------- */
	/* Back to top
	------------------------------------------------------- */ 
	var pxShow = 300; // height on which the button will show
	var fadeInTime = 400; // how slow/fast you want the button to show
	var fadeOutTime = 400; // how slow/fast you want the button to hide
	var scrollSpeed = 300; // how slow/fast you want the button to scroll to top. can be a value, 'slow', 'normal' or 'fast'

	// Show or hide the sticky footer button
	$(window).scroll(function() {
		if (!( $("#header-search").hasClass('is-visible'))) {
			if ($(window).scrollTop() >= pxShow)
				$("#go-top").fadeIn(fadeInTime);
			else
				$("#go-top").fadeOut(fadeOutTime);
		}
	});
</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
    m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(65126053, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/65126053" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->