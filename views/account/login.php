<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\assets\AuthAsset;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \mdm\admin\models\form\Login */

AuthAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Url::to(['/logo.ico'])]);

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Last-Modified: ".gmdate("D, d M Y H:i:s", (time()-24*3600))."GMT");
header("Expires: ".gmdate("D, d M Y H:i:s", (time()-24*3600))."GMT");

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache">
    <?= Html::csrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="modalBody">
</div>

<div class="authPage">
    <div class="formBlock">

        <div class="tabs">
            <div class="item signin<?= ($page == 'signin') ? ' active':'' ?>"> <i>Вход</i> </div>
            <div class="item signup<?= ($page == 'signup') ? ' active':'' ?>"> <i>Регистрация</i> </div>
        </div>

        <div class="logo"> <a href="/">SDAMEX</a> </div>

        <div class="forms">
            <div class="form signin<?= ($page == 'signin') ? ' active':'' ?>">
                <form id="signinForm" class="signinForm" action="/account/signin" method="post">
                    <?= Html::hiddenInput(\Yii::$app->getRequest()->csrfParam, \Yii::$app->getRequest()->getCsrfToken(), []); ?>
                    <input id="signinGmt" type="hidden" name="Signin[GMT]">
                    <div class="formGroup">
                        <input type="text" class="input signinEmail" name="Signin[email]" placeholder="E-mail">
                    </div>
                    <div class="formGroup">
                        <input type="password" class="input signinPass" name="Signin[password]" placeholder="Пароль">
                        <div class="forgotPass">Забыли пароль?</div>
                    </div>
                    <div class="checkboxBlock">
                        <input id='signinRememberMe' class="checkMark signinRememberMe" name="Signin[rememberMe]" type='checkbox' />
                        <label for='signinRememberMe'>
                            <span></span>
                            <div class="txt"> Запомнить меня </div>
                        </label>
                    </div>
                    <div class="signinSend send disable">
                        <i class="icon icon-android-arrow-forward"></i>
                    </div>
                </form>
            </div>

            <div class="form signup<?= ($page == 'signup') ? ' active':'' ?>">
                <form id="signupForm" class="signupForm" method="post" action="/account/signup">
                    <?= Html::hiddenInput(\Yii::$app->getRequest()->csrfParam, \Yii::$app->getRequest()->getCsrfToken(), []); ?>
                    <div class="formGroup">
                        <input type="text" class="input signupEmail" name="Signup[email]" placeholder="E-mail">
                    </div>
                    <div class="formGroup">
                        <input type="password" class="input signupPass" name="Signup[password]" placeholder="Пароль" autocomplete="newPassword">
                    </div>
                    <div class="formGroup">
                        <input type="password" class="input signupRetPass" name="Signup[retypePassword]" placeholder="Повторите пароль">
                    </div>
                    <div class="invite">
                        <label class="isInvite">У вас есть инвайт-код?</label>
                        <div class="formGroup">
                            <input type="text" class="input signupInviteCode" name="Signup[inviteCode]" placeholder="Инвайт-код">
                        </div>
                    </div>
                    <div class="checkboxBlock">
                        <input id='signupRules' class="signupRules checkMark" type='checkbox' />
                        <label for='signupRules'>
                            <span></span>
                            <div class="txt">
                                Согласие с <a href="#" target="_blank">политикой конфеденциальности</a> и <a href="#" target="_blank">договором оферты</a>.
                            </div>
                        </label>
                    </div>
                    <div class="signupSend send disable">
                        <i class="icon icon-android-arrow-forward"></i>
                    </div>
                </form>
            </div>

            <div class="form restorePass">
                <form id="restorePassForm" class="restorePassForm" method="post">
                    <?= Html::hiddenInput(\Yii::$app->getRequest()->csrfParam, \Yii::$app->getRequest()->getCsrfToken(), []); ?>
                    <div class="formGroup">
                        <input type="text" class="input restorePassEmail" name="email" placeholder="Введите ваш E-mail" autofill="off">
                        <div class="nameForm">Восстановление пароля</div>
                    </div>
                    <div class="restorePassSend send disable">
                        <i class="icon icon-android-arrow-forward"></i>
                    </div>
                </form>
            </div>

            <div class="form newPass <?php if ($page == 'newPass') {echo 'active';} ?>">
                <form id="newPassForm" class="newPassForm" method="post">
                    <?= Html::hiddenInput(\Yii::$app->getRequest()->csrfParam, \Yii::$app->getRequest()->getCsrfToken(), []); ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="token" value="<?= $token ?>">
                    <div class="formGroup">
                        <input type="password" class="input newPassPass" name="newPass" placeholder="Введите новый пароль">
                    </div>
                    <div class="formGroup">
                        <input type="password" class="input newPassRetPass" name="retypePass" placeholder="Повторите пароль">
                        <div class="nameForm">Изменение пароля</div>
                    </div>
                    <div class="newPassSend send disable">
                        <i class="icon icon-android-arrow-forward"></i>
                    </div>
                </form>
            </div>

            <div class="helpBlock <?php if ($error) {echo 'active';} ?>">
                <i class="icon icon-info-circled"></i>
                <div class="text">
                    <ul>
                        <?php if ($error) {
                            foreach ($errMessages as $key => $value) {
                                echo '<li>'.$value.'</li>';
                            }
                        } ?>
                    </ul>
                </div>
            </div>

            <div class="download">
                <div class="elem stub active">
                    <div class="downloadLogo">
                        <img src="/<?=Url::to('@imgFolder/bl-or-logo.svg')?>">
                    </div>
                </div>
                <div class="elem mailConfirm">
                    <p>
                        На вашу почту было отправленно письмо с подтверждением!
                        <a class="linkEmail" href="#"></a>
                    </p>
                </div>
            </div>

        </div>
        <div class="social">
            <a href="https://oauth.vk.com/authorize?client_id=7527305&redirect_uri=https://sdamex.ru/oauth-vk&display=page&scope=email&v=5.120" class="item"> <i class="icon-vk-logo"></i> </a>
            <a href="https://www.facebook.com/dialog/oauth?client_id=301248721244030&redirect_uri=https://sdamex.ru/oauth-fb&scope=public_profile,email&response_type=code" class="item"> <i class="icon-facebook"></i> </a>
            <a href="https://accounts.google.com/o/oauth2/auth?redirect_uri=https://sdamex.ru/oauth-gl&response_type=code&client_id=281510484117-lloo1qm2tvr9ssb405tortp4ulagah1j.apps.googleusercontent.com&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile" class="item"> <img src="<?=Url::to(['@imgFolder/google.svg'])?>"> </a>
        </div>

        <div class="consent">Регистрируясь, Вы соглашаетесь с <a href="#" target="_blank">политикой конфеденциальности</a> и <a href="#" target="_blank">договором оферты</a></div>
    </div>

    <div class="waves">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 847.33" class="wave wave1" preserveAspectRatio="none"><path class="cls-1" d="M0,11.33c95-21,266-18,442,54,464.57,190.05,518,402,869,517,287,94,423,72,609,61v204H0Z"/></svg>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 572.74" class="wave wave2" preserveAspectRatio="none"><path class="cls-1" d="M0,135.74c137-100,287-168,517-120,261,54.46,353,209,554,231,205.77,22.52,300-68,475-98,108.77-18.65,284-9,374,48v376H0Z"/></svg>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 586.14" class="wave wave3" preserveAspectRatio="none"><path class="cls-1" d="M0,286.14s143.48,96.81,403,53c231-39,396.68-331.07,674-339,245.56-7,374.9,254,624,254,177,0,219-44,219-44v376H0Z"/></svg>
    </div>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script type="text/javascript">
    $(document).ready(function () {

        // SIGNIN / ВХОД
        // -------------------------------------------------

        // Меняем значение поля GMT, на пользовательское (GMT - main.js)
        $('#signinGmt').val(GMT);

        // При изменение поля email
        $('.signinEmail').keyup(function (e) {
            let check = checkSignin(); // проверяем можно ли открыть доступ к отправке
            if (e.which == 13 && check) {
                e.preventDefault();
                $('.signinSend').click();
            }
        });

        // При изменение поля пароль
        $('.signinPass').keyup(function (e) {
            let check = checkSignin(); // проверяем можно ли открыть доступ к отправке
            if (e.which == 13 && check) {
                e.preventDefault();
                $('.signinSend').click();
            }
        });

        $('.signinSend').click(function () {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;

            // Получаем куку с помощью jq.cookie
            let error = $.cookie('signin_error');
            let validEmail = true;

            // Не ложное значение, кука есть
            if (!!error) {
                error = error.replace(/\+/g , ' '); // Меняем спец символ '+' на пробел
                error = error.split('&cstsplit;'); // Делим строку и получаем 0 - email, 1 - error
                $('.helpBlock .text').html(error[1]); // Меняем текст ошибки
                $('.helpBlock').addClass('active'); // Выводим ошибку

                if ($('.signinEmail').val() == error[0])
                    validEmail = false;
            }

            // Пройдёт валидацию, если email не забанен
            if (validEmail) {
                th.addClass('disable');
                data = $('#signinForm').serialize(); // Сериализуем форму
                $.post( '/account/signin', data)
                    .done(function( data, status, jqXHR ) {
                        console.log(data);
                        if (data.type == 'error')
                            viewError(data.messages);
                        else
                            window.location.href = data.messages.link;
                        th.removeClass('disable');
                    })
                    .fail(function( jqXHR, status, errorThrown ){
                        ajaxError(errorThrown, jqXHR);
                    });
            }
        });

        $('.helpBlock').on('click', '.mailAgain', function () {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;

            // Получаем куку с помощью jq.cookie
            // let error = $.cookie('signin_error');
            let validEmail = true;

            // Не ложное значение, кука есть
            // if (!!error) {
            //     error = error.replace(/\+/g , ' '); // Меняем спец символ '+' на пробел
            //     error = error.split('&cstsplit;'); // Делим строку и получаем 0 - email, 1 - error
            //     $('.helpBlock .text').html(error[1]); // Меняем текст ошибки
            //     $('.helpBlock').addClass('active'); // Выводим ошибку

            //     if ($('.signinEmail').val() == error[0]) {
            //         validEmail = false;
            //     }
            // }

            // Пройдёт валидацию, если email не забанен
            if (validEmail) {
                th.addClass('disable');
                let email = $('.signinEmail').val(); // получаем email
                $.post( '/account/mail-again', {'_csrf': $('meta[name="csrf-token"]').attr('content'), 'email':email})
                    .done(function( data, status, jqXHR ) {
                        if (data.type == 'error')
                            viewError(data.messages);
                        else if (data.type == 'success')
                            redirectConfirmEmail(data.messages);
                        th.removeClass('disable');
                    })
                    .fail(function( jqXHR, status, errorThrown ){
                        ajaxError(errorThrown, jqXHR);
                    });
            }

            return false;
        });

        function checkSignin() {
            let email = $('.signinEmail').val();
            let check = (email != '' && checkEmail(email) && $('.signinPass').val() != '');
            checkBtnSend(check, '.signinSend');
            return check;
        }

        // --------
        // SIGNIN

        ///////////////////////////////////

        // SIGNUP / РЕГИСТРАЦИЯ
        // -------------------------------------------------

        // Меняем значение поля GMT, на пользовательское (GMT - main.js)
        $('#signupGmt').val(GMT);

        // При изменение поля email
        $('.signupEmail').keyup(function (e) {
            var val = $(this).val();
            if (val.indexOf('@')) {
                var arr = val.split('@');
                $('#signupUsername').val(arr[0]);
                $('#domain').val(arr[1]);
            }
            
            let check = checkSignup(); // проверяем можно ли открыть доступ к отправке
            if (e.which == 13 && check) {
                e.preventDefault();
                $('.signupSend').click();
            }
        });

        // При изменение поля пароль
        $('.signupPass').keyup(function e() {
            let check = checkSignup(); // проверяем можно ли открыть доступ к отправке
            if (e.which == 13 && check) {
                e.preventDefault();
                $('.signupSend').click();
            }
        });

        // При изменение поля повтор пароля
        $('.signupRetPass').keyup(function (e) {
            let check = checkSignup(); // проверяем можно ли открыть доступ к отправке
            if (e.which == 13 && check) {
                e.preventDefault();
                $('.signupSend').click();
            }
        });

        // При нажатие на инвайт
        $('.invite .isInvite').click(function () {
            $(this).parent().toggleClass('active');
        });

        // При изменение поля повтор пароля
        $('.signupRules').change(function () {
            checkSignup(); // проверяем можно ли открыть доступ к отправке
        });

        $('.signupSend').click(function () {
            let th = $(this);
            if (th.hasClass('disable'))
                return false;

            th.addClass('disable');
            data = $('#signupForm').serialize(); // Сериализуем форму

            $.post( '/account/signup', data)
                .done(function( data, status, jqXHR ) {
                    if (data.type == 'error')
                        viewError(data.messages);
                    else if (data.type == 'success')
                        redirectConfirmEmail(data.messages);
                    th.removeClass('disable');
                })
                .fail(function( jqXHR, status, errorThrown ){
                    ajaxError(errorThrown, jqXHR);
                });
        });

        function checkSignup() {
            let email = $('.signupEmail').val();
            let check = (email != ''
                    && $('.signupPass').val() != ''
                    && $('.signupPass').val() == $('.signupRetPass').val()
                    && $('.signupRules').prop("checked")
                    && checkEmail(email));
            checkBtnSend(check, '.signupSend');
            return check;
        }

        // --------
        // SIGNUP

        // ///////////////////

        // RESTORE PASS / СБРОС ПАРОЛЯ
        // -------------------------------------------------

        // При изменение поля email
        $('.restorePassEmail').keyup(function (e) {
            let val = $(this).val();
            let check = (val != '' && checkEmail(val));

            if (check) {
                var arr = val.split('@');
                $('#domain').val(arr[1]);
            }
            checkBtnSend(check, '.restorePassSend');
            if (e.which == 13 && check)
                $('.restorePassSend').click();
        });

        $('.restorePassSend').click(function () {
            let th = $(this);
            if (!th.hasClass('disable')) {
                th.addClass('disable');
                data = $('#restorePassForm').serialize(); // Сериализуем форму
                $.ajax({
                    url: '/account/resetpass',
                    method: 'POST',
                    data: data,
                    success: function (data) {
                        console.log(data);
                        data = JSON.parse(data);

                        if (data.type == 'error') {
                            viewError(data.messages);
                            th.removeClass('disable');
                        } else if (data.type == 'success') {
                            redirectConfirmEmail(data.messages);
                        }
                    }
                    // error: globalError()
                });
            }
        });

        // --------
        // RESTORE PASS

        // ///////////////////

        // NEW PASS / НОВЫЙ ПАРОЛЯ
        // -------------------------------------------------

        // При изменение поля email
        $('.newPassPass').keyup(function (e) {
            check = checkNewPass();
            if (e.which == 13 && check)
                $('.newPassSend').click();
        });

        $('.newPassRetPass').keyup(function (e) {
            check = checkNewPass();
            if (e.which == 13 && check)
                $('.newPassSend').click();
        });

        $('.newPassSend').click(function () {
            let th = $(this);
            if (!th.hasClass('disable')) {
                th.addClass('disable');
                data = $('#newPassForm').serialize(); // Сериализуем форму
                $.ajax({
                    url: '/account/set-newpass',
                    method: 'POST',
                    data: data,
                    success: function (data) {
                        // console.log(data);
                        // data = JSON.parse(data);

                        if (data.type == 'error') {
                            viewError(data.messages);
                            th.removeClass('disable');
                        } else {
                            window.location.href = data.messages.link;
                        }
                    }
                    // error: globalError()
                });
            }
        });

        function checkNewPass() {
            let check = ($('.newPassPass').val() != '' && $('.newPassRetPass').val() != '');
            checkBtnSend(check, '.newPassSend');
            return check;
        }

        // --------
        // NEW PASS

        $('.forgotPass').click(function () {
            tabSwitch($(this));
        });

        $('.tabs .item').click(function () {
            tabSwitch($(this));
        });
    }); // end ready

    function viewError(errorList) {
        let errors = '';
        for (var i = 0; i < errorList.length; i++) {
            errors += '<ul>'+errorList[i]+'</ul>';
        }
        $('.helpBlock .text').html(errors);
        $('.helpBlock').addClass('active');
    }

    function tabSwitch(th) {
        if (!th.hasClass('active') && !$('.tabs').hasClass('disable')) {

            $('.tabs').addClass('disable');

            $('.tabs .item').removeClass('active');
            if (!th.hasClass('forgotPass'))
                th.addClass('active');

            $('.formBlock .download').addClass('active');

            setTimeout(function () {

                $('.helpBlock').removeClass('active');
                $('.formBlock .form').removeClass('active');

                if (th.hasClass('signin')) {
                    $('.formBlock .signin').addClass('active');
                } else if (th.hasClass('signup')) {
                    $('.formBlock .signup').addClass('active');
                } else {
                    $('.formBlock .restorePass').addClass('active');
                }

                setTimeout(function () {
                    $('.tabs').removeClass('disable');
                    $('.formBlock .download').removeClass('active');
                }, 300);

            }, 500);

        } // end if
    } // end fn tabSwitch

    function redirectConfirmEmail(domain='') {
        $('.tabs').addClass('disable');
        $('.download .elem').removeClass('active');
        $('.download .mailConfirm').addClass('active');
        if (domain != '') {
            $('.download .mailConfirm .linkEmail').text(domain);
            $('.download .mailConfirm .linkEmail').attr('href', `https://${domain}`);
        }
        $('.download').addClass('active');
    }

    function checkBtnSend(check, btnClass) {
        $('.helpBlock').removeClass('active');

        if (check) {
            $(btnClass).removeClass('disable');
        } else {
            if (!$(btnClass).hasClass('disable')) {
                $(btnClass).addClass('disable');
            }
        }
    }

    function checkEmail(email) {
        let match = email.match(/^[0-9a-z-\.]+\@[0-9a-z-]{2,}\.[a-z]{2,}$/i);
        if (!match) {
            $('.helpBlock .text').html('Проверьте правильность ввода поля E-mail');
            $('.helpBlock').addClass('active');
            return false;
        } else
            return true;
    }
</script>
