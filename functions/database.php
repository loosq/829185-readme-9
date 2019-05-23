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
function dbConnect($db_host = 'localhost', $db_user, $db_password, $db_name)
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
function dbFetchData($con, $sql, $data = [])
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
function dbInsertData($con, $sql, $data = [])
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
function dbNewPost(
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
    $sql = 'INSERT INTO posts (post_date, title, content, quote_author, users_site_url, img_url, video_url, users_id, content_types_id, isrepost) 
                       VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, 0)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssii', $postTitle, $postContent, $quoteAuthor, $userSiteUrl, $imgUrl, $videoUrl,
        $userId, $contentType);
    mysqli_stmt_execute($stmt);
    $postId = mysqli_insert_id($con);

    if ($hashtags) {
        $hashtagsArr = explode(' ', $hashtags);
        $hashtagsIdArr = [];
        foreach ($hashtagsArr as $hashtag) {
            $hashtag = trim($hashtag);
            $hashtagStr = mysqli_real_escape_string($con, $hashtag);
            if (!empty($hashtag)) {
                $sql = 'INSERT INTO hashtags (name)
                                VALUES(?)';
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, 's', $hashtagStr);
                mysqli_stmt_execute($stmt);
                $hashtagId = mysqli_insert_id($con);
                $hashtagsIdArr[] = $hashtagId;
            }
        }

        foreach ($hashtagsIdArr as $hashtagId) {
            $sql = 'INSERT INTO posts_has_hashtags (posts_to_hashtags_id, hashtags_to_posts_id)
                                VALUES(?,?)';
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'ii', $postId, $hashtagId);
            mysqli_stmt_execute($stmt);
        }

    }


    if ($postId) {
        sendEmailNewPost($con, $userId, $_SESSION['user-name'], $postTitle);
        redirect('post.php?postId=' . $postId);
    } else {
        echo 'Ошибка базы данных' . mysqli_error($con);
    }

    return $postId;
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
function dbNewUser($con, $newUserEmail, $newUserName, $newUserPwd, $newUserAva = '', $newUserInfo = '')
{

    $sql = 'INSERT INTO users (registration_date, email, name, password, avatar, contact_info) 
                       VALUES (NOW(), ?, ?, ?, ?, ?)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssss', $newUserEmail, $newUserName, $newUserPwd, $newUserAva, $newUserInfo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_insert_id($con);

    if ($result) {
        session_start();
        dbUserSessionByEmail($con, $newUserEmail);
        header('Location:/feed.php?block=feed&tab=all');
    } else {
        echo 'Ошибка базы данных' . mysqli_error($con);
    }

    return $result;
}

/**
 * Возвращает массив постов с сортировкой по типу контента и способу сортировки.
 * @param mysqli $con ресурс соединения
 * @param string $tab значения типа контента:
 *                                               text - будут показаны посты с типом контента текст;
 *                                               url - будут показаны посты с типом контента ссылка;
 *                                               picture  - будут показаны посты с типом контента картинка;
 *                                               video - будут показаны посты с типом контента видео;
 *                                               quote  - будут показаны посты с типом контента цитата;
 *                                               all  - будут показаны все посты.
 * @param string $sort способ сортировки, по умолчанию по количеству просмотров (популярность)
 * @param int $limit Лимит на запрос
 * @param int $offset сдвиг в таблице
 *
 * @return array $rows посты, сгруппированные по установленному типу контента
 */
