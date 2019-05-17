<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: профиль';
$getTab = $_GET['tab'];
$getUser = $_GET['user'];
$user = db_get_user_info($con, $getUser);
$userDataPosts = db_get_user_posts($con, $getUser);
$userDataSubs = db_get_user_subs($con, $getUser);
$cards = db_get_user_arr_posts($con, $getUser);

$content = include_template('profile.php', [
    'getTab'        => $getTab,
    'getUser'       => $getUser,
    'con'           => $con,
    'cards'         => $cards,
    'user'          => $user,
    'userDataPosts' => $userDataPosts,
    'userDataSubs'  => $userDataSubs,
]);

$html = include_template('layout.php', [
    'getTab'  => $getTab,
    'content' => $content,
    'title'   => $title,
]);

echo $html;
