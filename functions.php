<?php

/** Функции проверки форм */

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
            'email'           => 'Электронная почта.'
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
        }elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errors['email'] = 'Введите электронную почту в правильном формате';
        } elseif (strlen($_POST['email']) > 70) {
            $errors['email'] = 'Это поле не может быть длиннее 70 символов';
        } elseif (db_is_email_valid($con, $_POST['email'])) {
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
            db_new_user($con, $_POST['email'], $_POST['name'], $passwordHash, $newUserPicPath,
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
function db_is_email_valid($con, $newUserEmail)
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
 * Проверяет форму для отправки поста типа фото и перенаправляет на только что созданный пост.
 * @param mysqli $con ресурс соединения
 *
 * @return string $photoForm при ошибках валидации возвращает форму с ошибками, при успехе делает
 * соответствующую запись в бд и перенаправляет пользователя на этот пост;
 */
function photoFormValidation($con)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['photo-heading'];
        $dict = ['photo-heading' => 'Заголовок.'];
        $picture = $_FILES['photo-file-img']['name'];
        $url = $_POST['photo-url'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (!$url && !$picture) {
            $errors['file-or-url'] = 'Укажите ссылку или загрузите изображение';
        } elseif (isset($picture) && !$url) {
            $tmpPath = $_FILES['photo-file-img']['tmp_name'];
            $tmpName = $_FILES['photo-file-img']['name'];

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
            $tmpPath = $_FILES['photo-file-img']['tmp_name'];
            $tmpName = $_FILES['photo-file-img']['name'];

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
                'errors' => $errors,
                'dict'   => $dict,
            ]);
        } else {
            $photoForm = db_new_post($con, $_POST['photo-heading'], $photoContent, '', '', $_POST['photo-url'], '',
                3, $_POST['photo-tags'], 3);
        }
    } else {
        $photoForm = include_template('add.php');
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['video-heading', 'video-url'];
        $dict = ['video-heading' => 'Заголовок.', 'video-url' => 'Ссылка.'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (!$_POST['video-heading']) {
            $errors['video-heading'] = 'Это поле необходимо заполнить';
        }

        if (!$_POST['video-url']) {
            $errors['video-url'] = 'Это поле необходимо заполнить';
        } elseif (!filter_var($_POST['video-url'], FILTER_VALIDATE_URL)) {
            $errors['video-url'] = 'Укажите верный формат ссылки';
        } elseif (!check_youtube_url($_POST['video-url'])) {
            $errors['video-url'] = 'Укажите ссылку на http://youtube.com';
        }


        if (count($errors)) {
            $videoForm = include_template('add.php', [
                'errors' => $errors,
                'dict'   => $dict,
            ]);
        } else {
            $videoForm = db_new_post($con, $_POST['video-heading'], '', '', '', '', $_POST['video-url'], 3,
                $_POST['video-tags'], 4);
        }
    } else {
        $videoForm = include_template('add.php');
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['text-heading', 'text-content'];
        $dict = ['text-heading' => 'Заголовок.', 'text-content' => 'Текст поста.'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($_POST['text-heading'])) {
            $errors['text-heading'] = 'Это поле необходимо заполнить';
        }

        if (empty($_POST['text-content'])) {
            $errors['text-content'] = 'Это поле необходимо заполнить';
        } elseif (strlen($_POST['text-content']) > 70) {
            $errors['text-content'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (count($errors)) {
            $textForm = include_template('add.php', [
                'errors' => $errors,
                'dict'   => $dict,
            ]);
        } else {
            $textForm = db_new_post($con, $_POST['text-heading'], $_POST['text-content'], '', '', '', '', 3,
                $_POST['text-tags'], 1);
        }
    } else {
        $textForm = include_template('add.php');
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $required = ['quote-heading', 'quote-text', 'quote-author'];
        $dict = [
            'quote-heading' => 'Заголовок.',
            'quote-text'    => 'Текст поста.',
            'quote-author'  => 'Автор цитаты.'
        ];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($_POST['quote-heading'])) {
            $errors['quote-heading'] = 'Это поле необходимо заполнить';
        }

        if (empty($_POST['quote-text'])) {
            $errors['quote-text'] = 'Это поле необходимо заполнить';
        } elseif (strlen($_POST['quote-text']) > 70) {
            $errors['quote-text'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (empty($_POST['quote-author'])) {
            $errors['quote-author'] = 'Это поле необходимо заполнить';
        } elseif (strlen($_POST['quote-author']) > 70) {
            $errors['quote-author'] = 'Максимальная величина текста с пробелами 70 символов';
        }

        if (count($errors)) {
            $quoteForm = include_template('add.php', [
                'errors' => $errors,
                'dict'   => $dict,
            ]);
        } else {
            $quoteForm = db_new_post($con, $_POST['quote-heading'], $_POST['quote-text'], '',
                $_POST['quote-author'], '', '', 3, $_POST['quote-tags'], 2);
        }
    } else {
        $quoteForm = include_template('add.php');
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $required = ['link-heading', 'link-url'];
        $dict = ['link-heading' => 'Заголовок.', 'link-url' => 'Ссылка.'];

        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

        if (empty($_POST['link-heading'])) {
            $errors['link-heading'] = 'Это поле необходимо заполнить';
        }

        if (empty($_POST['link-url'])) {
            $errors['link-url'] = 'Это поле необходимо заполнить';
        } elseif (!filter_var($_POST['link-url'], FILTER_VALIDATE_URL)) {
            $errors['link-url'] = 'Укажите ссылку в правильном формате';
        }

        if (count($errors)) {
            $linkForm = include_template('add.php', [
                'errors' => $errors,
                'dict'   => $dict
            ]);
        } else {
            $linkForm = db_new_post($con, $_POST['link-heading'], '', $_POST['link-url'], '', '', '', 3,
                $_POST['link-tags'], 5);
        }
    } else {
        $linkForm = include_template('add.php');
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
        $content = include_template('add.php');
    }

    return $content;
}

/** Функции внесения записей в бд */
/**
 * Возвращает ресурc соединения с указанной базой данных.
 * @param string $db_host имя сервера, по умолчанию localhost
 * @param string $db_user имя для входа в базу данных
 * @param string $db_password пароль для входа в базу
 * @param string $db_name имя базы данных
 *
 * @return mysqli $con ресурс соединения при удачном соединении или строку с ошибкой
 */
function db_connect($db_host = 'localhost', $db_user, $db_password, $db_name)
{
    $con = mysqli_connect($db_host, $db_user, $db_password, $db_name);
    if (!$con) {
        $con = 'Ошибка соединения ' . mysqli_connect_error();
    }

    return $con;
}

/**
 * Создаёт подготовленное выражение для просмотра записей в бд и получает результат его выполнения.
 * @param mysqli $con ресурс соединения
 * @param string $sql sql запрос
 * @param array $data данные
 *
 * @return array $result массив с данными из бд;
 */
function db_fetch_data($con, $sql, $data = [])
{
    $result = [];
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    return $result;
}

/**
 * Создаёт подготовленное выражение для добавления записи в бд и получает результат его выполнения.
 * @param mysqli $con ресурс соединения
 * @param string $sql sql запрос
 * @param array $data данные
 *
 * @return int $result массив с данными из бд;
 */
function db_insert_data($con, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $result = mysqli_insert_id($con);
    }

    return $result;
}

/**
 * Создаёт новый пост и выводит его пользователю.
 * @param mysqli $con ресурс соединения
 * @param string $postTitle текст заголовка
 * @param string $postContent текст поста
 * @param string $userSiteUrl ссылка для поста типа ссылка, по умолчанию пустая строка
 * @param string $quoteAuthor автор цитаты для поста типа цитата, по умолчанию пустая строка
 * @param string $imgUrl ссылка на изображение, по умолчанию пустая строка
 * @param string $videoUrl ссылка на видео, по умолчанию пустая строка
 * @param int $userId id пользователя создавшего пост
 * @param string $hashtags хештеги поста, по умолчанию пустая строка
 * @param int $contentType тип контента
 *
 * @return int $postId id только что созданного поста;
 */
function db_new_post($con, $postTitle, $postContent, $userSiteUrl = '', $quoteAuthor = '', $imgUrl = '', $videoUrl = '', $userId, $hashtags = '', $contentType)
{
    if ($hashtags) {
        $hashtags = mysqli_real_escape_string($con, $hashtags);
        $sql = 'INSERT INTO hashtags (name)
                                VALUES(?)';

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 's', $hashtags);
        mysqli_stmt_execute($stmt);
        $hashtagsId = mysqli_insert_id($con);
    }

    $sql = 'INSERT INTO posts (post_date, title, content, quote_author, users_site_url, img_url, video_url, users_id, content_types_id) 
                       VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssii', $postTitle, $postContent,$quoteAuthor, $userSiteUrl, $imgUrl, $videoUrl, $userId, $contentType);
    mysqli_stmt_execute($stmt);
    $postId = mysqli_insert_id($con);

    if ($postId || $hashtagsId) {
        $sql = 'INSERT INTO posts_has_hashtags (posts_id, hashtags_id) 
                       VALUES (?, ?)';
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $postId, $hashtagsId);
        mysqli_stmt_execute($stmt);
        $result = $postId;
    } else {
        echo 'Ошибка добавления в таблицу Посты и хештеги' . mysqli_error($con);
    }

    if ($result) {
        header('Location: post.php?postId=' . $postId);
    } else {
        echo 'Ошибка базы данных' . mysqli_error($con);
    }

    return $result;
}

