<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}
$title = 'readme: моя лента';
$userSession = $_SESSION;
$getTab = $_GET['tab'] ?? '';
$getBlock = $_GET['block'] ?? '';
$myUserId = $_SESSION['user-id'];
$search = $_GET['q'] ?? '';
$cards = array_reverse(dbReadUsersSubPosts($con, $getTab, $myUserId));
$content = include_template('feed.php', [
    'getTab' => $getTab,
    'cards'  => $cards,
    'con'    => $con,
]);

$html = include_template('layout.php', [
    'userSession' => $userSession,
    'getBlock'    => $getBlock,
    'content'     => $content,
    'title'       => $title,
    'con'         => $con,
    'search'      => $search,
]);

echo $html;
