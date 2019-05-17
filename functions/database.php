<?php

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
function db_new_post(
    $con,
    $postTitle,
    $postContent,
    $userSiteUrl = '',
    $quoteAuthor = '',
    $imgUrl = '',
    $videoUrl = '',
    $userId,
    $hashtags = '',
    $contentType
) {
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
    mysqli_stmt_bind_param($stmt, 'ssssssii', $postTitle, $postContent, $quoteAuthor, $userSiteUrl, $imgUrl, $videoUrl,
        $userId, $contentType);
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
        redirect('post.php?postId=' . $postId);
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
function db_new_user($con, $newUserEmail, $newUserName, $newUserPwd, $newUserAva = '', $newUserInfo = '')
{

    $sql = 'INSERT INTO users (registration_date, email, name, password, avatar, contact_info) 
                       VALUES (NOW(), ?, ?, ?, ?, ?)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssss', $newUserEmail, $newUserName, $newUserPwd, $newUserAva, $newUserInfo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_insert_id($con);

    if ($result) {
        session_start();
        db_user_session_by_email($con, $newUserEmail);
        header('Location:/feed.php?block=feed&tab=all');
    } else {
        echo 'Ошибка базы данных' . mysqli_error($con);
    }

    return $result;
}

/**
 * Возвращает массив постов с сортировкой по типу контента и способу сортировки.
 * @param mysqli $con ресурс соединения
 * @param string $contentType , принимает значения типа контента:
 *                                               text - будут показаны посты с типом контента текст;
 *                                               url - будут показаны посты с типом контента ссылка;
 *                                               picture  - будут показаны посты с типом контента картинка;
 *                                               video - будут показаны посты с типом контента видео;
 *                                               quote  - будут показаны посты с типом контента цитата;
 *                                               all  - будут показаны все посты.
 * @param string $order способ сортировки, по умолчанию по количеству просмотров (популярность)
 * @param int $limit Лимит на запрос
 * @param int $offset сдвиг в таблице
 *
 * @return array $rows посты, сгруппированные по установленному типу контента
 */
function db_read_users_posts($con, $contentType = 'all', $order = 'number_of_views', $limit = 0, $offset = 0)
{
    if ($contentType !== 'all') {
        if ($contentType === 'text') {
            $number = 1;
        } elseif ($contentType === 'quote') {
            $number = 2;
        } elseif ($contentType === 'photo') {
            $number = 3;
        } elseif ($contentType === 'video') {
            $number = 4;
        } elseif ($contentType === 'url') {
            $number = 5;
        }

        $sql = 'SELECT p.posts_id, p.post_date, p.title, p.content, p.quote_author, p.img_url, p.video_url,
                        p.users_site_url, p.number_of_views, p.users_id, p.content_types_id, u.users_id, u.registration_date,
                        u.email, u.name, u.password, u.avatar, u.contact_info, c.type
                FROM posts p 
                LEFT JOIN users u
                ON p.users_id = u.users_id
                LEFT JOIN content_types c
                ON p.content_types_id = c.content_types_id
                WHERE p.content_types_id = ?
                ORDER BY ' . $order . ' LIMIT ' . $limit . ' OFFSET ' . $offset;

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            echo 'Ошибка базы данных ' . mysqli_error($con);
        }

    } else {

        $sql = 'SELECT p.posts_id, p.post_date, p.title, p.content, p.quote_author, p.img_url, p.video_url,
                        p.users_site_url, p.number_of_views, p.users_id, p.content_types_id, u.users_id, u.registration_date,
                        u.email, u.name, u.password, u.avatar, u.contact_info, c.type
                FROM posts p  
                LEFT JOIN users u
                ON p.users_id = u.users_id
                LEFT JOIN content_types c
                ON p.content_types_id = c.content_types_id
                ORDER BY ' . $order . ' LIMIT ' . $limit . ' OFFSET ' . $offset;

        $result = mysqli_query($con, $sql);

        if ($result) {
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            echo 'Ошибка базы данных ' . mysqli_error($con);
        }
    }


    return $rows;
}