/**
 * Создаёт нового пользователя.
 * @param mysqli $con ресурс соединения
 * @param string $newUserEmail электронная почта новго пользователя
 * @param string $newUserName логин нового пользователя
 * @param string $newUserPwd пароль нового пользователя
 * @param string $newUserAva аватар нового пользователя
 * @param string $newUserInfo информация о новом пользователе
 *
 * @return int $result если true, то добавление прошло успешно.;
 */
function db_new_user($con, $newUserEmail,$newUserName, $newUserPwd, $newUserAva = '', $newUserInfo = '')
{

    $sql = 'INSERT INTO users (registration_date, email, name, password, avatar, contact_info) 
                       VALUES (NOW(), ?, ?, ?, ?, ?)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssss', $newUserEmail,$newUserName, $newUserPwd, $newUserAva, $newUserInfo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_insert_id($con);

    if ($result) {
        header('Location:/');
    } else {
        echo 'Ошибка базы данных' . mysqli_error($con);
    }

    return $result;
}

/**
 * Возвращает массив постов с сортировкой по типу контента.
 * @param mysqli $con ресурс соединения
 * @param string $getTab , принимает значения типа контента:
 *                                               text - будут показаны посты с типом контента текст;
 *                                               url - будут показаны посты с типом контента ссылка;
 *                                               picture  - будут показаны посты с типом контента картинка;
 *                                               video - будут показаны посты с типом контента видео;
 *                                               quote  - будут показаны посты с типом контента цитата;
 *                                               all  - будут показаны все посты.
 *
 * @return array $rows посты, сгруппированные по установленному типу контента
 */
