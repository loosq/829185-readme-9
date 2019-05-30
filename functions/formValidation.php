<?php

/**
 * Проверяет форму входа на сайт.
 * @param mysqli $con ресурс соединения
 * @param string $email адресс электронной почты
 * @param string $password пароль
 *
 * @return string html контент
 */
function loginFormValidation($con, $email, $password)
{
    $title = 'readme: блог, каким он должен быть';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];
        if (empty($email)) {
            $errors['email'] = 'Это поле необходимо заполнить';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите почту в правильном формате';
        } elseif (!dbIsEmailValid($con, $email)) {
            $errors['email'] = 'Пользователь с такой электронной почтой не зарегистрирован';
        }
        $hashToEmail = dbGetHashToEmail($con, $email);
        if (empty($password)) {
            $errors['password'] = 'Это поле необходимо заполнить';
        } elseif (!password_verify($password, $hashToEmail)) {
            $errors['password'] = 'Пароль указан неверно';
        }

        if (!count($errors)) {
            $rows = dbUserSessionByEmail($con, $email);
            startNewSession($con, $rows);
            header('Location:/feed.php?block=feed&tab=all');
        } else {
            $html = include_template('index.php', [
                'errors'   => $errors,
                'password' => $password,
                'email'    => $email,
                'title'    => $title,
            ]);
        }
    } else {
        $html = include_template('index.php', [
            'password' => $password,
            'email'    => $email,
            'title'    => $title,
        ]);
    }

    return $html;
}

/**
 * Проверяет форму для регистрации нового пользователя.
 * @param mysqli $con ресурс соединения
 * @param string $userName имя пользователя
 * @param string $pwd пароль пользователя
 * @param string $copyPwd повтор пароля
 * @param string $email почта пользователя
 * @param string $userPicFilePath путь к картинке аватара пользователя
 * @param string $userPicFile файл картинка пользователя
 * @param string $contactInfo контактная информация о пользователе
 *
 * @return string при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на главную страницу;
 */
function regFormValidation(
    $con,
    $contactInfo,
    $copyPwd,
    $pwd,
    $userName,
    $email,
    $userPicFilePath = null,
    $userPicFile = null
) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['userName', 'password', 'password-repeat', 'email'];
        $dict = [
            'userName'        => 'Логин.',
            'password'        => 'Пароль.',
            'password-repeat' => 'Повтор пароля.',
            'email'           => 'Электронная почта.',
            'user-pic-size'   => 'Размер изображения.',
            'user-pic-format' => 'Формат изображения.',

        ];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($email)) {
            $errors['email'] = 'Это поле необходимо заполнить';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите электронную почту в правильном формате';
        } elseif (strlen($email) > 70) {
            $errors['email'] = 'Это поле не может быть длиннее 70 символов';
        } elseif (dbIsEmailValid($con, $email)) {
            $errors['email'] = 'Этот адрес уже занят';
        }

        if (empty($userName)) {
            $errors['userName'] = 'Это поле необходимо заполнить';
        } elseif (strlen($userName) > 70) {
            $errors['userName'] = 'Это поле не может быть длиннее 70 символов';
        }

        if (empty($pwd)) {
            $errors['password'] = 'Это поле необходимо заполнить';
        } elseif (strlen($pwd) > 70) {
            $errors['password'] = 'Это поле не может быть длиннее 70 символов';
        }

        if (empty($copyPwd)) {
            $errors['password-repeat'] = 'Это поле необходимо заполнить';
        } elseif (strlen($copyPwd) > 70) {
            $errors['password-repeat'] = 'Это поле не может быть длиннее 70 символов';
        }

        $passwordHash = password_hash($pwd, PASSWORD_DEFAULT);

        if (!password_verify($copyPwd, $passwordHash)) {
            $errors['password-repeat'] = 'Пароли отличаются';
        }

        if ($userPicFile) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $userPicFilePath);

            if (filesize($userPicFilePath) > 10485760) {
                $errors['user-pic-size'] = 'Максимально допустимый размер изображения 10 мегабайт';
            } elseif ($fileType === 'image/gif' || $fileType === 'image/png' || $fileType === 'image/jpeg') {
                $fileInfo = pathinfo($userPicFile, PATHINFO_EXTENSION);
                $newNameFile = uniqid() . '.' . $fileInfo;
                move_uploaded_file($userPicFilePath, 'uploads/' . $newNameFile);
                $newUserPicPath = '../uploads/' . $newNameFile;
            } else {
                $errors['user-pic-format'] = 'Загрузите картинку в формате на выбор: .jpg, .png, .gif';
            }
        } else {
            $newUserPicPath = '';
        }

        $contactInfo = trim(htmlspecialchars($contactInfo)) ?? '';
        if (count($errors)) {
            $content = include_template('reg.php', [
                'errors'          => $errors,
                'dict'            => $dict,
                'userPicFile'     => $userPicFile,
                'userPicFilePath' => $userPicFilePath,
                'email'           => $email,
                'userName'        => $userName,
                'pwd'             => $pwd,
                'copyPwd'         => $copyPwd,
                'contactInfo'     => $contactInfo,
            ]);
        } else {
            dbNewUser($con, $email, $userName, $passwordHash, $newUserPicPath,
                $contactInfo);
            redirectHome();
        }
    } else {
        $content = include_template('reg.php', [
            'userPicFile'     => $userPicFile,
            'userPicFilePath' => $userPicFilePath,
            'email'           => $email,
            'userName'        => $userName,
            'pwd'             => $pwd,
            'copyPwd'         => $copyPwd,
            'contactInfo'     => $contactInfo,

        ]);
    }

    return $content;
}

