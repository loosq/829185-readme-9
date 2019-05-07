<?php

if (0 < $_GET['postId'] && $_GET['postId']  < 2000){

    include_once 'init.php';
    include_once 'helpers.php';
    include_once 'functions.php';

    $cards = db_read_posts_id($con,$_GET['postId']);
    $content = include_template('post.php', [
        'cards' => $cards
    ]);


    $title = 'Post';

    $layoutContent = include_template('layout.php', [
        'content' => $content,
        'title'   => $title,
        'user'    => $user_name,
        'is_auth' => $is_auth
    ]);

    echo $layoutContent;

} else {
    http_response_code(404);
}