function db_read_users_posts($con, $getTab)
{
    if (isset($getTab)) {

        if ($getTab === 'text') {
            $number = 1;
        } elseif ($getTab === 'quote') {
            $number = 2;
        } elseif ($getTab === 'photo') {
            $number = 3;
        } elseif ($getTab === 'video') {
            $number = 4;
        } elseif ($getTab === 'url') {
            $number = 5;
        } elseif ($getTab === 'all') {
            $number = '0 OR p.content_types_id > 0';
        }

        $sql = 'SELECT p.id, u.name, users_site_url, avatar, title, p.content, c.type, img_url FROM posts p 
                LEFT JOIN users u
                ON p.users_id = u.id
                LEFT JOIN content_types c
                ON p.content_types_id = c.id 
                WHERE p.content_types_id = ' . $number;

    } else {

        $sql = 'SELECT p.id, u.name, users_site_url, avatar, title, p.content, c.type, img_url, video_url FROM posts p 
                LEFT JOIN users u
                ON p.users_id = u.id
                LEFT JOIN content_types c
                ON p.content_types_id = c.id';
    }


    $result = mysqli_query($con, $sql);
    $sqlError = mysqli_error($con);

    if ($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . $sqlError;
    }

    return $rows;
}

/**
 * Возвращает массив данных о посте по id.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста в базе данных
 *
 * @return array $rows данные о посте по id;
 */
