/*
* Добавляет прелоадер при загрузке страницы
* */
$(window).on('load', function () {
    $('#preloader').find('div').fadeOut().end().delay(200).fadeOut();
});

/*
* Выравнивает сетку просмотра постов в категории популярное
* */
$(document).ready(function () {
    $('#popular__posts').BlocksIt({
        numOfCol: 3,
        offsetX: 20,
        offsetY: 20,
        blockElement: 'article'
    });
});

/*
* Добавляет\удаляет лайки
* */
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

/*
* Добавляет\удаляет подписку
* */
$(document).ready(function () {
    var subBtn = $('.user__button--subscription');

    $(subBtn).on('click', function () {
        var subBtn = $(this);
        var isSubData = $(this).attr('data-issub');
        var userId = $(this).attr('data-user-id');
        var span = $(this).children('span');
        span.css('color', 'transparent');
        var preloader = $('<div>', {id: 'sub__preloader'});
        var msgBlock = $('<a>', {class: 'profile__user-button user__button user__button--writing button button--green',
                                 href: 'messages.php?block=messages&user=' + userId,
                                 text: 'Сообщение'});
        if (+isSubData === 1) {
            subBtn.append(preloader);
            $.ajax({
                url: 'subscribe.php',
                type: 'GET',
                data: 'user=' + userId,
                success: function () {
                    preloader.detach();
                    span.css('color', '#fff');
                    span.text('Подписаться');
                    $(subBtn).attr('data-issub', '0');
                    $('a.user__button--writing').fadeOut(300, function () {
                        $(this).detach();
                    });
                }
            });
        } else if (+isSubData === 0) {
            subBtn.append(preloader);
            $.ajax({
                url: 'subscribe.php',
                type: 'GET',
                data: 'user=' + userId,
                success: function () {
                    preloader.detach();
                    span.css('color', '#fff');
                    span.text('Отписаться');
                    $(subBtn).attr('data-issub', '1');
                    subBtn.fadeIn(400, function (){
                        $(this).parent().append(msgBlock);
                    });
                }
            });
        }
    });
});

/*
* Добавляет в поисковое поле тег по которому ведёться поиск
* */
$(document).ready(function () {
    var tagsBtn = $('.post__tags-btn');
    var searchInput = $('.header__search-input');
    var searchSubmit = $('.header__search-button');
    $(tagsBtn).on('click', function (e) {
        e.preventDefault();
        var tagsBtnVal = $(this).text();
        searchInput.val(tagsBtnVal);
        searchSubmit.click();
    });
});
