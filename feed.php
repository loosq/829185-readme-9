<?php

session_start();

if ($_SESSION) {
    include_once 'init.php';
    include_once 'helpers.php';
    include_once 'functions.php';

    $title = 'readme: моя лента';

    $content = include_template('feed.php');
    $html = include_template('layout.php', [
        'content' => $content,
        'title'   => $title,
    ]);
    echo $html;
} else {
    header('Location:/');
}
