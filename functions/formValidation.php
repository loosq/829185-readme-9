<?php

/**
 * Проверяет форму входа на сайт.
 * @param mysqli $con ресурс соединения
 * @param string $email адресс электронной почты
 * @param string $password пароль
 *
 * @return string $html html контент
 */
function loginFormValidation($con, $email, $password)
{
    $title = 'readme: блог, каким он должен быть';
    $userName = $_SESSION['user-name'];
    $userEmail = $email;
    $userPassword = $password;

    if (isset($userName)) {
        redirect('feed.php?block=feed&tab=all');
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $errors = [];

            if (empty($email)) {
                $errors['email'] = 'Это поле необходимо заполнить';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Введите почту в правильном формате';
            } elseif (!dbIsEmailValid($con, $email)) {
                $errors['email'] = 'Пользователь с такой электронной почтой не зарегистрирован';
            } else {
                $hashToEmail = dbGetHashToEmail($con, $email);
            }

            if (empty($password)) {
                $errors['password'] = 'Это поле необходимо заполнить';
            } elseif (!password_verify($password, $hashToEmail)) {
                $errors['password'] = 'Пароль указан неверно';
            }

            $html = include_template('index.php', [
                'userPassword' => $userPassword,
                'userEmail'    => $userEmail,
                'errors'       => $errors,
            ]);

            if (!count($errors)) {
                session_start();
                dbUserSessionByEmail($con, $email);
                header('Location:/feed.php?block=feed&tab=all');
            }

        } else {
            $html = include_template('index.php', [
                'title' => $title,
            ]);
        }
    }

    return $html;
}

/**
 * Проверяет форму для регистрации нового пользователя.
 * @param mysqli $con ресурс соединения
 *
 * @return string $regForm при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на главную страницу;
 */
function regFormValidation($con)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['name', 'password', 'password-repeat', 'email'];
        $dict = [
            'name'            => 'Логин.',
            'password'        => 'Пароль.',
            'password-repeat' => 'Повтор пароля.',
            'email'           => 'Электронная почта.',
        ];

        $userPicFile = $_FILES['userpic-file']['name'];
        $userPicFilePath = $_FILES['userpic-file']['tmp_name'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($_POST['email'])) {
            $errors['email'] = 'Это поле необходимо заполнить';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите электронную почту в правильном формате';
        } elseif (strlen($_POST['email']) > 70) {
            $errors['email'] = 'Это поле не может быть длиннее 70 символов';
        } elseif (dbIsEmailValid($con, $_POST['email'])) {
            $errors['email'] = 'Этот адрес уже занят';
        }

        if (empty($_POST['name'])) {
            $errors['name'] = 'Это поле необходимо заполнить';
        } elseif (strlen($_POST['name']) > 70) {
            $errors['name'] = 'Это поле не может быть длиннее 70 символов';
        }

        if (empty($_POST['password'])) {
            $errors['password'] = 'Это поле необходимо заполнить';
        } elseif (strlen($_POST['password']) > 70) {
            $errors['password'] = 'Это поле не может быть длиннее 70 символов';
        }

        if (empty($_POST['password-repeat'])) {
            $errors['password-repeat'] = 'Это поле необходимо заполнить';
        } elseif (strlen($_POST['password-repeat']) > 70) {
            $errors['password-repeat'] = 'Это поле не может быть длиннее 70 символов';
        }

        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if (!password_verify($_POST['password-repeat'], $passwordHash)) {
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


        if (count($errors)) {
            $regForm = include_template('reg.php', [
                'errors' => $errors,
                'dict'   => $dict,
            ]);
        } else {
            dbNewUser($con, $_POST['email'], $_POST['name'], $passwordHash, $newUserPicPath,
                $_POST['contact-info']);
        }

    } else {
        $regForm = include_template('reg.php');
    }

    return $regForm;
}

/**
 * Проверяет на уникальность электронную почту при регистрации.
 * @param mysqli $con ресурс соединения
 * @param string $newUserEmail почта новго пользователя
 *
 * @return bool $result true если новая почта уникальная, false если есть совпадения;
 */
function dbIsEmailValid($con, $newUserEmail)
{
    $result = false;
    $sql = 'SELECT email FROM users WHERE email = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $newUserEmail);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res) {
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
        if ($rows) {
            $result = true;
        }
    }

    return $result;
}

/**
 * Возвращает хеш пароля по введённому адресу почты.
 * @param mysqli $con ресурс соединения
 * @param string $email эмейл пользователя
 *
 * @return bool $row хеш пароля;
 */
function dbGetHashToEmail($con, $email)
{
    $row = false;
    $sql = 'SELECT password FROM users WHERE email = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res) {
        $row = mysqli_fetch_row($res);
    }

    return $row[0];
}

