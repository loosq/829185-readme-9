<?php

$nowTime = time();
$postTime = generate_random_date($item);
$diffTime = $nowTime - strtotime($postTime);

if ($diffTime < 3600) {
    $diffTime = ceil($diffTime / 60) . ' ' .get_noun_plural_form(ceil($diffTime / 60), 'минута', 'минуты', 'минут') . ' назад';
}
elseif ($diffTime > 3600 && $diffTime < 86400) {
    $diffTime = ceil($diffTime / 3600) . ' ' .get_noun_plural_form(ceil($diffTime / 3600), 'час', 'часа', 'часов') . ' назад';
}
elseif ($diffTime > 86400 && $diffTime < 604800) {
    $diffTime = ceil($diffTime / 86400) . ' ' .get_noun_plural_form(ceil($diffTime / 86400), 'день', 'дня', 'дней') . ' назад';
}
elseif ($diffTime > 604800 && $diffTime < 2629743) {
    $diffTime = ceil($diffTime / 604800) . ' ' .get_noun_plural_form(ceil($diffTime / 604800), 'неделя', 'недели', 'недель') . ' назад';
}
elseif ($diffTime > 2629743 && $diffTime < 31556926) {
    $diffTime = ceil($diffTime / 2629743) . ' ' .get_noun_plural_form(ceil($diffTime / 2629743), 'месяц', 'месяца', 'месяцев') . ' назад';
}
else {
    $diffTime = ceil($diffTime / 31556926) . ' ' .get_noun_plural_form(ceil($diffTime / 31556926), 'год', 'года', 'лет') . ' назад';
}
