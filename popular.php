<?php

include_once 'init.php';
include_once 'helpers.php';
include_once 'functions.php';
session_start();

if ($_SESSION) {

    $cards = db_read_users_posts($con, $_GET['tab']);

    if (isset($_GET['tab'])) {
        $getTab = $_GET['tab'];
    }
    $content = include_template('popular.php', [
        'cards'  => $cards,
        'getTab' => $getTab,
    ]);

    $title = 'readme: популярное';

    $html = include_template('layout.php', [
        'content' => $content,
        'title'   => $title,
    ]);

    echo $html;
} else {
    header('Location:/');
}