/**
 * Возвращает массив постов для пользователя-подписчика, сортируется по дате, а так же по типу контента.
 * @param mysqli $con ресурс соединения
 * @param string $contentType , принимает значения типа контента:
 *                                               text - будут показаны посты с типом контента текст;
 *                                               url - будут показаны посты с типом контента ссылка;
 *                                               picture  - будут показаны посты с типом контента картинка;
 *                                               video - будут показаны посты с типом контента видео;
 *                                               quote  - будут показаны посты с типом контента цитата;
 *                                               all  - будут показаны все посты.
 * @param int $myUserId id пользователя для кого формируется запрос
 *
 * @return array $rows посты, сгруппированные по установленному типу контента
 */
function db_read_users_sub_posts($con, $contentType = 'all', $myUserId)
{

    if ($contentType !== 'all') {
        if ($contentType === 'text') {
            $number = 1;
        } elseif ($contentType === 'quote') {
            $number = 2;
        } elseif ($contentType === 'photo') {
            $number = 3;
        } elseif ($contentType === 'video') {
            $number = 4;
        } elseif ($contentType === 'url') {
            $number = 5;
        }

        $sql = 'SELECT * FROM posts p 
                LEFT JOIN users u
                ON p.users_id = u.users_id
                LEFT JOIN content_types c
                ON p.content_types_id = c.content_types_id
                LEFT JOIN subscribers s
                ON p.users_id = s.users_subscribe_id
                WHERE s.users_id = ? AND p.content_types_id = ?
                ORDER BY p.post_date';

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $myUserId, $number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            echo 'Ошибка базы данных ' . mysqli_error($con);
        }

    } else {

        $sql = 'SELECT * FROM posts p  
                LEFT JOIN users u
                ON p.users_id = u.users_id
                LEFT JOIN content_types c
                ON p.content_types_id = c.content_types_id
                LEFT JOIN subscribers s
                ON p.users_id = s.users_subscribe_id
                WHERE s.users_id = ?
                ORDER BY p.post_date';

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $myUserId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            echo 'Ошибка базы данных ' . mysqli_error($con);
        }
    }


    return $rows;
}

/**
 * Возвращает количество постов с сортировкой по типу контента.
 * @param mysqli $con ресурс соединения
 * @param string $contentType , принимает значения типа контента:
 *                                               text - будут показаны посты с типом контента текст;
 *                                               url - будут показаны посты с типом контента ссылка;
 *                                               picture  - будут показаны посты с типом контента картинка;
 *                                               video - будут показаны посты с типом контента видео;
 *                                               quote  - будут показаны посты с типом контента цитата;
 *                                               all  - будут показаны все посты.
 *
 * @return int $rows число постов.
 */
function db_read_users_posts_by_tab($con, $contentType = 'all')
{
    if ($contentType !== 'all') {
        if ($contentType === 'text') {
            $number = 1;
        } elseif ($contentType === 'quote') {
            $number = 2;
        } elseif ($contentType === 'photo') {
            $number = 3;
        } elseif ($contentType === 'video') {
            $number = 4;
        } elseif ($contentType === 'url') {
            $number = 5;
        }

        $sql = 'SELECT COUNT(*) AS cnt FROM posts p 
                LEFT JOIN users u
                ON p.users_id = u.users_id
                LEFT JOIN content_types c
                ON p.content_types_id = c.content_types_id
                WHERE p.content_types_id = ?';

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result)['cnt'];
        } else {
            echo 'Ошибка базы данных ' . mysqli_error($con);
        }

    } else {

        $sql = 'SELECT COUNT(*) AS cnt FROM posts p 
                LEFT JOIN users u
                ON p.users_id = u.users_id
                LEFT JOIN content_types c
                ON p.content_types_id = c.content_types_id';

        $result = mysqli_query($con, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result)['cnt'];
        } else {
            echo 'Ошибка базы данных ' . mysqli_error($con);
        }
    }


    return $row;
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
    $sql = 'SELECT posts_id, u.name, avatar, users_site_url, title, quote_author, p.content, c.type, p.img_url, u.users_id FROM posts p 
            LEFT JOIN users u
            ON p.users_id = u.users_id
            LEFT JOIN content_types c
            ON p.content_types_id = c.content_types_id 
            WHERE p.posts_id =' . $postId;
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
 * Записывает в сессию данные о пользователе по электронной почте.
 * @param mysqli $con ресурс соединения
 * @param string $email электронная почта пользователя
 *
 * @return array $rows данные пользователя
 */
