<div class="searchTheme <?=($hide)?'hdn':''?>">
    <input type="text" class="form-control themeName" placeholder="Введите название темы">
    <div class="list">
        <?= $this->render('_item', [ 'model' => $model, 'active' => $active ]); ?>
    </div>
    <div class="btn btn-danger del">Удалить</div>    
    <div class="btn btn-success add">Добавить</div>    
</div>

<?php
$csrf = Yii::$app->getRequest()->getCsrfToken();
$js = <<<JS
    let csrf = "$csrf";
    $('.container').on('keyup', '.searchTheme .themeName', function () {
        let val = $(this).val();
        let search = $(this).parent();
        let item = $(this).siblings('.list').children('.item');

        let newTheme = true;
        item.each(function (i) {
            if (val == '')
                return false;
            if ($(this).text().indexOf(val) > -1 ) {
                if (search.hasClass('hdn'))
                    $(this).addClass('view');
                else
                    $(this).removeClass('hidden');
                newTheme = false;
            } else {
                if (search.hasClass('hdn'))
                    $(this).removeClass('view');
                else
                    $(this).addClass('hidden');
            }
        });

        if (val == '') {
            newTheme = false;
            if (search.hasClass('hdn'))
                item.removeClass('view');
            else
                item.removeClass('hidden');
        }

        if (newTheme)
            search.addClass('new');
        else 
            search.removeClass('new');
    });
    
    $('.container').on('click', '.searchTheme .list .item', function () {
        $(this).toggleClass('active');

        let edit = false;
        $('.searchTheme .list .item').each(function (i) {
            if ($(this).hasClass('active')) {
                edit = true;
                return false;
            }
        });

        if (edit)
            $('.searchTheme').addClass('edit');
        else 
            $('.searchTheme').removeClass('edit');
    });

    $('.searchTheme .add').click(function () {
        let th = $(this);
        let val = $('.themeName').val();
        if (val == '' || th.hasClass('disable'))
            return false;

        th.addClass('disable');
        $.post('/admin/theme/add', { '_csrf':csrf, 'name':val })
            .done(function (req) {
                if (req == 0)
                    globalError();
                else
                    $('.searchTheme .list').append(req);

                th.removeClass('disable');
                $('.searchTheme').removeClass('new');
                $('.themeName').val('');
                $('.searchTheme .list .item').removeClass('hidden');
            }).fail(()=>{globalError();});
    });

    $('.searchTheme .del').click(function () {
        let th = $(this);
        if (th.hasClass('disable'))
            return false;

        let ids = [];
        th.addClass('disable');
        $('.searchTheme .list .item').each(function (i) {
            if ($(this).hasClass('active')) {
                ids.push($(this).attr('number'));
            }
        });

        $.post('/admin/theme/del', { '_csrf':csrf, 'ids[]':ids })
            .done(function (req) {
                if (req == 0)
                    globalError();
                else
                    $('.searchTheme .list .item.active').remove();

                th.removeClass('disable');
                $('.searchTheme').removeClass('edit');
            }).fail(()=>{globalError();});
    });
JS;
$this->registerJs($js);
 ?>