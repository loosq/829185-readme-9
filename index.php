<?php

date_default_timezone_set('Europe/Moscow');
setlocale(LC_ALL, 'ru_RU');

include_once 'helpers.php';
include_once 'cardsList.php';
include_once 'functions.php';


$title = 'ReadMe';
$user_name = 'Sergei';


$content = include_template('index.php', $cardsList);

$layoutContent = include_template('layout.php',
    [
    'content' => $content,
    'title'   => $title,
    'user'    => $user_name
    ]);

echo $layoutContent;

?>

