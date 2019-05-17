<?php

include_once 'init.php';

$postId = $_GET['postId'];
$getTab = $_GET['tab'];

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: публикация';
$getUser = $_GET['user'];
$cards = db_read_posts_id($con, $postId);
$comments = db_get_comments_to_post($con, $postId);
$commentText = $_POST['comment-text'];
foreach ($cards as $card) {
    $userDataPosts = db_get_user_posts($con, $card['users_id']);
}
$userDataSubs = db_get_user_subs($con, $card['users_id']);
$user = db_get_user_info($con, $card['users_id']);
$content = include_template('post.php', [
    'commentText'   => $commentText,
    'cards'         => $cards,
    'comments'      => $comments,
    'user'          => $user,
    'userDataPosts' => $userDataPosts,
    'userDataSubs'  => $userDataSubs,
    'postId'        => $postId,
    'con'           => $con,
    'getUser'       => $getUser,
]);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    if (strlen($commentText) < 4) {
        $errors['comment-error'] = 'Минимальный комментарий 4 символа';
    }

    if (!empty($errors)) {
        $content = include_template('post.php', [
            'commentText'   => $commentText,
            'errors'        => $errors,
            'cards'         => $cards,
            'comments'      => $comments,
            'user'          => $user,
            'userDataPosts' => $userDataPosts,
            'userDataSubs'  => $userDataSubs,
            'postId'        => $postId,
            'getUser'       => $getUser,
            'con'           => $con,
        ]);
    } else {
        db_new_comment($con, $postId, $commentText, $_SESSION['user-id']);
    }
}

$layoutContent = include_template('layout.php', [
    'getTab'  => $getTab,
    'content' => $content,
    'title'   => $title,
]);

echo $layoutContent;
