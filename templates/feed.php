<?php
/**
 * @var string $getTab тип сортировки контента из строки запроса
 * @var array $cards моссив с постами
 * @var string $cardType тип контента поста
 * @var string $cardPostId id поста
 * @var string $cardPostDate дата поста
 * @var string $cardAva ссылка на картинку аватар автора поста
 * @var string $cardName имя автор поста
 * @var string $cardUserId id автора поста
 * @var string $cardTitle заголовок поста
 * @var string $cardContent контент поста
 * @var string $cardQuoteAuthor автор цитаты для поста-цитаты
 * @var string $cardUserSiteId ссылка для поста-ссылки
 * @var string $cardVideoUrl ссылка на видео для поста-видео
 * @var string $cardImgUrl ссылка на фото для поста-фото
 * @var mysqli $con ресурс соединения
 */
?>

<main class="page__main page__main--feed">
    <div class="container">
        <h1 class="page__title page__title--feed">Моя лента</h1>
    </div>
    <div class="page__main-wrapper container">
        <section class="feed">
            <h2 class="visually-hidden">Лента</h2>
            <div class="feed__main-wrapper">
                <div class="feed__wrapper">
                    <?php foreach ($cards
                    as $key => $card): ?>
                    <?php
                    $cardType = $card['type'];
                    $cardPostId = $card['posts_id'];
                    $cardPostDate = $card['post_date'];
                    $cardAva = $card['avatar'];
                    $cardName = $card['name'];
                    $cardUserId = $card['users_id'];
                    $cardTitle = $card['title'];
                    $cardContent = $card['content'];
                    $cardQuoteAuthor = $card['quote_author'];
                    $cardUserSiteId = $card['users_site_url'];
                    $cardVideoUrl = $card['video_url'];
                    $cardImgUrl = $card['img_url'];
                    ?>
                    <article class="feed__post post post-photo">
                        <header class="post__header post__author">
                            <a class="post__author-link" href="post.php?postId=<?= $cardPostId ?>"
                               title="<?= $cardName ?>">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar" src="<?= $cardAva ?>"
                                         alt="Аватар пользователя" width="60" height="60">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= $cardName ?></b>
                                    <span class="post__time"><?= showTimeGap($cardPostDate) . ' назад ' ?></span>
                                </div>
                            </a>
                        </header>
                        <div class="post__main">
                            <?php if ($cardType === 'picture'): ?>
                                    <h2><a href="post.php?postId=<?= $cardPostId ?>"><?= $cardTitle ?></a></h2>
                                    <div class="post-photo__image-wrapper">
                                        <img src="<?= $cardContent ?>" alt="Фото от пользователя" width="760"
                                             height="396">
                                    </div>
                            <?php endif ?>
                            <?php if ($cardType === 'text'): ?>
                                <h2><a href="post.php?postId=<?= $cardPostId ?>" ><?= $cardTitle ?></a></h2>
                                <p style="margin-left: 7%" ><?= cutText($cardContent) ?></p>
                            <?php endif ?>
                            <?php if ($cardType === 'video'): ?>
                                    <iframe width="100%" height="240" src="<?= $cardVideoUrl ?>" frameborder="0"
                                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                            <?php endif ?>
                            <?php if ($cardType === 'quote'): ?>
                                <blockquote>
                                    <p>
                                     <?= $cardContent ?>
                                    </p>
                                    <cite><?= $cardQuoteAuthor ?></cite>
                                </blockquote>
                            <?php endif ?>
                            <?php if ($cardType === 'url'): ?>
                                <div class="post-link__wrapper">
                                    <a class="<?= $cardUserSiteId ?>" href="<?= $cardUserSiteId ?>"
                                       title="Перейти по ссылке">
