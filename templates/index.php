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
                        <a class="filters__button filters__button--ellipse filters__button--all filters__button--active" href="#">
                            <span>Все</span>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--photo button" href="#">
                            <span class="visually-hidden">Фото</span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-photo"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--video button" href="#">
                            <span class="visually-hidden">Видео</span>
                            <svg class="filters__icon" width="24" height="16">
                                <use xlink:href="#icon-filter-video"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--text button" href="#">
                            <span class="visually-hidden">Текст</span>
                            <svg class="filters__icon" width="20" height="21">
                                <use xlink:href="#icon-filter-text"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--quote button" href="#">
                            <span class="visually-hidden">Цитата</span>
                            <svg class="filters__icon" width="21" height="20">
                                <use xlink:href="#icon-filter-quote"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--link button" href="#">
                            <span class="visually-hidden">Ссылка</span>
                            <svg class="filters__icon" width="21" height="18">
                                <use xlink:href="#icon-filter-link"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="popular__posts">
            <div class="visually-hidden" id="donor">
                <!--содержимое для поста-цитаты-->
                <blockquote>
                    <p>
                        <!--здесь текст-->
                    </p>
                    <cite>Неизвестный Автор</cite>
                </blockquote>

                <!--содержимое для поста-ссылки-->
                <div class="post-link__wrapper">
                    <a class="post-link__external" href="http://" title="Перейти по ссылке">
                        <div class="post-link__info-wrapper">
                            <div class="post-link__icon-wrapper">
                                <img src="img/logo-vita.jpg" alt="Иконка">
                            </div>
                            <div class="post-link__info">
                                <h3><!--здесь заголовок--></h3>
                            </div>
                        </div>
                        <span><!--здесь ссылка--></span>
                    </a>
                </div>

                <!--содержимое для поста-фото-->
                <div class="post-photo__image-wrapper">
                    <img src="img/" alt="Фото от пользователя" width="360" height="240">
                </div>

                <!--содержимое для поста-текста-->
                <p><!--здесь текст--></p>
            </div>

            <?php

            date_default_timezone_set('Europe/Moscow');
            setlocale(LC_ALL, 'ru_RU');

            $cardsList = [
                [
                    'userName' => 'Лариса',
                    'avatar'   => 'userpic-larisa-small.jpg',
                    'title'    => 'Цитата',
                    'content'  => 'Мы в жизни любим только раз, а после ищем лишь похожих',
                    'type'     => 'post-quote'
                ],
                [
                    'userName' => 'Владик',
                    'avatar'   => 'userpic.jpg',
                    'title'    => 'Игра престолов',
                    'content'  => 'Не могу дождаться начала финального сезона своего любимого сериала!',
                    'type'     => 'post-text'
                ],
                [
                    'userName' => 'Виктор',
                    'avatar'   => 'userpic-mark.jpg',
                    'title'    => 'Наконец, обработал фотки!',
                    'content'  => 'rock-medium.jpg',
                    'type'     => 'post-photo'
                ],
                [
                    'userName' => '	Лариса',
                    'avatar'   => 'userpic-larisa-small.jpg',
                    'title'    => 'Моя мечта',
                    'content'  => 'coast-medium.jpg',
                    'type'     => 'post-photo'
                ],
                [
                    'userName' => 'Владик',
                    'avatar'   => 'userpic.jpg',
                    'title'    => 'Лучшие курсы',
                    'content'  => 'www.htmlacademy.ru',
                    'type'     => 'post-link'
                ]
            ];

            function cutText ($text, $maxLength = 300)
            {
                if (strlen($text) > $maxLength) {
                    $textArr = explode(' ', $text);
                    $i = 0;
                    $j = 0;
                    foreach ($textArr as $word) {
                        $i += strlen($word);
                        $j++;
                        if ($i > $maxLength) {
                            //Блоку "Читать далее" добавил левый отступ 0, потому что не получилось выровнить по умолчанию.
                            echo implode(' ', array_slice($textArr, 0, $j)) . '...' . '<br/><a class="post-text__more-link" href="#" style="margin-left: 0">Читать далее</a>';
                            break;
                        }
                    }
                } else {
                    echo $text;
                }
            }

            foreach ($cardsList as $key => $item):

            $nowTime = time();
            $postTime = generate_random_date($item);
            $diffTime = $nowTime - strtotime($postTime);

            if ($diffTime < 3600)
            {
                $diffTime = ceil($diffTime / 60) . ' ' .get_noun_plural_form(ceil($diffTime / 60), 'минута', 'минуты', 'минут') . ' назад';
            }
            elseif ($diffTime > 3600 && $diffTime < 86400)
            {
                $diffTime = ceil($diffTime / 3600) . ' ' .get_noun_plural_form(ceil($diffTime / 3600), 'час', 'часа', 'часов') . ' назад';
            }
            elseif ($diffTime > 86400 && $diffTime < 604800)
            {
                $diffTime = ceil($diffTime / 86400) . ' ' .get_noun_plural_form(ceil($diffTime / 86400), 'день', 'дня', 'дней') . ' назад';
            }
            elseif ($diffTime > 604800 && $diffTime < 2629743)
            {
                $diffTime = ceil($diffTime / 604800) . ' ' .get_noun_plural_form(ceil($diffTime / 604800), 'неделя', 'недели', 'недель') . ' назад';
            }
            elseif ($diffTime > 2629743 && $diffTime < 31556926)
            {
                $diffTime = ceil($diffTime / 2629743) . ' ' .get_noun_plural_form(ceil($diffTime / 2629743), 'месяц', 'месяца', 'месяцев') . ' назад';
            }
            else
            {
                $diffTime = ceil($diffTime / 31556926) . ' ' .get_noun_plural_form(ceil($diffTime / 31556926), 'год', 'года', 'лет') . ' назад';
            }
            ?>

                <article class="popular__post post <?= $item['type'] ?>">
                    <header class="post__header">
                        <h2><?= htmlspecialchars($item['title']) ?><!--здесь заголовок--></h2>
                    </header>
                    <div class="post__main">
                        <!--здесь содержимое карточки-->
                        <?php if($item['type'] === 'post-quote'): ?>
                            <!--содержимое для поста-цитаты-->
                            <blockquote>
                                <p>
                                    <?= htmlspecialchars($item['content']) ?><!--здесь текст-->
                                </p>
                                <cite>Неизвестный Автор</cite>
                            </blockquote>
                        <?php elseif ($item['type'] === 'post-link'): ?>
                            <!--содержимое для поста-ссылки-->
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="http://" title="Перейти по ссылке">
                                    <div class="post-link__info-wrapper">
                                        <div class="post-link__icon-wrapper">
                                            <img src="img/logo-vita.jpg" alt="Иконка">
                                        </div>
                                        <div class="post-link__info">
                                            <h3><?= htmlspecialchars($item['title']) ?><!--здесь заголовок--></h3>
                                        </div>
                                    </div>
                                    <span><?= htmlspecialchars($item['content']) ?><!--здесь ссылка--></span>
                                </a>
                            </div>
                        <?php elseif ($item['type'] === 'post-photo'): ?>
                            <!--содержимое для поста-фото-->
                            <div class="post-photo__image-wrapper">
                                <img src="img/<?= htmlspecialchars($item['content']) ?>" alt="Фото от пользователя" width="360" height="240">
                            </div>
                        <?php elseif ($item['type'] === 'post-text'): ?>
                            <!--содержимое для поста-текста-->
                            <p><?= htmlspecialchars(cutText($item['content'])) ?><!--здесь текст--></p>
                        <?php endif ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link" href="#" title="Автор">
                                <div class="post__avatar-wrapper">
                                    <!--укажите путь к файлу аватара-->
                                    <img class="post__author-avatar" src="img/<?= htmlspecialchars($item['avatar']) ?>" alt="Аватар пользователя">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= htmlspecialchars($item['userName']) ?><!--здесь имя пользоателя--></b>
                                    <time class="post__time" datetime="<?= $postTime ?>"  title="<?= date( 'd.m.Y H:i',strtotime($postTime)) ?>"><?= $diffTime ?></time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                    <svg class="post__indicator-icon" width="20" height="17">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                    <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                        <use xlink:href="#icon-heart-active"></use>
                                    </svg>
                                    <span>0</span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span>0</span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endforeach ?>
        </div>
    </div>
</section>