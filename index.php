<?php

date_default_timezone_set('Europe/Moscow');
setlocale(LC_ALL, 'ru_RU');

include_once 'helpers.php';
include_once 'cards.php';
include_once 'functions.php';

$content = include_template('index.php', ['cards' => $cards]);

$layoutContent = include_template('layout.php',
    [
    'content' => $content,
    'title'   => $title,
    'user'    => $user_name
    ]
);

echo $layoutContent;
