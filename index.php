<?php

include_once 'helpers.php';

$title = 'ReadMe';
$user_name = 'Sergei';
$cardsList = [
    [
        'userName' => 'Лариса',
        'avatar'   => 'userpic-larisa-small.jpg',
        'title'    => 'Цитата',
        'content'  => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'type'     => 'post-quote'
    ],
    [
        'userName' => 'Владик',
        'avatar'   => 'userpic.jpg',
        'title'    => 'Игра престолов',
        'content'  => 'Не могу дождаться начала финального сезона своего любимого сериала!',
        'type'     => 'post-text'
    ],
    [
        'userName' => 'Виктор',
        'avatar'   => 'userpic-mark.jpg',
        'title'    => 'Наконец, обработал фотки!',
        'content'  => 'rock-medium.jpg',
        'type'     => 'post-photo'
    ],
    [
        'userName' => '	Лариса',
        'avatar'   => 'userpic-larisa-small.jpg',
        'title'    => 'Моя мечта',
        'content'  => 'coast-medium.jpg',
        'type'     => 'post-photo'
    ],
    [
        'userName' => 'Владик',
        'avatar'   => 'userpic.jpg',
        'title'    => 'Лучшие курсы',
        'content'  => 'www.htmlacademy.ru',
        'type'     => 'post-link'
    ]
];

$content = include_template('index.php', $cardsList);

$layoutContent = include_template('layout.php',[
    'content' => $content,
    'title'   => $title,
    'user'    => $user_name]);

print ($layoutContent);

?>

