<?php
/**
 * @var string $getTab тип контента из строки запроса
 * @var array $cards массив постов
 * @var string $cardType  тип поста
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
 * @var mysqli $con ресурс соединения
 * @var array $pagesCount массив с количеством страниц
 */
?>

<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link sorting__link--active" href="#">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all <?= $getTab === 'all' ? 'filters__button--active' : '' ?>"
                           href="?block=pop&tab=all&page=1">
                            <span>Все</span>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--photo button<?= $getTab === 'photo' ? ' filters__button--active' : '' ?>"
                           href="?block=pop&tab=photo&page=1">
                            <span class="visually-hidden">Фото</span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-photo"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--video button<?= $getTab === 'video' ? ' filters__button--active' : '' ?>"
                           href="?block=pop&tab=video&page=1">
                            <span class="visually-hidden">Видео</span>
                            <svg class="filters__icon" width="24" height="16">
                                <use xlink:href="#icon-filter-video"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--text button<?= $getTab === 'text' ? ' filters__button--active' : '' ?>"
                           href="?block=pop&tab=text&page=1">
                            <span class="visually-hidden">Текст</span>
                            <svg class="filters__icon" width="20" height="21">
                                <use xlink:href="#icon-filter-text"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--quote button<?= $getTab === 'quote' ? ' filters__button--active' : '' ?>"
                           href="?block=pop&tab=quote&page=1">
                            <span class="visually-hidden">Цитата</span>
                            <svg class="filters__icon" width="21" height="20">
                                <use xlink:href="#icon-filter-quote"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--link button<?= $getTab === 'url' ? ' filters__button--active' : '' ?>"
                           href="?block=pop&tab=url&page=1">
                            <span class="visually-hidden">Ссылка</span>
                            <svg class="filters__icon" width="21" height="18">
                                <use xlink:href="#icon-filter-link"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="popular__posts" id="popular__posts">
            <?php foreach ($cards as $key => $card): ?>
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
                ?>
                <article class="popular__post post <?= $cardType ?>">
                    <header class="post__header">
                        <h2><a href='post.php?postId=<?= $cardPostId ?>'><?= $cardTitle ?></a></h2>
                    </header>
                    <div class="post__main">
                        <?php if ($cardType === 'quote'): ?>
                            <blockquote>
                                <p>
                                    <?= htmlspecialchars($cardContent) ?>
                                </p>
                                <cite><?= htmlspecialchars($cardQuoteAuthor) ?></cite>
                            </blockquote>
                        <?php elseif ($cardType === 'url'): ?>
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="<?= $cardUserSiteId ?>"
                                   title="Перейти по ссылке">
                                    <div class="post-link__info-wrapper">
                                        <div class="post-link__info">
                                            <h3><?= htmlspecialchars($cardTitle) ?></h3>
                                        </div>
                                    </div>
                                    <span><?= htmlspecialchars($cardContent) ?></span>
                                </a>
                            </div>
                        <?php elseif ($cardType === 'picture'): ?>
                            <div class="post-photo__image-wrapper">
                                <img src="<?= htmlspecialchars($cardContent) ?>" alt="Фото от пользователя"
                                     width="360" height="240">
                            </div>
                        <?php elseif ($cardType === 'video'): ?>
                            <div class="post-photo__image-wrapper">
                                <iframe width="100%" height="240" src="<?= $cardVideoUrl ?>" frameborder="0"
                                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                            </div>
                        <?php elseif ($cardType === 'text'): ?>
                            <p style="margin-left: 7%">
                                <?= htmlspecialchars(cutText($cardContent)) ?></p>
                        <?php endif ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link" href="profile.php?user=<?= $cardUserId ?>&tab=posts"
                               title="<?= $cardName ?>">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar" src="img/<?= htmlspecialchars($cardAva) ?>">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name">
                                        <?= htmlspecialchars($cardName) ?></b>
                                    <time class="post__time" datetime="<?= $cardPostDate ?>"
                                          title="<?= date('d.m.Y H:i',
                                              strtotime($cardPostDate)) ?>"><?= showTimeGap($cardPostDate) . ' назад' ?></time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
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
                                <a class="post__indicator post__indicator--comments button"
                                   href="post.php?postId=<?= $cardPostId ?>"
                                   title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= db_count_comments_to_post($con, $cardPostId) ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endforeach ?>
            <p class="<?= isset($cardTitle) ? 'visually-hidden' : '' ?>">По данному запросу ни одного поста не
                найдено</p>
        </div>
        <?php if($pagesCount > 1): ?>
        <div class="popular__page-links">
            <?php if($curPage > 1): ?>
            <a class="popular__page-link popular__page-link--prev button button--gray" href="?tab=<?= $getTab ?>&page=<?= $curPage - 1 ?>">Предыдущая страница</a>
            <?php endif; ?>
            <?php if($pagesCount - $curPage): ?>
            <a class="popular__page-link popular__page-link--next button button--gray" href="?tab=<?= $getTab ?>&page=<?= $curPage + 1 ?>">Следующая страница</a>
            <?php endif; ?>
        </div>
        <?php endif ?>
    </div>
</section>
