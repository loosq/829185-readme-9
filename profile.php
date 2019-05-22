<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: профиль';
$userSession = $_SESSION;
$getTab = $_GET['tab'] ?? '';
$getUser = $_GET['user'] ?? '';
$getPostId = $_GET['postId'] ?? '';
$getRepost = $_GET['repost'] ?? '';
$user = dbGetUserInfo($con, $getUser);
$userDataPosts = dbGetUserPosts($con, $getUser);
$userDataSubs = dbGetUserSubs($con, $getUser);
$cards = dbGetUserArrPosts($con, $getUser);
$likers = dbGetUsersByLike($con, $getUser);
$subsList = dbGetAllSubs($con, $getUser);

if($getUser !== $userSession['user-id'] && $getRepost){
    dbNewRepost($con, $getPostId, $userSession['user-id']);
}

$content = include_template('profile.php', [
    'likers'        => $likers,
    'subsList'      => $subsList,
    'userSession'   => $userSession,
    'getTab'        => $getTab,
    'getUser'       => $getUser,
    'con'           => $con,
    'cards'         => $cards,
    'user'          => $user,
    'userDataPosts' => $userDataPosts,
    'userDataSubs'  => $userDataSubs,
]);

$html = include_template('layout.php', [
    'userSession' => $userSession,
    'getTab'      => $getTab,
    'content'     => $content,
    'title'       => $title,
]);

echo $html;
