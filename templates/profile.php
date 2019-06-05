<?php
/**
 * @var string $getTab тип контента
 * @var string $getUser пользователь из GET запроса
 * @var array $user массив данных о пользователе
 * @var string userData['avatar'] аватар пользователя
 * @var string userData['name'] имя пользователя
 * @var string userData['registration_date'] дата регистрации пользователя
 *
 * @var array $cards массив данных о постах пользователся
 * @var int $card ['posts_id'] id поста
 * @var string $card ['title'] Заголовок поста
 * @var string $card ['content'] содержимое поста
 * @var string $card ['content-types-id'] id типа контента
 * @var string $card ['quote_author'] автор цитаты
 * @var string $card ['user_site_url'] ссылка от пользователя
 * @var string $card ['video_url'] ссылка на видео
 * @var string $card ['post_date'] дата поста
 * @var string $hashtag ['name'] слово - хештег
 *
 * @var array $userSession данные о пользователе у которого открыта сессия
 * @var string $userSession ['user-name'] имя пользователя
 * @var string $userSession ['user-ava'] ссылка на аватар пользователя
 * @var string $userSession ['user-id '] id пользователя
 *
 * @var array $likers информация о пользователях которые лайкнули ваши посты
 * @var int $likeData ['posts_id'] пост который был лайкнту
 * @var string $likeData ['conten'] текст поста
 * @var int $likeData ['users_id'] id пользователя который лайкнул ваш пост
 * @var string $likeData ['name'] имы пользователя
 * @var string $likeData ['likes_date'] дата лайка
 *
 * @var array $subsList массив данных о подписчиках
 * @var string $sub ['name'] имя пользователя
 * @var string $sub ['registration_date'] дата регистрации
 * @var string $sub ['avatar'] ссылка на автар
 * @var int $sub ['postCount'] количество постов
 * @var int $sub ['subCount'] количество подписчиков
 *
 */
