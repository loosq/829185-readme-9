<?php

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
 * Возвращает массив постов с сортировкой по типу контента.
 * @param mysqli $con ресурс соединения
 * @param string $getTab, принимает значения типа контента:
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
        } elseif ($getTab === 'all'){
            $number = '0 OR p.content_types_id > 0';
        }

        $sql = 'SELECT p.id, u.name, avatar, title, content, c.type FROM posts p 
                LEFT JOIN users u
                ON p.users_id = u.id
                LEFT JOIN content_types c
                ON p.content_types_id = c.id 
                WHERE p.content_types_id = ' . $number;

    } else {

        $sql = 'SELECT p.id, u.name, avatar, title, content, c.type FROM posts p 
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
function db_read_posts_id($con, $postId){
    $sql = 'SELECT p.id, u.name, avatar, title, content, c.type, p.img_url FROM posts p 
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
function db_read_posts_types($con, $postType){
    $sql = 'SELECT p.id, u.name, avatar, title, content, c.type, p.img_url FROM posts p 
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
};
