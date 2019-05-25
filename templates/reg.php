<main class="page__main page__main--registration">
    <div class="container">
        <h1 class="page__title page__title--registration">Регистрация</h1>
    </div>
    <section class="registration container">
        <h2 class="visually-hidden">Форма регистрации</h2>
        <form class="registration__form form" action="" method="post" enctype="multipart/form-data">
            <div class="form__text-inputs-wrapper">
                <div class="form__text-inputs">
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-email">Электронная почта <span
                                    class="form__input-required">*</span></label>
                        <div class="form__input-section<?= isset($errors['email']) ? ' form__input-section--error' : '' ?>">
                            <input class="registration__input form__input" id="registration-email" type="email"
                                   name="email" placeholder="Укажите эл.почту"
                                   value="<?= htmlspecialchars($_POST['email']) ?>">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Ошибка почты</h3>
                                <p class="form__error-desc">Введите корректный адрес почты в формате example@mail.ru, а
                                    так же он должен быть уникальным</p>
                            </div>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-login">Логин <span
                                    class="form__input-required">*</span></label>
                        <div class="form__input-section<?= isset($errors['name']) ? ' form__input-section--error' : '' ?>">
                            <input class="registration__input form__input" id="registration-login" type="text"
                                   name="name" placeholder="Укажите логин"
                                   value="<?= htmlspecialchars($_POST['name']) ?>">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Ошибка логина</h3>
                                <p class="form__error-desc">Заполните это поле, лимит 70 символов.</p>
                            </div>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-password">Пароль<span
                                    class="form__input-required">*</span></label>
                        <div class="form__input-section<?= isset($errors['password']) ? ' form__input-section--error' : '' ?>">
                            <input class="registration__input form__input" id="registration-password" type="password"
                                   name="password" placeholder="Придумайте пароль"
                                   value="<?= htmlspecialchars($_POST['password']) ?>">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Ошибка пароля</h3>
                                <p class="form__error-desc">Используйте любое количество символов до 70</p>
                            </div>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-password-repeat">Повтор
                            пароля<span class="form__input-required">*</span></label>
                        <div class="form__input-section<?= isset($errors['password-repeat']) ? ' form__input-section--error' : '' ?>">
                            <input class="registration__input form__input" id="registration-password-repeat"
                                   type="password" name="password-repeat" placeholder="Повторите пароль"
                                   value="<?= htmlspecialchars($_POST['password-repeat']) ?>">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Повтор пароля</h3>
                                <p class="form__error-desc">Проверьте что бы пароли совпадали</p>
                            </div>
                        </div>
                    </div>
                    <div class="registration__textarea-wrapper form__textarea-wrapper">
                        <label class="registration__label form__label" for="text-info">Информация о себе</label>
                        <div class="form__input-section">
                            <textarea class="registration__textarea form__textarea form__input" id="text-info"
                                      placeholder="Коротко о себе в свободной форме"><?= htmlspecialchars($_POST['contact-info']) ?></textarea>
                        </div>
                    </div>
                </div>
                <?php if (isset($errors)): ?>
                    <div class="form__invalid-block">
                        <b class="form__invalid-slogan">Пожалуйста, исправьте ошибки в форме:</b>
                        <ul class="form__invalid-list">
                            <?php foreach ($errors as $key => $val): ?>
                                <li class="form__invalid-item"><?= $dict[$key] ?> <?= $val ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>
            </div>
            <div class="registration__input-file-container form__input-container form__input-container--file">
                <div class="registration__input-file-wrapper form__input-file-wrapper">
                    <div class="registration__file-zone form__file-zone dropzone">
                        <input class="registration__input-file form__input-file" id="userpic-file" type="file"
                               name="userpic-file" title=" ">
                        <div class="form__file-zone-text">
                            <span>Перетащите фото сюда</span>
                        </div>
                    </div>
                    <button class="registration__input-file-button form__input-file-button button" type="button">
                        <span>Выбрать фото</span>
                        <svg class="registration__attach-icon form__attach-icon" width="10" height="20">
                            <use xlink:href="#icon-attach"></use>
                        </svg>
                    </button>
                </div>
                <div class="registration__file form__file dropzone-previews">

                </div>
            </div>
            <button class="registration__submit button button--main" type="submit">Отправить</button>
        </form>
    </section>
</main>