//var_dump();
?>
<?php foreach ($user

as $userData) ?>
<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--default">
        <div class="profile__user-wrapper">
            <div class="profile__user user container">
                <div class="profile__user-info user__info">
                    <div class="profile__avatar user__avatar">
                        <img class="profile__picture user__picture" src="<?= $userData['avatar'] ?>" alt="">
                    </div>
                    <div class="profile__name-wrapper user__name-wrapper">
                        <span class="profile__name user__name"><?= $userData['name'] ?></span>
                        <time class="profile__user-time user__time" datetime="<?= date('Y.m.d.',
                            strtotime($userData['registration_date'])) ?>"><?= showTimeGap($userData['registration_date']) . ' на сайте' ?></time>
                    </div>
                </div>
                <div class="profile__rating user__rating">
                    <p class="profile__rating-item user__rating-item user__rating-item--publications">
                        <span class="user__rating-amount"><?= $userDataPosts ?></span>
                        <span class="profile__rating-text user__rating-text">публикаций</span>
                    </p>
                    <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="user__rating-amount"><?= $userDataSubs ?></span>
                        <span class="profile__rating-text user__rating-text">подписчиков</span>
                    </p>
                </div>
                <div class="profile__user-buttons user__buttons">
                    <a class="profile__user-button user__button user__button--subscription button button--main"
                       data-user-id="<?= $getUser ?>" data-issub="<?= dbCheckSubscription($con, $userSession['user-id'],
                        $getUser) ? '1' : '0' ?>"><span><?= dbCheckSubscription($con, $userSession['user-id'],
                                $getUser) ? 'Отписаться' : 'Подписаться' ?></span></a>
                    <?php if (dbCheckSubscription($con, $userSession['user-id'], $userData['users_id'])): ?>
                        <a class="profile__user-button user__button user__button--writing button button--green"
                           href="messages.php?block=messages&user=<?= $getUser ?>">Сообщение</a>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="profile__tabs-wrapper tabs">
            <div class="container">
                <div class="profile__tabs filters">
                    <b class="profile__tabs-caption filters__caption">Показать:</b>
                    <ul class="profile__tabs-list filters__list tabs__list">
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item tabs__item--active button<?= $getTab === 'posts' ? ' filters__button--active' : '' ?>"
                               href="?user=<?= $getUser ?>&tab=posts">Посты</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button<?= $getTab === 'likes' ? ' filters__button--active' : '' ?>"
                               href="?user=<?= $getUser ?>&tab=likes">Лайки</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button<?= $getTab === 'subs' ? ' filters__button--active' : '' ?>"
                               href="?user=<?= $getUser ?>&tab=subs">Подписки</a>
                        </li>
                    </ul>
                </div>
                <div class="profile__tab-content">
                    <section
                            class="profile__posts tabs__content<?= $getTab === 'posts' ? ' tabs__content--active' : '' ?>">
                        <?php if ($getTab === 'posts'): ?>
                            <h2 class="visually-hidden">Публикации</h2>
                            <?php foreach ($cards as $card): ?>
                                <article class="profile__post post post-photo">
                                    <header class="post__header">
                                        <?php if ($card['isrepost']): ?>
                                            <div class="post__author">
                                                <a class="post__author-link"
                                                   href="profile.php?user=<?= $card['user_author_id'] ?>&tab=posts"
                                                   title="Автор">
                                                    <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                                        <img class="post__author-avatar"
                                                             src="<?= dbGetUserAva($con, $card['user_author_id']) ?>"
                                                             alt="">
                                                    </div>
                                                    <div class="post__info">
                                                        <b class="post__author-name">Репост: <?= dbGetUserName($con,
                                                                $card['user_author_id']) ?></b>
                                                        <time class="post__time" datetime="<?= date('Y-m-D H:i',
                                                            strtotime(dbGetPostDate($con,
                                                                $card['post_origin_id']))) ?>"><?= showTimeGap(dbGetPostDate($con,
                                                                $card['post_origin_id'])) ?> назад
                                                        </time>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif ?>

                                        <h2><a href='post.php?postId=<?= $card['posts_id'] ?>'><?= $card['title'] ?></a>
                                        </h2>
                                    </header>
                                    <div class="post__main">
                                        <?php if ($card['content_types_id'] === 2): ?>
                                            <blockquote>
                                                <p>
                                                    <?= $card['content'] ?>
                                                </p>
                                                <cite><?= $card['quote_author'] ?></cite>
                                            </blockquote>


                                        <?php elseif ($card['content_types_id'] === 5): ?>
                                            <div class="post-link__wrapper">
                                                <a class="post-link__external" href="<?= $card['users_site_url'] ?>"
                                                   title="Перейти по ссылке">
                                                    <div class="post-link__info-wrapper">
                                                        <div class="post-link__info">
                                                            <h3><?= $card['title'] ?></h3>
                                                        </div>
                                                    </div>
                                                    <span><?= $card['content'] ?></span>
                                                </a>
                                            </div>
                                        <?php elseif ($card['content_types_id'] === 3): ?>
                                            <div class="post-photo__image-wrapper">
                                                <img src="<?= $card['content'] ?>" alt="Фото от пользователя"
                                                     width="760" height="396">
                                            </div>
                                        <?php elseif ($card['content_types_id'] === 4): ?>
                                            <div class="post-photo__image-wrapper">
                                                <div class="post__main">
                                                    <?= embed_youtube_video($card['video_url']) ?>
                                                </div>
                                            </div>
                                        <?php elseif ($card['content_types_id'] === 1): ?>
                                            <p style="margin-left: 7%">
                                                <?= cutText($card['content'], 200, $card['posts_id']) ?></p>
                                        <?php endif ?>
                                    </div>

                                    <footer class="post__footer">
                                        <div class="post__indicators">
                                            <div class="post__buttons">
                                                <a class="post__indicator post__indicator--likes button" title="Лайк"
                                                   data-post-id='<?= $card['posts_id'] ?>'>
                                                    <svg class="post__indicator-icon" width="20" height="17">
                                                        <use xlink:href="#icon-heart<?= dbGetLike($con,
                                                            $card['posts_id'],
                                                            $userSession['user-id']) ? '-active' : '' ?>"></use>
                                                    </svg>
                                                    <span class="<?= dbGetLike($con, $card['posts_id'],
                                                        $userSession['user-id']) ? '' : 'like-counter' ?>"><?= dbCountLikesToPost($con,
                                                            $card['posts_id']) ?></span>
                                                    <span class="visually-hidden">количество лайков</span>
                                                </a>
                                                <a class="post__indicator post__indicator--repost button"
                                                   href="profile.php?postId=<?= $card['posts_id'] ?>&repost=1&tab=posts"
                                                   title="Репост">
                                                    <svg class="post__indicator-icon" width="19" height="17">
                                                        <use xlink:href="#icon-repost"></use>
                                                    </svg>
                                                    <span><?= $card['isrepost'] ? dbGetPostReposts($con,
                                                            $card['post_origin_id']) : dbGetPostReposts($con,
                                                            $card['posts_id']) ?></span>
                                                    <span class="visually-hidden">количество репостов</span>
                                                </a>
                                            </div>
                                            <time class="post__time" datetime="<?= date('d.m.Y H:i',
                                                strtotime($card['post_date'])) ?>"><?= showTimeGap($card['post_date']) ?>
                                                назад
                                            </time>
                                        </div>
                                        <?php $hashtags = dbGetAllHashtagsToPost($con, $card['posts_id']) ?>
                                        <ul class="post__tags">
                                            <?php foreach ($hashtags as $key => $hashtag): ?>
                                                <li>
                                                    <a class="post__tags-btn"
                                                       title="Поиск по тэгу <?= $hashtag['name'] ?>">#<?= $hashtag['name'] ?></a>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>
                                    </footer>
                                    <div class="comments">
                                        <a class="comments__button button"
                                           href='post.php?postId=<?= $card['posts_id'] ?>#comments'>Показать
                                            комментарии</a>
                                    </div>
                                </article>
                            <?php endforeach ?>
                            <?php if (empty($cards)) : ?>
                                <p>Постов от этого пользователя не найдено</p>
                            <?php endif ?>
                        <?php endif ?>
                    </section>

                    <section
                            class="profile__likes tabs__content<?= $getTab === 'likes' ? ' tabs__content--active' : '' ?>">
                        <?php if ($getTab === 'likes'): ?>
                            <h2 class="visually-hidden">Лайки</h2>
                            <ul class="profile__likes-list">
                                <?php foreach ($likers as $likeData): ?>
                                    <?php if ($likeData['type'] === 'picture'): ?>
                                        <li class="post-mini post-mini--photo post user">
                                            <div class="post-mini__user-info user__info">
                                                <div class="post-mini__avatar user__avatar">
                                                    <a class="user__avatar-link"
                                                       href="profile.php?user=<?= $likeData['users_id'] ?>">
                                                        <img class="post-mini__picture user__picture"
                                                             src="<?= $likeData['avatar'] ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="post-mini__name-wrapper user__name-wrapper">
                                                    <a class="post-mini__name user__name"
                                                       href="profile.php?user=<?= $likeData['users_id'] ?>">
                                                        <span><?= $likeData['name'] ?></span>
                                                    </a>
                                                    <div class="post-mini__action">
                                                        <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                                                        <time class="post-mini__time user__additional"
                                                              datetime="<?= date('Y.m.d H:i',
                                                                  $likeData['likes_date']) ?>"><?= showTimeGap($likeData['likes_date']) ?>
                                                            назад
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="post-mini__preview">
                                                <a class="post-mini__link"
                                                   href="post.php?postId=<?= $likeData['posts_id'] ?>"
                                                   title="Перейти на публикацию">
                                                    <div class="post-mini__image-wrapper">
                                                        <img class="post-mini__image" src="<?= $likeData['content'] ?>"
                                                             width="109" height="109" alt="">
                                                    </div>
                                                    <span class="visually-hidden">Фото</span>
                                                </a>
                                            </div>
                                        </li>
                                    <?php elseif ($likeData['type'] === 'text'): ?>
                                        <li class="post-mini post-mini--text post user">
                                            <div class="post-mini__user-info user__info">
                                                <div class="post-mini__avatar user__avatar">
                                                    <a class="user__avatar-link"
                                                       href="profile.php?user=<?= $likeData['users_id'] ?>">
                                                        <img class="post-mini__picture user__picture"
                                                             src="<?= $likeData['avatar'] ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="post-mini__name-wrapper user__name-wrapper">
                                                    <a class="post-mini__name user__name"
                                                       href="profile.php?user=<?= $likeData['users_id'] ?>">
                                                        <span><?= $likeData['name'] ?></span>
                                                    </a>
                                                    <div class="post-mini__action">
                                                        <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                                                        <time class="post-mini__time user__additional"
                                                              datetime="<?= date('Y.m.d H:i',
                                                                  $likeData['likes_date']) ?>"><?= showTimeGap($likeData['likes_date']) ?>
                                                            назад
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="post-mini__preview">
                                                <a class="post-mini__link" href="#" title="Перейти на публикацию">
                                                    <span class="visually-hidden">Текст</span>
                                                    <svg class="post-mini__preview-icon" width="20" height="21">
                                                        <use xlink:href="#icon-filter-text"></use>
                                                    </svg>
                                                </a>
                                            </div>
                                        </li>
                                    <?php elseif ($likeData['type'] === 'video'): ?>
                                        <li class="post-mini post-mini--video post user">
                                            <div class="post-mini__user-info user__info">
                                                <div class="post-mini__avatar user__avatar">
                                                    <a class="user__avatar-link"
                                                       href="profile.php?user=<?= $likeData['users_id'] ?>">
                                                        <img class="post-mini__picture user__picture"
                                                             src="<?= $likeData['avatar'] ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="post-mini__name-wrapper user__name-wrapper">
                                                    <a class="post-mini__name user__name"
                                                       href="profile.php?user=<?= $likeData['users_id'] ?>">
                                                        <span><?= $likeData['name'] ?></span>
                                                    </a>
                                                    <div class="post-mini__action">
                                                        <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                                                        <time class="post-mini__time user__additional"
                                                              datetime="<?= date('Y.m.d H:i',
                                                                  $likeData['likes_date']) ?>"><?= showTimeGap($likeData['likes_date']) ?>
                                                            назад
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="post-mini__preview">
                                                <a class="post-mini__link"
                                                   href="post.php?postId=<?= $likeData['posts_id'] ?>"
                                                   title="Перейти на публикацию">
                                                    <div class="post-mini__image-wrapper">
                                                        <div class="post__main">
                                                            <?= $card['video_url'] ?>
                                                        </div>
                                                        <span class="post-mini__play-big">
                            <svg class="post-mini__play-big-icon" width="12" height="13">
                              <use xlink:href="#icon-video-play-big"></use>
                            </svg>
                          </span>
                                                    </div>
                                                    <span class="visually-hidden">Видео</span>
                                                </a>
                                            </div>
                                        </li>
                                    <?php elseif ($likeData['type'] === 'quote'): ?>
                                        <li class="post-mini post-mini--quote post user">
                                            <div class="post-mini__user-info user__info">
                                                <div class="post-mini__avatar user__avatar">
                                                    <a class="user__avatar-link" href="#">
                                                        <img class="post-mini__picture user__picture"
                                                             src="<?= $likeData['avatar'] ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="post-mini__name-wrapper user__name-wrapper">
                                                    <a class="post-mini__name user__name"
                                                       href="profile.php?userId=<?= $likeData['users_id'] ?>">
                                                        <span><?= $likeData['name'] ?></span>
                                                    </a>
                                                    <div class="post-mini__action">
                                                        <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                                                        <time class="post-mini__time user__additional"
                                                              datetime="<?= date('Y.m.d H:i',
                                                                  $likeData['likes_date']) ?>"><?= showTimeGap($likeData['likes_date']) ?>
                                                            назад
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="post-mini__preview">
                                                <a class="post-mini__link" href="#" title="Перейти на публикацию">
                                                    <span class="visually-hidden">Цитата</span>
                                                    <svg class="post-mini__preview-icon" width="21" height="20">
                                                        <use xlink:href="#icon-filter-quote"></use>
                                                    </svg>
                                                </a>
                                            </div>
                                        </li>
                                    <?php elseif ($likeData['type'] === 'url'): ?>
                                        <li class="post-mini post-mini--link post user">
                                            <div class="post-mini__user-info user__info">
                                                <div class="post-mini__avatar user__avatar">
                                                    <a class="user__avatar-link" href="#">
                                                        <img class="post-mini__picture user__picture"
                                                             src="<?= $likeData['avatar'] ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="post-mini__name-wrapper user__name-wrapper">
                                                    <a class="post-mini__name user__name"
                                                       href="profile.php?user=<?= $likeData['users_id'] ?>">
                                                        <span><?= $likeData['name'] ?></span>
                                                    </a>
                                                    <div class="post-mini__action">
                                                        <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                                                        <time class="post-mini__time user__additional"
                                                              datetime="<?= date('Y.m.d H:i',
                                                                  $likeData['likes_date']) ?>"><?= showTimeGap($likeData['likes_date']) ?>
                                                            назад
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="post-mini__preview">
                                                <a class="post-mini__link" href="#" title="Перейти на публикацию">
                                                    <span class="visually-hidden">Ссылка</span>
                                                    <svg class="post-mini__preview-icon" width="21" height="18">
                                                        <use xlink:href="#icon-filter-link"></use>
                                                    </svg>
                                                </a>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach ?>
                            </ul>
                            <?php if (empty($likers)) : ?>
                                <p>Лайков не найдено</p>
                            <?php endif ?>
                        <?php endif ?>
                    </section>

                    <section
                            class="profile__subscriptions tabs__content<?= $getTab === 'subs' ? ' tabs__content--active' : '' ?>">
                        <h2 class="visually-hidden">Подписки</h2>
                        <ul class="profile__subscriptions-list">
                            <?php foreach ($subsList as $sub): ?>
                                <li class="post-mini post-mini--photo post user">
                                    <div class="post-mini__user-info user__info">
                                        <div class="post-mini__avatar user__avatar">
                                            <a class="user__avatar-link"
                                               href="profile.php?user=<?= $sub['users_id'] ?>">
                                                <img class="post-mini__picture user__picture"
                                                     src="<?= $sub['avatar'] ?>"
                                                     alt="">
                                            </a>
                                        </div>
                                        <div class="post-mini__name-wrapper user__name-wrapper">
                                            <a class="post-mini__name user__name"
                                               href="profile.php?user=<?= $sub['users_id'] ?>">
                                                <span><?= $sub['name'] ?></span>
                                            </a>
                                            <time class="post-mini__time user__additional"
                                                  datetime="<?= date('Y-m-d H:i',
                                                      strtotime($sub['registration_date'])) ?>">
                                                <?= showTimeGap($sub['registration_date']) ?> на сайте
                                            </time>
                                        </div>
                                    </div>
                                    <div class="post-mini__rating user__rating">
                                        <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                                            <span class="post-mini__rating-amount user__rating-amount"><?= $sub['postsCount'] ?></span>
                                            <span class="post-mini__rating-text user__rating-text">публикаций</span>
                                        </p>
                                        <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                                            <span class="post-mini__rating-amount user__rating-amount"><?= $sub['subsCount'] ?></span>
                                            <span class="post-mini__rating-text user__rating-text">подписчиков</span>
                                        </p>
                                    </div>
                                    <div class="post-mini__user-buttons user__buttons">
                                        <a class="profile__user-button user__button user__button--subscription button button--main"
                                           data-user-id="<?= $sub['users_id'] ?>"
                                           data-issub="<?= dbCheckSubscription($con, $userSession['user-id'],
                                               $sub['users_id']) ? '1' : '0' ?>"><span><?= dbCheckSubscription($con,
                                                    $userSession['user-id'],
                                                    $sub['users_id']) ? 'Отписаться' : 'Подписаться' ?></span></a>
                                    </div>
                                </li>
                            <?php endforeach ?>
                            <?php if (empty($subsList)) : ?>
                                <p>Подписчиков не найдено</p>
                            <?php endif ?>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>
