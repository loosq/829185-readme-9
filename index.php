<?php

date_default_timezone_set('Europe/Moscow');
setlocale(LC_ALL, 'ru_RU');

include_once  'sql/sql.php';
include_once 'helpers.php';
include_once 'cards.php';
include_once 'functions.php';

$content = include_template('index.php', ['cards' => $cards]);

$is_auth = rand(0, 1);
$user_name = 'Sergei';
$title = 'Readme';

$layoutContent = include_template('layout.php', [
    'content' => $content,
    'title'   => $title,
    'user'    => $user_name,
    'is_auth' => $is_auth
]);

echo $layoutContent;