function db_user_session_by_email($con, $email)
{
    $sql = 'SELECT * FROM users 
            WHERE email = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
    }


    foreach ($rows as $row) {

        $_SESSION['user-id'] = $row['users_id'];
        $_SESSION['user-posts'] = db_get_user_posts($con, $row['users_id']);
        $_SESSION['user-reg-date'] = $row['registration_date'];
        $_SESSION['user-name'] = $row['name'];
        $_SESSION['user-ava'] = $row['avatar'];
        $_SESSION['user-contact-info'] = $row['contact_info'];
        $_SESSION['users-subs'] = db_get_user_subs($con, $row['users_id']);
        $_SESSION['user-active'] = showTimeGap($row['registration_date']);
    }

    return $rows;
}

/**
 * Возвращает количество постов пользователя по id.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return int $res количество постов
 */
function db_get_user_posts($con, $userId)
{
    $sql = 'SELECT COUNT(*) FROM posts 
            WHERE users_id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $res = mysqli_fetch_row($result);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
    }

    return $res[0];
}

/**
 * Возвращает информацию о пользователя по id.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return array $rows вся информация о пользователе из таблицы пользователи
 */
function db_get_user_info($con, $userId)
{
    $sql = 'SELECT * FROM users 
            WHERE users_id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
    }

    return $rows;
}

/**
 * Возвращает количество подписчиков пользователя по id.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return int $res количество подписчиков
 */
function db_get_user_subs($con, $userId)
{
    $sql = 'SELECT COUNT(*) FROM subscribers 
            WHERE users_id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $res = mysqli_fetch_row($result);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
    }

    return $res[0];
}

/**
 * Создаёт новый комментарий и переадрессовыввает обратно на строничку поста.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста к которому присваивается комментарий
 * @param string $commentText текст комментария
 * @param string $userId id пользователя кто оставил комментарий
 *
 * @return bool $result если true, то добавление прошло успешно
 */
function db_new_comment($con, $postId, $commentText, $userId)
{

    $sql = 'INSERT INTO comments (data_time_of_origin, text, post_id, users_id) 
                       VALUES (NOW(), ?, ?, ?)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sii', $commentText, $postId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_insert_id($con);

    if ($result) {
        header('Refresh: 0');
    } else {
        echo 'Ошибка базы данных' . mysqli_error($con);
    }

    return $result;
}

/**
 * Возвращает массив с комментариями к посту по id.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 *
 * @return array $rows массив комментариев к посту
 */
function db_get_comments_to_post($con, $postId)
{
    $sql = 'SELECT * FROM comments c
            JOIN users u
            ON c.users_id = u.users_id
            WHERE post_id = ? 
            ORDER BY c.data_time_of_origin DESC';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
    }

    return $rows;
}

/**
 * Возвращает количество лайков поста по id.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 *
 * @return int $res количество лайков
 */
function db_count_likes_to_post($con, $postId)
{
    $sql = 'SELECT COUNT(*) FROM likes
            WHERE post_id =' . $postId;

    $result = mysqli_query($con, $sql);

    if ($result) {
        $res = mysqli_fetch_row($result);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
    }

    return $res[0];
}

/**
 * Возвращает количество комментариев поста по id.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 *
 * @return int $res количество комментариев
 */