/**
 * Проверяет форму для отправки поста типа фото и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string $photoForm при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function photoFormValidation($con)
{
    $getTab = $_GET['tab'];
    $userSession = $_SESSION;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['photo-heading'];
        $dict = ['photo-heading' => 'Заголовок.'];
        $photoHeading = $_POST['photo-heading'];
        $photoTags = $_POST['photo-tags'];
        $picture = $_FILES['photo-file-img']['name'];
        $url = $_POST['photo-url'];
        $tmpPath = $_FILES['photo-file-img']['tmp_name'];
        $tmpName = $_FILES['photo-file-img']['name'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (!$url && !$picture) {
            $errors['file-or-url'] = 'Укажите ссылку или загрузите изображение';
        } elseif (isset($picture) && !$url) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $tmpPath);

            if (filesize($tmpPath) > 10485760) {
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
        } elseif (isset($picture) && isset($url)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $tmpPath);

            if (filesize($tmpPath) > 10485760) {
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
                'userSession'   => $userSession,
                'photoTags'     => $photoTags,
                'photoHeading'  => $photoHeading,
                'url'           => $url,
                'getTab'        => $getTab,
                'errors'        => $errors,
                'dict'          => $dict,
            ]);
        } else {
            $photoForm = dbNewPost($con, $photoHeading, $photoContent, '', '', $url, '',
                $_SESSION['user-id'], $photoTags, 3);
        }
    } else {
        $photoForm = include_template('add.php', [
            'userSession'   => $userSession,
            'getTab' => $getTab,
        ]);
    }

    return $photoForm;
}

/**
 * Проверяет форму для отправки поста типа видео и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string $videoForm при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function videoFormValidation($con)
{
    $getTab = $_GET['tab'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $videoHeading = $_POST['video-heading'];
        $videoUrl = $_POST['video-url'];
        $videoTags = $_POST['video-tags'];
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
        }

        if (!$videoUrl) {
            $errors['video-url'] = 'Это поле необходимо заполнить';
        } elseif (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            $errors['video-url'] = 'Укажите верный формат ссылки';
        } elseif (!check_youtube_url($videoUrl)) {
            $errors['video-url'] = 'Укажите ссылку на http://youtube.com';
        }

        $videoUrl = embed_youtube_video($videoUrl);

        if (count($errors)) {
            $videoForm = include_template('add.php', [
                'videoHeading'  => $videoHeading,
                'videoUrl'      => $videoUrl,
                'videoTags'     => $videoTags,
                'getTab'        => $getTab,
                'errors'        => $errors,
                'dict'          => $dict,
            ]);
        } else {
            $videoForm = dbNewPost($con, $videoHeading, '', '', '', '', $videoUrl,
                $_SESSION['user-id'],
                $videoTags, 4);
        }
    } else {
        $videoForm = include_template('add.php', [
            'getTab' => $getTab,
        ]);
    }

    return $videoForm;
}

/**
 * Проверяет форму для отправки поста типа текст и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string $textForm при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function textFormValidation($con)
{
    $getTab = $_GET['tab'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['text-heading', 'text-content'];
        $dict = ['text-heading' => 'Заголовок.', 'text-content' => 'Текст поста.'];
        $textHeading = $_POST['text-heading'];
        $textContent = $_POST['text-content'];
        $textTags = $_POST['text-tags'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($textHeading)) {
            $errors['text-heading'] = 'Это поле необходимо заполнить';
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
            'getTab' => $getTab,
        ]);
    }

    return $textForm;
}

/**
 * Проверяет форму для отправки поста типа цитата и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string $quoteForm при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function quoteFormValidation($con)
{
    $getTab = $_GET['tab'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quoteHeading = $_POST['quote-heading'];
        $quoteText = $_POST['quote-text'];
        $quoteAuthor = $_POST['quote-author'];
        $quoteTags = $_POST['quote-tags'];
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
            'getTab' => $getTab,
        ]);
    }

    return $quoteForm;
}

/**
 * Проверяет форму для отправки поста типа ссылка и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string $quoteForm при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function urlFormValidation($con)
{
    $getTab = $_GET['tab'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $linkHeading = $_POST['link-heading'];
        $linkUrl = $_POST['link-url'];
        $linkTags = $_POST['link-tags'];
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
            'getTab' => $getTab,
        ]);
    }

    return $linkForm;
}

/**
 * Проверяет формы в соответствии с выбранным типом поста.
 * @param mysqli $con ресурс соединения
 * @param string $tab текущий тип контента
 *
 * @return string $content форма;
 */
function validForm($con, $tab)
{
    $getTab = $_GET['tab'];
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
            'getTab' => $getTab,
        ]);
    }

    return $content;
}


/**
 * Проверяет форму отправки сообщения.
 * @param mysqli $con ресурс соединения
 * @param string $text  сообщение
 * @param int $userSend отправитель
 * @param int $userGet получатель
 *
 * @return bool $res true при успешной отправке
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