function dbReadUsersPosts($con, $tab = 'all', $sort = null, $limit = 0, $offset = 0)
{

    if ($sort === 'pop') {
        $srt = ' ORDER BY number_of_views ';
    } elseif ($sort === 'date') {
        $srt = ' ORDER BY post_date DESC ';
    } elseif ($sort === 'likes') {
        $srt = ' ORDER BY likes ';
    } else {
        $srt = '';
    }


    if ($tab === 'text') {
        $contentId = ' AND p.content_types_id = 1';
    } elseif ($tab === 'quote') {
        $contentId = ' AND p.content_types_id = 2';
    } elseif ($tab === 'photo') {
        $contentId = ' AND p.content_types_id = 3';
    } elseif ($tab === 'video') {
        $contentId = ' AND p.content_types_id = 4';
    } elseif ($tab === 'url') {
        $contentId = ' AND p.content_types_id = 5';
    } else {
        $contentId = '';
    }

    $sql = 'SELECT p.posts_id, p.post_date, p.title, p.content, p.quote_author, p.img_url, p.video_url,
                        p.users_site_url, p.number_of_views, p.users_id, p.content_types_id, u.users_id,
                         u.registration_date, u.email, u.name, u.password, u.avatar, u.contact_info, c.type,
                        (SELECT COUNT(l.post_id) FROM likes l) AS likes
                FROM posts p 
                LEFT JOIN users u
                ON p.users_id = u.users_id
                LEFT JOIN content_types c
                ON p.content_types_id = c.content_types_id
                LEFT JOIN likes l
                ON p.posts_id = l.post_id WHERE p.isrepost = 0' .
        $contentId . $srt . ' LIMIT ' . $limit . ' OFFSET ' . $offset;


    $result = mysqli_query($con, $sql);

    if ($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
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
function dbReadUsersSubPosts($con, $contentType = 'all', $myUserId)
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
function dbReadUsersPostsByTab($con, $contentType = 'all')
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
                WHERE p.content_types_id = (?) AND isrepost = 0';

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
                ON p.content_types_id = c.content_types_id WHERE isrepost = 0';

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
function dbReadPostsId($con, $postId)
{
    $sql = 'SELECT posts_id, u.name, avatar, users_site_url, title, quote_author, p.content, c.type, p.img_url, p.video_url, u.users_id, p.isrepost FROM posts p 
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
function dbUserSessionByEmail($con, $email)
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
        $_SESSION['user-posts'] = dbGetUserPosts($con, $row['users_id']);
        $_SESSION['user-reg-date'] = $row['registration_date'];
        $_SESSION['user-name'] = $row['name'];
        $_SESSION['user-ava'] = $row['avatar'];
        $_SESSION['user-contact-info'] = $row['contact_info'];
        $_SESSION['users-subs'] = dbGetUserSubs($con, $row['users_id']);
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
function dbGetUserPosts($con, $userId)
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
function dbGetUserInfo($con, $userId)
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
function dbGetUserSubs($con, $userId)
{
    $sql = 'SELECT COUNT(*) FROM subscribers 
            WHERE users_subscribe_id = ?';
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
function dbNewComment($con, $postId, $commentText, $userId)
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
function dbGetCommentsToPost($con, $postId)
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
function dbCountLikesToPost($con, $postId)
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
function dbCountCommentsToPost($con, $postId)
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
function dbGetUserArrPosts($con, $userId)
{
    $sql = 'SELECT * FROM posts p LEFT JOIN users u ON p.users_id = u.users_id
            WHERE p.users_id = (?) ORDER BY p.post_date DESC';
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
function dbAddLike($con, $postId, $userId)
{
    if (!dbGetLike($con, $postId, $userId)) {

        $sql = 'INSERT INTO likes (likes_date, post_id, user_id) 
                       VALUES (NOW(), ?, ?)';

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
function dbGetLike($con, $postId, $userId)
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
function dbDelLike($con, $postId, $userId)
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
function dbCheckSubscription($con, $userId, $userSudId)
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
 * Возвращает массив всех подписчиков.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return array $rows ассоциативный массив
 */
function dbGetAllSubs($con, $userId)
{
    $sql = 'SELECT u.users_id, u.email, u.name, u.registration_date, u.avatar, 
            (SELECT COUNT(p.users_id) FROM posts p WHERE p.users_id = (?)) AS postsCount,
            (SELECT COUNT(s.users_id) FROM subscribers s WHERE s.users_subscribe_id = (?)) AS subsCount 
            FROM users u
            LEFT JOIN subscribers s
            ON u.users_id = s.users_id
            WHERE s.users_subscribe_id = (?)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'iii', $userId, $userId, $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка бд' . mysqli_error($con);
    }

    return $rows;
}

/**
 * Добавляет подписку на пользователя.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 * @param int $userSudId id пользователя на которого подписываемся
 *
 * @return bool $res результат выполнения ф-ии, если есть подписка возвращает true, если нет false
 */
function dbInsSubscription($con, $userId, $userSudId)
{
    if (!dbCheckSubscription($con, $userId, $userSudId)) {
        $sql = 'INSERT INTO subscribers (users_id, users_subscribe_id)
                              VALUES (?, ?)';

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $userSudId);
        $res = mysqli_stmt_execute($stmt);
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
function dbDelSub($con, $userId, $userSudId)
{
    if (dbCheckSubscription($con, $userId, $userSudId)) {

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

/**
 * Возвращает все хештеги к посту.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 *
 * @return array $row массив хештегов
 */
function dbGetAllHashtagsToPost($con, $postId)
{


    $sql = 'SELECT name FROM hashtags h LEFT JOIN posts_has_hashtags ph 
            ON h.hashtags_id = ph.hashtags_to_posts_id WHERE ph.posts_to_hashtags_id= ?';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $postId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    return $rows;
}

/**
 * Создаёт чат между 2мя пользователями, в поле chat_hash хрониться ключ лога между двумя пользователями,
 * если чата между ними не было - создаётся новый, если уже был, то используется предыдущий.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя отправителя
 * @param int $userIdGet id пользователя получателя
 * @param string $content текст сообщения
 *
 * @return bool $res результат выполнения ф-ии
 */
function dbNewMsg($con, $userId, $userIdGet, $content)
{
    $content = htmlspecialchars(trim($content));

    $sql = 'SELECT chat_hash FROM messages WHERE 
            users_id_get = (?) AND users_id_send = (?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $userIdGet);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);

    if (!$rows){
        $sql = 'SELECT chat_hash FROM messages WHERE 
                users_id_send = (?) AND users_id_get = (?)';
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $userIdGet);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    $sql = 'INSERT INTO messages (date_of_origin, users_id_send, users_id_get, text, chat_hash)
                              VALUES (NOW(), ?, ?, ?, ?)';
    $chatHash = $rows[0]['chat_hash'] ?? uniqid();
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'iiss', $userId, $userIdGet, $content, $chatHash);
    $res = mysqli_stmt_execute($stmt);

    return $res;
}

/**
 * Возвращает сообщения между 2мя пользователями.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя отправителя
 * @param int $userIdGet id пользователя получателя
 *
 * @return array $rows массив сообщения
 */
function dbGetChat($con, $userId, $userIdGet)
{

    $sql = 'SELECT  m.date_of_origin, m.text, u.name, m.users_id_get, m.users_id_send, u.avatar 
            FROM messages m LEFT JOIN users u 
            ON m.users_id_get = u.users_id 
            WHERE 
            (m.users_id_send = (?) AND m.users_id_get = (?)
            AND
            m.users_id_get = (?) AND m.users_id_send = (?)) 
            ORDER BY m.date_of_origin DESC LIMIT 3';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'iiii', $userId, $userIdGet, $userIdGet, $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (!$res) {
        echo 'Ошибка бд' . mysqli_error($con);
    } else {
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    return $rows;
}

/**
 * Возвращает хеши всех чатов.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя отправителя
 *
 * @return array $rows массив данных
 */
function dbGetAllChats($con, $userId)
{
    $sql = 'SELECT MAX(messages_id) as messages_id, MIN(text) as text, MAX(date_of_origin) as date,
            MAX(users_id_send) as users_id_send, MAX(users_id_get) as users_id_get 
            FROM messages m 
            WHERE m.chat_hash IN (SELECT DISTINCT m.chat_hash FROM messages m 
            WHERE m.users_id_send = (?) OR m.users_id_get = (?)) GROUP BY m.chat_hash';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $rows = false;
    }

    return $rows;
}

/**
 * Возвращает информацию о пользователях лайкнувших пост пользователя.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return array $rows массив с данными
 */
function dbGetUsersByLike($con, $userId)
{
    $sql = 'SELECT p.posts_id, p.content, u.users_id, u.name, l.likes_date, ct.type, u.avatar, p.video_url FROM posts p
            LEFT JOIN likes l ON
            p.posts_id = l.post_id
            LEFT JOIN users u ON
            u.users_id = l.user_id
            LEFT JOIN content_types ct ON 
            p.content_types_id = ct.content_types_id
            WHERE p.users_id = ? AND u.users_id IS NOT NULL';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
        $res = false;
    }

    return $res;
}

/**
 * Создаёт новый репост и переадресовывает пользователя на страницу своего профиля.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста который репостим
 * @param int $userId id пользователя кто делает репост
 */
function dbNewRepost($con, $postId, $userId)
{
    $postId = (int)$postId;
    if ($postId && $userId) {
        $sql = 'SELECT * FROM posts p LEFT JOIN users u 
                ON p.users_id = u.users_id WHERE
                 p.posts_id = (?)';
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $postId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    } else {
        redirectBack();
    }

    if ($rows[0]['isrepost'] === 0) {
        $userAuthId = $rows[0]['users_id'];
        $postOrigId = $rows[0]['posts_id'];
    } else {
        $userAuthId = $rows[0]['user_author_id'];
        $postOrigId = $rows[0]['post_origin_id'];
        $query = mysqli_query($con, 'SELECT * FROM posts WHERE post_origin_id = ' . $postOrigId);
        $res = mysqli_fetch_all($query, MYSQLI_ASSOC);
        if ($res) {
            redirect('profile.php?user=' . $userId . '&tab=posts');
        }
    }

    $query = mysqli_query($con, 'SELECT * FROM posts WHERE post_origin_id = ' . $postOrigId);
    $res = mysqli_fetch_all($query, MYSQLI_ASSOC);
    if ($res) {

        redirect('profile.php?user=' . $userId . '&tab=posts');
    }


    $sqlRepost = 'INSERT INTO posts (post_date, title, content, quote_author, img_url, video_url,
                users_site_url, number_of_views, users_id, content_types_id, isrepost, user_author_id, post_origin_id)
                VALUES (NOW(), ?, ?, ?, ?, ?, ?, 0, ?, ?, 1, ?, ?)';
    $stmtRepost = mysqli_prepare($con, $sqlRepost);
    mysqli_stmt_bind_param($stmtRepost, 'ssssssiiii',
        $rows[0]['title'],
        $rows[0]['content'],
        $rows[0]['quote_author'],
        $rows[0]['img_url'],
        $rows[0]['video_url'],
        $rows[0]['users_site_url'],
        $userId,
        $rows[0]['content_types_id'],
        $userAuthId,
        $postOrigId);
    mysqli_stmt_execute($stmtRepost);

    redirect('profile.php?user=' . $userId . '&tab=posts');
}

/**
 * Возвращает информацию о пользователях с которого сделан репост.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return array $rows массив с данными
 */
function dbGetUsersRepostInfo($con, $userId)
{
    $sql = 'SELECT u.name, u.avatar, p.post_date FROM users u
            LEFT JOIN posts p ON u.users_id = p.users_id 
            WHERE p.user_author_id = (?)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
        $res = false;
    }

    return $res;
}

/**
 * Возвращает имя пользователя по id.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return string $name имя пользователя
 */
function dbGetUserName($con, $userId)
{
    $sql = 'SELECT name FROM users 
            WHERE users_id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $name = mysqli_fetch_row($result);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
        $name = false;
    }

    return $name[0];
}

/**
 * Возвращает ссылку на аватар пользователя по id.
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя
 *
 * @return string $ava ссылка на аватар
 */
function dbGetUserAva($con, $userId)
{
    $sql = 'SELECT avatar FROM users 
            WHERE users_id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $ava = mysqli_fetch_row($result);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
        $ava = false;
    }

    return $ava[0];
}

/**
 * Возвращает дату поста по id.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 *
 * @return string $date ссылка на аватар
 */
function dbGetPostDate($con, $postId)
{
    $sql = 'SELECT post_date FROM posts 
            WHERE posts_id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $date = mysqli_fetch_row($result);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
        $date = false;
    }

    return $date[0];
}