function db_read_posts_id($con, $postId)
{
    $sql = 'SELECT p.id, u.name, avatar, users_site_url, title, quote_author, content, c.type, p.img_url FROM posts p 
            LEFT JOIN users u
            ON p.users_id = u.id
            LEFT JOIN content_types c
            ON p.content_types_id = c.id
            WHERE p.id =' . $postId;
    $result = mysqli_query($con, $sql);
    $sqlError = mysqli_error($con);

    if ($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . $sqlError;
    }

    return $rows;
};

/**
 * Возвращает массив данных о посте по типу поста.
 * @param mysqli $con ресурс соединения
 * @param int $postType тип контента
 *
 * @return array $rows данные отсортированные по типу контента
 */
function db_read_posts_types($con, $postType)
{
    $sql = 'SELECT p.id, u.name, users_site_url, avatar, title, content, c.type, p.img_url FROM posts p 
            LEFT JOIN users u
            ON p.users_id = u.id
            LEFT JOIN content_types c
            ON p.content_types_id = c.id
            WHERE p.content_types_id =' . $postType;
    $result = mysqli_query($con, $sql);
    $sqlError = mysqli_error($con);

    if ($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . $sqlError;
    }

    return $rows;
}

/** Остальные */
/**
 * Проверяет текст на количество символов, если символов больше, то
 * подставляет ссылку "Читать далее" с открытием по клику.
 * @param string $text текст
 * @param int $maxLength количество символов (по умолчанию 300)
 *
 * @return string текст с/без блоком-ссылкой "Читать далее"
 */
function cutText($text, $maxLength = 300)
{
    if (strlen($text) > $maxLength) {
        $textArr = explode(' ', $text);
        $countLetters = 0;
        $j = 0;
        foreach ($textArr as $word) {
            $countLetters += strlen($word);
            $j++;
            if ($countLetters > $maxLength) {
                return implode(' ', array_slice($textArr, 0,
                        $j)) . '...' . '<br/><a class="post-text__more-link" href="#" style="margin-left: 0">Читать далее</a>';
            }
        }
    }

    return $text;
}

/**
 * Трансформирует дату в "человеческую" строку "Сколько прошло времени с этой даты".
 * @param int $datePoint Число дата, с которой будет показываться перод
 *
 * @return string Текст, количество времени прописью, например "2 минуты назад или 5 часов назад или 7 дней назад etc..."
 */
function showTimeGap($datePoint)
{
    $nowTime = time();
    $diffTime = $nowTime - strtotime($datePoint);

    if ($diffTime < 3600) {
        $num = ceil($diffTime / 60);
        $word = get_noun_plural_form(ceil($diffTime / 60), 'минута', 'минуты', 'минут');
    } elseif ($diffTime > 3600 && $diffTime < 86400) {
        $num = ceil($diffTime / 3600);
        $word = get_noun_plural_form(ceil($diffTime / 3600), 'час', 'часа', 'часов');
    } elseif ($diffTime > 86400 && $diffTime < 604800) {
        $num = ceil($diffTime / 86400);
        $word = get_noun_plural_form(ceil($diffTime / 86400), 'день', 'дня', 'дней');
    } elseif ($diffTime > 604800 && $diffTime < 2629743) {
        $num = ceil($diffTime / 604800);
        $word = get_noun_plural_form(ceil($diffTime / 604800), 'неделя', 'недели', 'недель');
    } elseif ($diffTime > 2629743 && $diffTime < 31556926) {
        $num = ceil($diffTime / 2629743);
        $word = get_noun_plural_form(ceil($diffTime / 2629743), 'месяц', 'месяца', 'месяцев');
    } else {
        $num = ceil($diffTime / 31556926);
        $word = get_noun_plural_form(ceil($diffTime / 31556926), 'год', 'года', 'лет');
    }

    return $num . ' ' . $word . ' назад';
}
