<?php
/**
 * @var array $errors массив с ошибками валидации поля комментария
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
 * @var array $user массив информации о пользователе
 * @var int $userDataPosts количество постов пользователя
 * @var int $userDataSubs количество подписчиков пользователя
 * @var int $postId id поста
 * @var array $comments массив комментариев
 * @var string $commentText текст комментария
 */
?>
<?php foreach ($cards as $key => $card) ?>
<?php foreach ($user

as $key => $userData) ?>
<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= $card['title'] ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper">
                <div class="post-details__main-block post post--details">
                    <?php if ($card['type'] === 'quote'): ?>
                        <div class="post-details__image-wrapper post-quote">
                            <div class="post__main">
                                <blockquote>
                                    <p>
                                        <?= $card['content'] ?>
                                    </p>
                                    <cite> <?= $card['quote_author'] ?></cite>
                                </blockquote>
                            </div>
                        </div>
                    <?php elseif ($card['type'] === 'video'): ?>
                        <div class="post-photo__image-wrapper">
                            <div class="post__main post__iframe">
                                <?= embed_youtube_video($card['video_url']) ?>
                            </div>
                        </div>
                    <?php elseif ($card['type'] === 'text'): ?>
                        <div class="post-details__image-wrapper post-text">
                            <div class="post__main">
                                <p>
                                    <?= $card['content'] ?>
                                </p>
                            </div>
                        </div>
                    <?php elseif ($card['type'] === 'url'): ?>
                        <div class="post__main">
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="<?= $card['users_site_url'] ?>"
                                   title="Перейти по ссылке">
                                    <div class="post-link__info-wrapper">
                                        <div class="post-link__icon-wrapper">
                                            <img src="#" alt="Иконка">
                                        </div>
                                        <div class="post-link__info">
                                            <h3><?= $card['title'] ?></h3>
                                        </div>
                                    </div>
                                    <span><?= $card['users_site_url'] ?></span>
                                </a>
                            </div>
                        </div>
                    <?php elseif ($card['type'] === 'picture'): ?>
                        <div class="post-details__image-wrapper post-photo__image-wrapper">
                            <img src="<?= $card['content'] ?>" alt="Фото от пользователя" width="760" height="507">
                        </div>
                    <?php endif ?>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" title="Лайк"
                               data-post-id='<?= $postId ?>'>
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart<?= dbGetLike($con, $postId,
                                        $userSession['user-id']) ? '-active' : '' ?>"></use>
                                </svg>
                                <span class="<?= dbGetLike($con, $postId,
                                    $userSession['user-id']) ? '' : 'like-counter' ?>"><?= dbCountLikesToPost($con,
                                        $postId) ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= dbCountCommentsToPost($con, $postId) ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                            <?php if ($card['isrepost']) : ?>
                                <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-repost"></use>
                                    </svg>
                                    <span><?= dbGetPostReposts($con, $postId) ?></span>
                                    <span class="visually-hidden">количество репостов</span>
                                </a>
                            <?php endif ?>
                        </div>
                        <span class="post__view"><?= dbGetViewPost($con,
                                $postId) . ' ' . get_noun_plural_form(dbGetPostViews($con, $postId), 'просмотр',
                                'просмотры', 'просмотров') ?></span>
                    </div>
                    <?php $hashtags = dbGetAllHashtagsToPost($con, $postId) ?>
                    <ul class="post__tags">
                        <?php foreach ($hashtags as $hashtag): ?>
                            <li><a class="post__tags-btn"
                                   title="Поиск по тэгу <?= $hashtag['name'] ?>">#<?= $hashtag['name'] ?></a></li>
                        <?php endforeach ?>
                    </ul>
                    <div class="comments" id="comments">
                        <form class="comments__form form" action="?postId=<?= $postId ?>" method="post">
                            <div class="comments__my-avatar">
                                <img class="comments__picture" src="<?= $userSession['user-ava'] ?>"
                                     alt="">
                            </div>
                            <textarea
                                    class="comments__textarea form__textarea<?= isset($errors['comment-error']) ? ' form__textarea--error' : '' ?>"
                                    placeholder="Ваш комментарий"
                                    name="comment-text"><?= htmlspecialchars($commentText) ?></textarea>
                            <label class="visually-hidden">Ваш комментарий</label>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                            <p class="<?= isset($errors) ? ' comments__error' : ' visually-hidden' ?>"><?= $errors['comment-error'] ?></p>
                        </form>
                        <div class="comments__list-wrapper">
                            <ul class="comments__list">
                                <?php foreach ($comments as $key => $comment): ?>
                                    <li class="comments__item user">
                                        <div class="comments__avatar">
                                            <a class="user__avatar-link" href="#">
                                                <img class="comments__picture" src="<?= $comment['avatar'] ?>"
                                                     alt="">
                                            </a>
                                        </div>
                                        <div class="comments__info">
                                            <div class="comments__name-wrapper">
                                                <a class="comments__user-name"
                                                   href="profile.php?user=<?= $comment['users_id'] ?>&tab=posts">
                                                    <span><?= $comment['name'] ?></span>
                                                </a>
                                                <time class="comments__time"
                                                      datetime="<?= $comment['data_time_of_origin'] ?>"
                                                      title="<?= date('d.m.Y H:i',
                                                          strtotime($comment['data_time_of_origin'])) ?>"><?= showTimeGap($comment['data_time_of_origin']) . ' назад' ?></time>
                                            </div>
                                            <p class="comments__text">
                                                <?= $comment['text'] ?>
                                            </p>
                                        </div>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                            <?php if (dbCountCommentsToPost($con, $postId) > 3 && (int)$allComments === 0): ?>
                                <a class="comments__more-link"
                                   href="post.php?postId=<?= $postId ?>&all=1&#end-of-comments">
                                    <span>Показать все комментарии</span>
                                    <sup class="comments__amount"><?= dbCountCommentsToPost($con, $postId) ?></sup>
                                </a>
                            <?php endif ?>
                            <div id="end-of-comments"></div>
                        </div>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link"
                               href="profile.php?user=<?= $card['users_id'] ?>">
                                <img class="post-details__picture user__picture" src="<?= $card['avatar'] ?>"
                                     alt="">
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name"
                               href="/profile.php?user=<?= $card['users_id'] ?>&tab=posts">
                                <span><?= $card['name'] ?></span>
                            </a>
                            <time class="post-details__time user__time" datetime="<?= date('Y.m.d.',
                                strtotime($userData['registration_date'])) ?>"><?= showTimeGap($userData['registration_date']) . ' на сайте' ?></time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span class="post-details__rating-amount user__rating-amount"><?= $userDataSubs ?></span>
                            <span class="post-details__rating-text user__rating-text">подписчиков</span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span class="post-details__rating-amount user__rating-amount"><?= $userDataPosts ?></span>
                            <span class="post-details__rating-text user__rating-text">публикаций</span>
                        </p>
                    </div>
                    <div class="post-details__user-buttons user__buttons">
                        <a class="profile__user-button user__button user__button--subscription button button--main"
                           data-user-id="<?= $userData['users_id'] ?>"
                           data-issub="<?= dbCheckSubscription($con, $userSession['user-id'],
                               $userData['users_id']) ? '1' : '0' ?>"><span><?= dbCheckSubscription($con,
                                    $userSession['user-id'],
                                    $userData['users_id']) ? 'Отписаться' : 'Подписаться' ?></span></a>
                        <?php if (dbCheckSubscription($con, $userSession['user-id'], $userData['users_id'])): ?>
                            <a class="profile__user-button user__button user__button--writing button button--green"
                               href="messages.php?block=messages&user=<?= $userData['users_id'] ?>">Сообщение</a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