function db_count_comments_to_post($con, $postId)
{
    $sql = 'SELECT COUNT(*) FROM comments
            WHERE post_id =' . $postId;

    $result = mysqli_query($con, $sql);

    if ($result) {
        $res = mysqli_fetch_row($result);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
    }

    return $res[0];
}

/**
 * Возвращает данные о постах пользователя по id.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return array $res ассоциатиынй массив данных с постами пользователя
 */
function db_get_user_arr_posts($con, $userId)
{
    $sql = 'SELECT * FROM posts 
            WHERE users_id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
    }

    return $res;
}

/**
 * Добавляет лайк посту по id.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 * @param int $userId id пользователя
 *
 * @return bool $res результат выполнения ф-ии
 */
function db_add_like($con, $postId, $userId)
{
    if (!db_get_like($con, $postId, $userId)) {

        $sql = 'INSERT INTO likes (post_id, user_id) 
                       VALUES (?, ?)';

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $postId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_insert_id($con);

        if (!$result) {
            echo 'Ошибка базы данных ' . mysqli_error($con);
        }
    } else {
        $result = false;
    }

    return $result;
}

/**
 * Проверяет наличие лайка.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 * @param int $userId id пользователя
 *
 * @return bool $result результат выполнения ф-ии, если лайк есть возвращает true, если нет false
 */
function db_get_like($con, $postId, $userId)
{
    $sql = 'SELECT likes_id FROM likes
            WHERE post_id =' . $postId . ' AND user_id =' . $userId;

    $res = mysqli_query($con, $sql);
    if ($res) {
        $row = mysqli_fetch_row($res);
    }
    return $row[0];
}

/**
 * Удаляет лайк.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 * @param int $userId id пользователя
 *
 * @return bool $result результат выполнения ф-ии
 */
function db_del_like($con, $postId, $userId)
{
    $sql = 'DELETE FROM likes
            WHERE post_id = ? AND user_id = ?';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $postId, $userId);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        $res = 'удалили успешно';
    }

    return $res;
}

/**
 * Проверяет наличие подписки на пользователя.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 * @param int $userSudId id пользователя на которого проверяется подписка
 *
 * @return bool $res результат выполнения ф-ии, если есть подписка возвращает true, если нет false
 */
function db_check_subscription($con, $userId, $userSudId)
{
    $sql = 'SELECT users_id FROM subscribers
            WHERE users_id =' . $userId . ' AND users_subscribe_id =' . $userSudId;

    $res = mysqli_query($con, $sql);
    if ($res) {
        $row = mysqli_fetch_row($res);
    } else {
        echo 'Ошибка бд' . mysqli_error($con);
    }

    return $row[0];
}

/**
 * Добавляет подписку на пользователя.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 * @param int $userSudId id пользователя на которого подписываемся
 *
 * @return bool $res результат выполнения ф-ии, если есть подписка возвращает true, если нет false
 */
function db_ins_subscription($con, $userId, $userSudId)
{
    if (!db_check_subscription($con, $userId, $userSudId)) {
        $sql = 'INSERT INTO subscribers (users_id, users_subscribe_id)
                              VALUES (?, ?)';

        $stml = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stml, 'ii', $userId, $userSudId);
        $res = mysqli_stmt_execute($stml);
        if (!$res) {
            echo 'Ошибка бд' . mysqli_error($con);
        }
    }

    return $res;
}

/**
 * Удаляет подписку.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 * @param int $userSudId id пользователя на которого удаляем подписку
 *
 * @return bool $result результат выполнения ф-ии
 */
function db_del_sub($con, $userId, $userSudId)
{
    if (db_check_subscription($con, $userId, $userSudId)) {

        $sql = 'DELETE FROM subscribers
            WHERE users_id = ? AND users_subscribe_id = ?';

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $userSudId);
        $res = mysqli_stmt_execute($stmt);
        if ($res) {
            $res = 'удалили успешно';
        }
    }

    return $res;
}
