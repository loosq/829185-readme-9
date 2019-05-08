<?php

include_once 'init.php';
include_once 'helpers.php';
include_once 'functions.php';

$content = validForm($con, $_GET['tab']);

$is_auth = rand(0, 1);
$user_name = 'Sergei';
$title = 'New post';

$layoutContent = include_template('layout.php', [
    'content' => $content,
    'title'   => $title,
    'user'    => $user_name,
    'is_auth' => $is_auth,
]);

echo $layoutContent;