<!--                                        <div class="post-link__icon-wrapper">-->
<!--                                            <img src="img/logo-vita.jpg" alt="Иконка">-->
<!--                                        </div>-->
                                        <div class="post-link__info">
                                            <h3><?= $cardTitle ?></h3>
                                            <p><?= $cardContent ?></p>
                                            <span><?= $cardUserSiteId ?></span>
                                        </div>
                                        <svg class="post-link__arrow" width="11" height="16">
                                            <use xlink:href="#icon-arrow-right-ad"></use>
                                        </svg>
                                    </a>
                                </div>
                            <?php endif ?>
                        </div>
                        <footer class="post__footer post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button" title="Лайк"
                                   data-post-id='<?= $cardPostId ?>'>
                                    <svg class="post__indicator-icon" width="20" height="17">
                                        <use xlink:href="#icon-heart<?= db_get_like($con, $cardPostId,
                                            $_SESSION['user-id']) ? '-active' : '' ?>"></use>
                                    </svg>
                                    <span class="<?= db_get_like($con, $cardPostId,
                                        $_SESSION['user-id']) ? '' : 'like-counter' ?>"><?= db_count_likes_to_post($con,
                                            $cardPostId) ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button" href="post.php?postId=<?= $cardPostId ?>"
                                   title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= db_count_comments_to_post($con, $cardPostId) ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                                <a class="post__indicator post__indicator--repost button" href="#"
                                   title="Репост">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-repost"></use>
                                    </svg>
                                    <span>5</span>
                                    <span class="visually-hidden">количество репостов</span>
                                </a>
                            </div>
                        </footer>
                    </article>
                        <?php endforeach ?>
                </div>
            </div>
            <ul class="feed__filters filters">
                <li class="feed__filters-item filters__item">
                    <a class="filters__button<?= $getTab === 'all' ? ' filters__button--active' : '' ?>"
                       href="?block=feed&tab=all">
                        <span>Все</span>
                    </a>
                </li>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--photo button <?= $getTab === 'photo' ? ' filters__button--active' : '' ?>"
                       href="?block=feed&tab=photo">
                        <span class="visually-hidden">Фото</span>
                        <svg class="filters__icon" width="22" height="18">
                            <use xlink:href="#icon-filter-photo"></use>
                        </svg>
                    </a>
                </li>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--video button<?= $getTab === 'video' ? ' filters__button--active' : '' ?>"
                       href="?block=feed&tab=video">
                        <span class="visually-hidden">Видео</span>
                        <svg class="filters__icon" width="24" height="16">
                            <use xlink:href="#icon-filter-video"></use>
                        </svg>
                    </a>
                </li>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--text button<?= $getTab === 'text' ? ' filters__button--active' : '' ?>"
                       href="?block=feed&tab=text">
                        <span class="visually-hidden">Текст</span>
                        <svg class="filters__icon" width="20" height="21">
                            <use xlink:href="#icon-filter-text"></use>
                        </svg>
                    </a>
                </li>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--quote button<?= $getTab === 'quote' ? ' filters__button--active' : '' ?>"
                       href="?block=feed&tab=quote">
                        <span class="visually-hidden">Цитата</span>
                        <svg class="filters__icon" width="21" height="20">
                            <use xlink:href="#icon-filter-quote"></use>
                        </svg>
                    </a>
                </li>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--link button<?= $getTab === 'url' ? ' filters__button--active' : '' ?>"
                       href="?block=feed&tab=url">
                        <span class="visually-hidden">Ссылка</span>
                        <svg class="filters__icon" width="21" height="18">
                            <use xlink:href="#icon-filter-link"></use>
                        </svg>
                    </a>
                </li>
            </ul>
        </section>
        <aside class="promo">
            <article class="promo__block promo__block--barbershop">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
                </p>
                <a class="promo__link" href="#">
                    Подробнее
                </a>
            </article>
            <article class="promo__block promo__block--technomart">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Товары будущего уже сегодня в онлайн-сторе Техномарт!
                </p>
                <a class="promo__link" href="#">
                    Перейти в магазин
                </a>
            </article>
            <article class="promo__block">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Здесь<br> могла быть<br> ваша реклама
                </p>
                <a class="promo__link" href="#">
                    Разместить
                </a>
            </article>
        </aside>
    </div>
</main>
