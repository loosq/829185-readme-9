<?php
/**
 * @var string $getTab тип контента
 * @var array $errors массив с ошибками форм валидации
 * @var array $dict массив "словарь" для массива $errors
 *
 * Фото-пост:
 *  @var string $photoHeading заголовок
 *  @var string $url ссылка на изображение
 *  @var string $photoTags теги
 *
 * Видео-пост:
 *  @var string $videoHeading заголовок
 *  @var string $videoUrl ссылка на видео
 *  @var string $videoTags тэги
 *
 * Текст-пост:
 *  @var string $textHeading заголовок
 *  @var string $textContent сам текст
 *  @var string $textTags тэги
 *
 * Цитата-пост:
 *  @var string $quoteHeading заголовок
 *  @var string $quoteText текст цитаты
 *  @var string $quoteAuthor автор цитаты
 *  @var string $quoteTags тэги
 *
 * Ссылка-пост:
 *  @var string $linkHeading заголовок
 *  @var string $linkUrl ссылка
 *  @var string $linkTags тэги
 *
 */
?>
<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link filters__button filters__button--photo tabs__item tabs__item--active button<?= ($getTab === 'photo') ? ' filters__button--active' : '' ?>" href="add.php?tab=photo">
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-photo"></use>
                                </svg>
                                <span>Фото</span>
                            </a>
                        </li>
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link filters__button filters__button--video tabs__item button<?= ($getTab === 'video') ? ' filters__button--active' : '' ?>" href="add.php?tab=video">
                                <svg class="filters__icon" width="24" height="16">
                                    <use xlink:href="#icon-filter-video"></use>
                                </svg>
                                <span>Видео</span>
                            </a>
                        </li>
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link filters__button filters__button--text tabs__item button<?= ($getTab === 'text') ? ' filters__button--active' : '' ?>" href="add.php?tab=text">
                                <svg class="filters__icon" width="20" height="21">
                                    <use xlink:href="#icon-filter-text"></use>
                                </svg>
                                <span>Текст</span>
                            </a>
                        </li>
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link filters__button filters__button--quote tabs__item button<?= ($getTab === 'quote') ? ' filters__button--active' : '' ?>" href="add.php?tab=quote">
                                <svg class="filters__icon" width="21" height="20">
                                    <use xlink:href="#icon-filter-quote"></use>
                                </svg>
                                <span>Цитата</span>
                            </a>
                        </li>
                        <li class="adding-post__tabs-item filters__item">
                            <a href="/add.php?tab=link" class="adding-post__tabs-link filters__button filters__button--link tabs__item button<?= ($getTab === 'link') ? ' filters__button--active' : '' ?>">
                                <svg class="filters__icon" width="21" height="18">
                                    <use xlink:href="#icon-filter-link"></use>
                                </svg>
                                <span>Ссылка</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="adding-post__tab-content">
                    <?php if ($getTab === 'photo'): ?>
                    <section class="adding-post__photo tabs__content <?= ($getTab === 'photo') ? 'tabs__content--active' : '' ?>">
                        <h2 class="visually-hidden">Форма добавления фото</h2>
                        <form class="adding-post__form form" action="?tab=photo" method="POST" enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="photo-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['photo-heading']) ? ' form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input" id="photo-heading" type="text" name="photo-heading" placeholder="Введите заголовок" value="<?= htmlspecialchars($photoHeading) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок</h3>
                                                <p class="form__error-desc">Это поле не может быть пустым</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper<?= isset($errors['file-or-url']) ? ' form__input-section--error' : '' ?><?= isset($errors['incorrect-url']) ? ' form__input-section--error' : '' ?>">
                                        <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="photo-url" type="text" name="photo-url" placeholder="Введите ссылку" value="<?= htmlspecialchars($url) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Ссылка</h3>
                                                <p class="form__error-desc">Укажите ссылку, а также проверьте формат</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="photo-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="photo-tags" type="text" name="photo-tags" placeholder="Введите теги" value="#<?= htmlspecialchars($photoTags) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($errors)): ?>
                                <div class="form__invalid-block">
                                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php foreach ($errors as $key => $val): ?>
                                        <li class="form__invalid-item"><?= $dict[$key] ?> <?= $val ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                </div>
                                <?php endif ?>
                            </div>
                            <div class="adding-post__input-file-container form__input-container form__input-container--file">
                                <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                                    <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
                                        <input class="adding-post__input-file form__input-file" id="photo-file-img" type="file" name="photo-file-img" title="">
                                        <div class="form__file-zone-text">
                                            <span>Перетащите фото сюда</span>
                                        </div>
                                    </div>
                                    <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button" type="button">
                                        <span>Выбрать фото</span>
                                        <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                                            <use xlink:href="#icon-attach"></use>
                                        </svg>
                                    </button>
                                </div>
                                <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">
                                </div>
                            </div>
                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="add.php">Закрыть</a>
                            </div>
                        </form>
                    </section>
                    <?php elseif ($getTab === 'video'): ?>
                    <section class="adding-post__video tabs__content <?= ($getTab === 'video') ? 'tabs__content--active' : '' ?>">
                        <h2 class="visually-hidden">Форма добавления видео</h2>
                        <form class="adding-post__form form" action="?tab=video" method="post" enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="video-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['video-heading']) ? ' form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input" id="video-heading" type="text" name="video-heading" placeholder="Введите заголовок" value="<?=  htmlspecialchars($videoHeading) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок</h3>
                                                <p class="form__error-desc">Это поле не может быть пустым</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['video-url']) ? ' form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input" id="video-url" type="text" name="video-url" placeholder="Введите ссылку" value="<?=  htmlspecialchars($videoUrl) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Ссылка</h3>
                                                <p class="form__error-desc">Укажите ссылку на виде находящееся на <a style="color: deepskyblue" href="http://youtube.com">Youtube</a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="video-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="video-tags" type="text" name="video-tags" placeholder="Введите ссылку" value="#<?=  htmlspecialchars($videoTags) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($errors)): ?>
                                <div class="form__invalid-block">
                                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php foreach ($errors as $key => $val): ?>
                                            <li class="form__invalid-item"><?= $dict[$key] ?> <?= $val ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                </div>
                                <?php endif ?>
                            </div>

                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="add.php">Закрыть</a>
                            </div>
                        </form>
                    </section>
                    <?php elseif ($getTab === 'text'): ?>
                    <section class="adding-post__text tabs__content <?= ($getTab === 'text') ? 'tabs__content--active' : '' ?>">
                        <h2 class="visually-hidden">Форма добавления текста</h2>
                        <form class="adding-post__form form" action="?tab=text" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="text-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['text-heading']) ? ' form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input" id="text-heading" type="text" name="text-heading" placeholder="Введите заголовок" value="<?= htmlspecialchars($textHeading) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__textarea-wrapper form__textarea-wrapper">
                                        <label class="adding-post__label form__label" for="post-text">Текст поста <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['text-content']) ? ' form__input-section--error' : '' ?>">
                                            <textarea class="adding-post__textarea form__textarea form__input" id="post-text" name="text-content" placeholder="Введите текст публикации"><?= htmlspecialchars($textContent) ?></textarea>
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="post-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="post-tags" type="text" name="text-tags" placeholder="Введите теги" value="#<?= htmlspecialchars($textTags) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($errors)): ?>
                                    <div class="form__invalid-block">
                                        <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                        <ul class="form__invalid-list">
                                            <?php foreach ($errors as $key => $val): ?>
                                                <li class="form__invalid-item"><?= $dict[$key] ?> <?= $val ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                <?php endif ?>
                            </div>
                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="add.php">Закрыть</a>
                            </div>
                        </form>
                    </section>
                    <?php elseif ($getTab === 'quote'): ?>
                    <section class="adding-post__quote tabs__content <?= ($getTab === 'quote') ? 'tabs__content--active' : '' ?>">
                        <h2 class="visually-hidden">Форма добавления цитаты</h2>
                        <form class="adding-post__form form" action="?tab=quote" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['quote-heading']) ? ' form__input-section--error' : '' ?>">
                                        <label class="adding-post__label form__label" for="quote-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="quote-heading" type="text" name="quote-heading" placeholder="Введите заголовок" value="<?= htmlspecialchars($quoteHeading) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__textarea-wrapper">
                                        <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['quote-heading']) ? ' form__input-section--error' : '' ?>">
                                            <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="cite-text" placeholder="Текст цитаты" name="quote-text"><?= htmlspecialchars($quoteText) ?></textarea>
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__textarea-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['quote-author']) ? ' form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author" value="<?= htmlspecialchars($quoteAuthor) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="cite-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="cite-tags" type="text" name="quote-tags" placeholder="Введите теги" value="#<?= htmlspecialchars($quoteTags) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($errors)): ?>
                                    <div class="form__invalid-block">
                                        <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                        <ul class="form__invalid-list">
                                            <?php foreach ($errors as $key => $val): ?>
                                                <li class="form__invalid-item"><?= $dict[$key] ?> <?= $val ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                <?php endif ?>
                            </div>
                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="add.php">Закрыть</a>
                            </div>
                        </form>
                    </section>
                    <?php elseif ($getTab === 'link'): ?>
                    <section class="adding-post__link tabs__content<?= ($getTab === 'link') ? ' tabs__content--active' : '' ?>">
                        <h2 class="visually-hidden">Форма добавления ссылки</h2>
                        <form class="adding-post__form form" action="?tab=link" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="link-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['link-heading']) ? ' form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input" id="link-heading" type="text" name="link-heading" placeholder="Введите заголовок" value="<?= htmlspecialchars($linkHeading) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок</h3>
                                                <p class="form__error-desc">Это поле необходимо заполнить</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__textarea-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="post-link">Ссылка <span class="form__input-required">*</span></label>
                                        <div class="form__input-section<?= isset($errors['link-url']) ? ' form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input" id="post-link" type="text" name="link-url" value="<?= htmlspecialchars($linkUrl) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Ссылка</h3>
                                                <p class="form__error-desc">Это поле необходимо заполнить</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="link-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="link-tags" type="text" name="link-tags" placeholder="Введите ссылку" value="#<?= htmlspecialchars($linkTags) ?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообя</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($errors)): ?>
                                    <div class="form__invalid-block">
                                        <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                        <ul class="form__invalid-list">
                                            <?php foreach ($errors as $key => $val): ?>
                                                <li class="form__invalid-item"><?= $dict[$key] ?> <?= $val ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                <?php endif ?>
                            </div>
                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="../add.php">Закрыть</a>
                            </div>
                        </form>
                    </section>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</main>
