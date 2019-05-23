<?php
/**
 * @var string $search поисковый запрос
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
<main class="page__main page__main--search-results">
    <h1 class="visually-hidden">Страница результатов поиска</h1>
    <section class="search">
        <h2 class="visually-hidden">Результаты поиска</h2>
        <div class="search__query-wrapper">
            <div class="search__query container">
                <span>Вы искали:</span>
                <span class="search__query-text"><?= $search ?></span>
            </div>
        </div>
        <div class="search__results-wrapper">
            <div class="container">
                <div class="search__content">
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
                    <article class="search__post post post-photo">
                        <header class="post__header post__author">
                            <a class="post__author-link" href="post.php?postId=<?= $cardPostId ?>" title="<?= $cardName ?>">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar" src="<?= $cardAva ?>" alt="Аватар пользователя" width="60" height="60">
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
                                <img src="<?= $cardContent ?>" alt="Фото от пользователя" width="760" height="396">
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
                                        <use xlink:href="#icon-heart<?= dbGetLike($con, $cardPostId,
                                            $userSession['user-id']) ? '-active' : '' ?>"></use>
                                    </svg>
                                    <span class="<?= dbGetLike($con, $cardPostId,
                                        $userSession['user-id']) ? '' : 'like-counter' ?>"><?= dbCountLikesToPost($con,
                                            $cardPostId) ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button" href="post.php?postId=<?= $cardPostId ?>"
                                   title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= dbCountCommentsToPost($con, $cardPostId) ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                                <?php $hashtags = dbGetAllHashtagsToPost($con, $cardPostId) ?>
                                <ul class="post__tags">
                                    <?php foreach ($hashtags as $val => $hashtag): ?>
                                        <li><a href="../search.php?q=#<?= $hashtag['name'] ?>" >#<?= $hashtag['name'] ?></a></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </footer>
                    </article>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </section>
</main>
