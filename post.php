<?php

include_once 'init.php';

$postId = $_GET['postId'] ?? '';
$getTab = $_GET['tab'] ?? '';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: публикация';
$userSession = $_SESSION;
$getUser = $_GET['user'] ?? '';
$getBlock = $_GET['block'] ?? '';
$search = $_GET['q'] ?? '';
$cards = dbReadPostsId($con, $postId);
$allComments = $_GET['all'] ?? '';
$addView = dbAddViewToPost($con, $userSession['user-id'], $postId);
$comments = dbGetCommentsToPost($con, $postId, $allComments);
$commentText = $_POST['comment-text'] ?? '';

foreach ($cards as $card) {
    $userDataPosts = dbGetUserPosts($con, $card['users_id']);
}
$userDataSubs = dbGetUserSubs($con, $card['users_id']);
$user = dbGetUserInfo($con, $card['users_id']);
$content = include_template('post.php', [
    'userSession'   => $userSession,
    'commentText'   => $commentText,
    'cards'         => $cards,
    'comments'      => $comments,
    'user'          => $user,
    'userDataPosts' => $userDataPosts,
    'userDataSubs'  => $userDataSubs,
    'postId'        => $postId,
    'con'           => $con,
    'getUser'       => $getUser,
    'allComments'   => $allComments,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    if (strlen($commentText) < 4) {
        $errors['comment-error'] = 'Минимальный комментарий 4 символа';
    }

    if (!empty($errors)) {
        $content = include_template('post.php', [
            'userSession'   => $userSession,
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
        dbNewComment($con, $postId, $commentText, $_SESSION['user-id']);
    }
}

$html = include_template('layout.php', [
    'userSession' => $userSession,
    'getTab'      => $getTab,
    'content'     => $content,
    'title'       => $title,
    'con'         => $con,
    'search'      => $search,
    'getBlock'    => $getBlock,
]);

echo $html;
