<?php

include_once 'init.php';
include_once 'helpers.php';
include_once 'functions.php';

$cards = db_read_users_posts($con, $_GET['tab']);

if (isset($_GET['tab'])) {
    $getTab = $_GET['tab'];
}
$content = include_template('index.php', [
    'cards' => $cards,
    'getTab' => $getTab
]);

$is_auth = rand(0, 1);
$user_name = 'Sergei';
$title = 'Readme';

$layoutContent = include_template('layout.php', [
    'content' => $content,
    'title'   => $title,
    'user'    => $user_name,
    'is_auth' => $is_auth,
]);

echo $layoutContent;