/**
 * Проверяет форму для отправки поста типа фото и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function photoFormValidation($con)
{
    $getTab = $_GET['tab'];
    $userSession = $_SESSION;
    $required = ['photo-heading'];
    $dict = ['photo-heading' => 'Заголовок.', 'file-or-url' => 'Файл или ссылка.', 'file-size' => 'Размер картинки.'];
    $photoHeading = $_POST['photo-heading'] ?? '';
    $photoTags = $_POST['photo-tags'] ?? '';
    $picture = $_FILES['photo-file-img']['name'] ?? '';
    $url = $_POST['photo-url'] ?? '';
    $tmpPath = $_FILES['photo-file-img']['tmp_name'] ?? '';
    $tmpName = $_FILES['photo-file-img']['name'] ?? '';
    $fileSize = $_FILES['photo-file-img']['size'] ?? '';


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (strlen($photoHeading) > 10240) {
            $errors['photo-heading'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (!$url && !$picture) {
            $errors['file-or-url'] = 'Укажите ссылку или загрузите изображение';
        } elseif (isset($picture) && !$url) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $tmpPath);

            if ($fileSize > 10485760) {
                $errors['file-size'] = 'Максимально допустимый размер изображения 10 мегабайт';
            } elseif ($fileType === 'image/gif' || $fileType === 'image/png' || $fileType === 'image/jpeg') {
                $fileInfo = pathinfo($tmpName, PATHINFO_EXTENSION);
                $newNameFile = uniqid() . '.' . $fileInfo;
                move_uploaded_file($tmpPath, 'uploads/' . $newNameFile);
                $photoContent = '../uploads/' . $newNameFile;
            } else {
                $errors['file'] = 'Загрузите картинку в формате на выбор: .jpg, .png, .gif';
            }
        } elseif (isset($url) && !$picture) {
            if (!filter_var($_POST['photo-url'], FILTER_VALIDATE_URL)) {
                $errors['incorrect-url'] = 'Укажите ссылку в формате http://example.com ';
            } else {
                $photoContent = $_POST['photo-url'];
            }
        } elseif ($picture && $url) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $tmpPath);

            if ($fileSize > 10485760) {
                $errors['file-size'] = 'Максимально допустимый размер изображения 10 мегабайт';
            } elseif ($fileType === 'image/gif' || $fileType === 'image/png' || $fileType === 'image/jpeg') {
                $fileInfo = pathinfo($tmpName, PATHINFO_EXTENSION);
                $newNameFile = uniqid() . '.' . $fileInfo;
                move_uploaded_file($tmpPath, 'uploads/' . $newNameFile);
                $photoContent = '../uploads/' . $newNameFile;
            } else {
                $errors['file'] = 'Загрузите картинку в формате на выбор: .jpg, .png, .gif';
            }
        }

        if (count($errors)) {
            $photoForm = include_template('add.php', [
                'userSession'  => $userSession,
                'photoTags'    => $photoTags,
                'photoHeading' => $photoHeading,
                'url'          => $url,
                'getTab'       => $getTab,
                'errors'       => $errors,
                'dict'         => $dict,
            ]);
        } else {
            $photoForm = dbNewPost($con, $photoHeading, $photoContent, '', '', $url, '',
                $_SESSION['user-id'], $photoTags, 3);
        }
    } else {
        $photoForm = include_template('add.php', [
            'userSession'  => $userSession,
            'getTab'       => $getTab,
            'photoTags'    => $photoTags,
            'photoHeading' => $photoHeading,
            'url'          => $url,
        ]);
    }

    return $photoForm;
}

/**
 * Проверяет форму для отправки поста типа видео и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function videoFormValidation($con)
{
    $getTab = $_GET['tab'] ?? '';
    $videoHeading = $_POST['video-heading'] ?? '';
    $videoUrl = $_POST['video-url'] ?? '';
    $videoTags = $_POST['video-tags'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['video-heading', 'video-url'];
        $dict = ['video-heading' => 'Заголовок.', 'video-url' => 'Ссылка.'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (!$videoHeading) {
            $errors['video-heading'] = 'Это поле необходимо заполнить';
        } elseif (strlen($videoHeading) > 70) {
            $errors['video-heading'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (!$videoUrl) {
            $errors['video-url'] = 'Это поле необходимо заполнить';
        } elseif (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            $errors['video-url'] = 'Укажите верный формат ссылки';
        } elseif (!check_youtube_url($videoUrl)) {
            $errors['video-url'] = 'Укажите ссылку на http://youtube.com';
        }

        if (count($errors)) {
            $videoForm = include_template('add.php', [
                'videoHeading' => $videoHeading,
                'videoUrl'     => $videoUrl,
                'videoTags'    => $videoTags,
                'getTab'       => $getTab,
                'errors'       => $errors,
                'dict'         => $dict,
            ]);
        } else {
            $videoForm = dbNewPost($con, $videoHeading, '', '', '', '', $videoUrl,
                $_SESSION['user-id'],
                $videoTags, 4);
        }
    } else {
        $videoForm = include_template('add.php', [
            'getTab'       => $getTab,
            'videoHeading' => $videoHeading,
            'videoUrl'     => $videoUrl,
            'videoTags'    => $videoTags,
        ]);
    }

    return $videoForm;
}

/**
 * Проверяет форму для отправки поста типа текст и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function textFormValidation($con)
{
    $getTab = $_GET['tab'];
    $textHeading = $_POST['text-heading'] ?? '';
    $textContent = $_POST['text-content'] ?? '';
    $textTags = $_POST['text-tags'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['text-heading', 'text-content'];
        $dict = ['text-heading' => 'Заголовок.', 'text-content' => 'Текст поста.'];
        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($textHeading)) {
            $errors['text-heading'] = 'Это поле необходимо заполнить';
        } elseif (strlen($textHeading) > 70) {
            $errors['text-heading'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (empty($textContent)) {
            $errors['text-content'] = 'Это поле необходимо заполнить';
        } elseif (strlen($textContent) > 70) {
            $errors['text-content'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (count($errors)) {
            $textForm = include_template('add.php', [
                'textHeading' => $textHeading,
                'textContent' => $textContent,
                'textTags'    => $textTags,
                'getTab'      => $getTab,
                'errors'      => $errors,
                'dict'        => $dict,
            ]);
        } else {
            $textForm = dbNewPost($con, $textHeading, $textContent, '', '', '', '',
                $_SESSION['user-id'],
                $textTags, 1);
        }
    } else {
        $textForm = include_template('add.php', [
            'getTab'      => $getTab,
            'textHeading' => $textHeading,
            'textContent' => $textContent,
            'textTags'    => $textTags,
        ]);
    }

    return $textForm;
}

/**
 * Проверяет форму для отправки поста типа цитата и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function quoteFormValidation($con)
{
    $getTab = $_GET['tab'];
    $quoteHeading = $_POST['quote-heading'] ?? '';
    $quoteText = $_POST['quote-text'] ?? '';
    $quoteAuthor = $_POST['quote-author'] ?? '';
    $quoteTags = $_POST['quote-tags'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['quote-heading', 'quote-text', 'quote-author'];
        $dict = [
            'quote-heading' => 'Заголовок.',
            'quote-text'    => 'Текст поста.',
            'quote-author'  => 'Автор цитаты.',
        ];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($quoteHeading)) {
            $errors['quote-heading'] = 'Это поле необходимо заполнить';
        } elseif (strlen($quoteHeading) > 70) {
            $errors['quote-heading'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (empty($quoteText)) {
            $errors['quote-text'] = 'Это поле необходимо заполнить';
        } elseif (strlen($quoteText) > 70) {
            $errors['quote-text'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (empty($quoteAuthor)) {
            $errors['quote-author'] = 'Это поле необходимо заполнить';
        } elseif (strlen($quoteAuthor) > 70) {
            $errors['quote-author'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (count($errors)) {
            $quoteForm = include_template('add.php', [
                'getTab'       => $getTab,
                'quoteHeading' => $quoteHeading,
                'quoteText'    => $quoteText,
                'quoteAuthor'  => $quoteAuthor,
                'quoteTags'    => $quoteTags,
                'errors'       => $errors,
                'dict'         => $dict,
            ]);
        } else {
            $quoteForm = dbNewPost($con, $quoteHeading, $quoteText, '',
                $quoteAuthor, '', '', $_SESSION['user-id'], $quoteTags, 2);
        }
    } else {
        $quoteForm = include_template('add.php', [
            'getTab'       => $getTab,
            'quoteHeading' => $quoteHeading,
            'quoteText'    => $quoteText,
            'quoteAuthor'  => $quoteAuthor,
            'quoteTags'    => $quoteTags,
        ]);
    }

    return $quoteForm;
}

/**
 * Проверяет форму для отправки поста типа ссылка и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function urlFormValidation($con)
{
    $getTab = $_GET['tab'];
    $linkHeading = $_POST['link-heading'] ?? '';
    $linkUrl = $_POST['link-url'] ?? '';
    $linkTags = $_POST['link-tags'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $required = ['link-heading', 'link-url'];
        $dict = ['link-heading' => 'Заголовок.', 'link-url' => 'Ссылка.'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($linkHeading)) {
            $errors['link-heading'] = 'Это поле необходимо заполнить';
        } elseif (strlen($linkHeading) > 70) {
            $errors['link-heading'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (empty($linkUrl)) {
            $errors['link-url'] = 'Это поле необходимо заполнить';
        } elseif (!filter_var($linkUrl, FILTER_VALIDATE_URL)) {
            $errors['link-url'] = 'Укажите ссылку в правильном формате';
        }

        if (count($errors)) {
            $linkForm = include_template('add.php', [
                'getTab'      => $getTab,
                'linkHeading' => $linkHeading,
                'linkUrl'     => $linkUrl,
                'linkTags'    => $linkTags,
                'errors'      => $errors,
                'dict'        => $dict,
            ]);
        } else {
            $linkForm = dbNewPost($con, $linkHeading, '', $linkUrl, '', '', '',
                $_SESSION['user-id'],
                $linkTags, 5);
        }
    } else {
        $linkForm = include_template('add.php', [
            'getTab'      => $getTab,
            'linkHeading' => $linkHeading,
            'linkUrl'     => $linkUrl,
            'linkTags'    => $linkTags,
        ]);
    }

    return $linkForm;
}

/**
 * Проверяет формы в соответствии с выбранным типом поста.
 * @param mysqli $con ресурс соединения
 * @param string $tab текущий тип контента
 *
 * @return string форма;
 */
function validForm($con, $tab)
{
    $getTab = $_GET['tab'] ?? '';
    $userSession = $_SESSION;
    if ($tab === 'photo') {
        $content = photoFormValidation($con);
    } elseif ($tab === 'video') {
        $content = videoFormValidation($con);
    } elseif ($tab === 'text') {
        $content = textFormValidation($con);
    } elseif ($tab === 'quote') {
        $content = quoteFormValidation($con);
    } elseif ($tab === 'link') {
        $content = urlFormValidation($con);
    } else {
        $content = include_template('add.php', [
            'userSession' => $userSession,
            'getTab'      => $getTab,
        ]);
    }

    return $content;
}

/**
 * Проверяет форму отправки сообщения.
 * @param mysqli $con ресурс соединения
 * @param string $text сообщение
 * @param int $userSend отправитель
 * @param int $userGet получатель
 *
 * @return bool true при успешной отправке
 */
function msgFormValidation($con, $userSend, $userGet, $text)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($text !== '') && $userGet) {
        $msgLong = htmlspecialchars($text);
        $msg = substr($msgLong, 0, 220);
        $res = dbNewMsg($con, $userSend, $userGet, $msg);
        redirectBack();
    } else {
        $res = false;
    }

    return $res;
}
