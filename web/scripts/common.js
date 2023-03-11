// локальная дата
var dateNow = new Date();
// получаем разницу часовых поясов
var GMT = dateNow.getTimezoneOffset()/60 * -1;
$("input[name=GMT]").val(GMT);

// localStorage.setItem("GMT", GMT);

if (!$.cookie('work'))
    $.cookie('work', true, { expires : 365, path: '/',  });
if ($.cookie('GMT') != GMT)
    $.cookie('GMT', GMT, { expires : 365, path: '/' });

// Выделяем активный пункт меню
$('.navbar .menu .item').each(function () {

    var link = document.location.href;
    // Вытаскиваем адресную строку и проверяем её на наличие той страницы на которой мы находимся
    if (link.indexOf($(this).attr('href')) > 0) {
        $('.navbar .menu .item').removeClass('active');
        $(this).addClass('active');
    }

});

// Закрытие прелоудера
// $(window).on("load", function () {
//     $('#bgPreloader').fadeOut("slow");
//     window.onbeforeunload = function () { $('#bgPreloader').fadeIn("slow"); }
// });

$(".modalBody").click(function (e) {
    if (e.target.className.indexOf("modalBody") > -1)
        closeModal();
});

$(".modal .close").click(function () {
    closeModal();
});

$(".modal .cancel").click(function () {
    closeModal();
});

$(".modal .confirm").click(function () {
    closeModal();
});

function openModal(modal) {
    $(".modalBody").css("display", "flex");
    $(modal).css("display", "block").addClass("fadeInDown");
}

// Закрытие модального окна
function closeModal() {
    var modal = $(".modalBody").children(".fadeInDown");
    modal.addClass("fadeOutUp").removeClass("fadeInDown");
    setTimeout(function () {
        $(".modalBody").fadeOut(500);
        if ($('.modalBody .youtube').hasClass('fadeOutUp')) {
            $('.modalBody .youtube iframe').attr('src', '');
        }
        modal.css("display", "none").removeClass("fadeOutUp");

    }, 500);
}

function scrollTop(time = 1000, top = 0) {
    $('html, body').animate({
        scrollTop: top
    }, time);
}

// Глушилка
function globalError(message = "Что-то пошло не так!") {
    alert(message);
}

function ajaxError(clarification = '', jqXHR = null, message = "ОШИБКА AJAX запроса: ") {
    globalError();
    console.log( message + clarification, jqXHR );
}

function changeClassStar(item, num, cls) {
    $(item).each(function (i, e) {
        if (num >= (i+1)) {
            if (cls == 'active')
                $(this).addClass('icon-star').removeClass('icon-star-o');
            $(this).addClass(cls);
        } else {
            if (cls == 'active')
                $(this).addClass('icon-star-o').removeClass('icon-star');
            $(this).removeClass(cls);
        }
    });
}

// Выводим дату в нужном формате
function formatDate(date) {
    var monthNames = [
      "Января", "Февраля", "Марта",
      "Апреля", "Мая", "Июня", "Июля",
      "Августа", "Сентября", "Октября",
      "Ноября", "Декабря"
    ];
  
    var day = date.getDate();
        day = (day <= 9)?'0'+day:''+day;
    var monthIndex = date.getMonth();
    var year = date.getFullYear();
  
    return day + ' ' + monthNames[monthIndex] + ' ' + year;
}

// создание рандомной строки
function makeRandomString(length)
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    for( var i=0; i < length; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}