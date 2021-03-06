<?php

/**
 * Проверяет текст на количество символов, если символов больше, то
 * подставляет ссылку "Читать далее" с открытием по клику переадресовывает на пост.
 * @param string $text текст
 * @param int $maxLength количество символов (по умолчанию 300)
 * @param int $postId id поста
 *
 * @return string текст с/без блоком-ссылкой "Читать далее"
 */
function cutText($text, $maxLength = 300, $postId)
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
                        $j)) . '...' . '<br/><a class="post-text__more-link" href="post.php?postId=' . $postId . '" style="margin-left: 0"> Читать далее </a>';
            }
        }
    }

    return $text;
}

/**
 * Трансформирует дату в человеко-понятную строку "Сколько прошло времени с этой даты".
 * @param int $datePoint Число дата, с которой будет показываться перод
 *
 * @return string Текст, количество времени прописью, например "2 минуты или 5 часов или 7 дней etc..."
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

    return $num . ' ' . $word;
}

/**
 * Перенаправляет пользователя по заданной директории.
 * @param string $path путь переадресации
 *
 */
function redirect($path)
{
    header('Location: ' . $path);
    die;
}

/**
 * Перенаправляет пользователя на главную.
 */
function redirectHome()
{
    redirect('/');
}

/**
 * Перенаправляет пользователя на страницу назад.
 */
function redirectBack()
{
    redirect($_SERVER['HTTP_REFERER']);
}

/**
 * Проверяет залогинин ли пользователь.
 */
function isUserLoggedIn()
{
    $userName = $_SESSION['user-name'] ?? '';
    if ($userName) {
        return true;
    }

    return false;
}

/**
 * Показывает ЧП дату сообщений.
 * @param string $datePoint дата
 *
 * @return string $res Текст, ЧП дата
 */
function showTimeOfMsg($datePoint)
{
    $date = strtotime($datePoint);
    $nowTime = time();
    $diffTime = $nowTime - $date;
    if ($diffTime < 86400) {
        $res = strftime('%H:%M', $date);
    } elseif ($diffTime >= 86400 && $diffTime < 604800) {
        $res = strftime('%e %h', $date);
    } elseif ($diffTime >= 604800 && $diffTime < 31556926) {
        $res = strftime('%d %m %Y', $date);
    }

    return $res;
}

/**
 * Обрезает сообщение и добавляет "..." в конце.
 * @param string $text текст сообщения
 * @param int $len количество символов
 *
 * @return string обрезанный текст
 */
function msgTextCut($text, $len = null)
{
    if (strlen($text) > $len) {
        $text = substr($text, 0, $len) . '...';
    }

    return $text;
}

/**
 * Добавляет данные пользователя в сессию.
 * @param array $userData массив с данными пользователя
 * @param mysqli $con подключение к бд
 *
 * @return array сессия.
 */
function startNewSession($con, $userData)
{
    foreach ($userData as $userRow) {
        $_SESSION['user-id'] = $userRow['users_id'];
        $_SESSION['user-posts'] = dbGetUserPosts($con, $userRow['users_id']);
        $_SESSION['user-reg-date'] = $userRow['registration_date'];
        $_SESSION['user-name'] = $userRow['name'];
        $_SESSION['user-ava'] = $userRow['avatar'];
        $_SESSION['user-contact-info'] = $userRow['contact_info'];
        $_SESSION['users-subs'] = dbGetUserSubs($con, $userRow['users_id']);
        $_SESSION['user-active'] = showTimeGap($userRow['registration_date']);
    }

    return $_SESSION;
}