/**
 * Возвращает количество репостов поста по id.
 * @param mysqli $con ресурс соединения
 * @param int $postId id поста
 *
 * @return string $countReposts ссылка на аватар
 */
function dbGetPostReposts($con, $postId)
{
    $sql = 'SELECT COUNT(post_origin_id) FROM posts 
            WHERE post_origin_id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $countReposts = mysqli_fetch_row($result);
    } else {
        echo 'Ошибка базы данных ' . mysqli_error($con);
        $countReposts = false;
    }

    return $countReposts[0];
}

/**
 * Возвращает контент в зависимости от поискового запроса.
 * @param mysqli $con ресурс соединения
 * @param string $search поисковый запрос
 *
 * @return string $coтеуте данные для шаблона
 */
function multySearch($con, $search)
{
    if ($search) {
        $search = trim($search);
        $firstChar = $search{0};
        if ($firstChar !== '#') {
            if (strlen($search) >= 3) {
                $sql = 'SELECT * FROM posts p
                LEFT JOIN users u ON p.users_id = u.users_id
                LEFT JOIN content_types c ON p.content_types_id = c.content_types_id
                WHERE MATCH(p.content, p.title) AGAINST(?)';
                $stmt = db_get_prepare_stmt($con, $sql, [$search]);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $cards = mysqli_fetch_all($result, MYSQLI_ASSOC);

            } else {
                $sql = 'SELECT * FROM posts p
                LEFT JOIN users u ON p.users_id = u.users_id
                LEFT JOIN content_types c ON p.content_types_id = c.content_types_id
                WHERE p.content LIKE (?) OR p.title LIKE (?)';
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, 'dd', $search, $search);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $cards = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        } else {
            $search = substr($search, 1);
            $search = (string)$search;
            if (strlen($search) >= 3) {
                $sql = 'SELECT * FROM posts p 
                LEFT JOIN posts_has_hashtags phh ON phh.posts_to_hashtags_id = p.posts_id
                LEFT JOIN hashtags h ON phh.hashtags_to_posts_id = h.hashtags_id
                LEFT JOIN content_types t ON p.content_types_id = t.content_types_id
                LEFT JOIN users u ON p.users_id = u.users_id
                WHERE MATCH(h.name) AGAINST(?)';
                $saveSearch = '#' . $search;
            } else {
                $sql = 'SELECT * FROM posts p 
                LEFT JOIN posts_has_hashtags phh ON phh.posts_to_hashtags_id = p.posts_id
                LEFT JOIN hashtags h ON phh.hashtags_to_posts_id = h.hashtags_id
                LEFT JOIN content_types t ON p.content_types_id = t.content_types_id
                LEFT JOIN users u ON p.users_id = u.users_id
                WHERE h.name LIKE (?)';
                $saveSearch = '#' . $search;
                $search = '%' . $search . '%';
            }

            $stmt = db_get_prepare_stmt($con, $sql, [$search]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $cards = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $search = $saveSearch;
        }

        if (!$cards) {
            $content = include_template('search-no-results.php', [
                'search' => $search,
            ]);
        } else {
            $content = include_template('search.php', [
                'cards'  => $cards,
                'search' => $search,
                'con'    => $con,
            ]);
        }

    } else {
        $content = include_template('search-no-results.php', [
            'search' => $search,
        ]);
    }

    return $content;
}
