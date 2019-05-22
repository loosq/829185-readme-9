//Прелоадер
$(window).on('load', function() {
    $('#preloader').find('div').fadeOut().end().delay(200).fadeOut();
});

//Сетка карточек постов в популярном
$(document).ready(function () {
    $('#popular__posts').BlocksIt({
        numOfCol: 3,
        offsetX: 20,
        offsetY: 20,
        blockElement: 'article'
    });
});

//Добавление\удаление лайков
$(document).ready(function () {
    var likeBtn = $('.post__indicator--likes');

    $(likeBtn).on('click', function () {

        var postId = $(this).attr('data-post-id');
        var svg = $(this).children('svg');

        var span = $(this).children().first().next();
        var count = $(this).children().first().next().text();
        var newCount = +count + 1;
        var remCount = +count - 1;

        if (!($(this).children('span').hasClass('like-counter'))) {

            $.ajax({
                url: 'likes.php',
                type: 'GET',
                data: 'postId=' + postId,
                success: function () {
                    svg.children('use').attr('xlink:href', '#icon-heart');
                    span.addClass('like-counter');
                    span.text(remCount);
                }
            });

        } else {
            $.ajax({
                url: 'likes.php',
                type: 'GET',
                data: 'postId=' + postId,
                success: function () {
                    svg.children('use').attr('xlink:href', '#icon-heart-active');
                    span.text(newCount);
                    span.removeClass('like-counter');
                }
            });
        }
    });
});

//Добавление\удаление подписки
$(document).ready(function () {
    var subBtn = $('.user__button--subscription');

    $(subBtn).on('click', function () {
        var subBtn = $(this);
        var isSubData = $(this).attr('data-issub');
        var userId = $(this).attr('data-user-id');
        var span = $(this).children('span');
        span. css('color', 'transparent');
        var preloader = $('<div>', {id: 'sub__preloader'});
        if (+isSubData === 1) {
            subBtn.append(preloader);
            $.ajax({
                url: 'subscribe.php',
                type: 'GET',
                data: 'user=' + userId,
                success: function () {
                    preloader.detach();
                    span. css('color', '#fff');
                    span.text('Подписаться');
                    $(subBtn).attr('data-issub', '0');
                }
            });
        } else if (+isSubData === 0){
            subBtn.append(preloader);
            $.ajax({
                url: 'subscribe.php',
                type: 'GET',
                data: 'user=' + userId,
                success: function () {
                    preloader.detach();
                    span. css('color', '#fff');
                    span.text('Отписаться');
                    $(subBtn).attr('data-issub', '1');
                }
            });
        }
    });
});

//переадресует на страницу поиска и передаёт поле поисковой формы
$(document).ready(function () {
    var localUrl = window.location.pathname;

    if (!(localUrl === '/search.php')) {
        var searchSubmtBtn = $('.header__search-button');
        var getQ = $('.header__search-input').text();
        $(searchSubmtBtn).on('click', function () {
            window.location.replace('search.php?q=' + getQ);
        });
    }
});
