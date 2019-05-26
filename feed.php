<?php

include_once 'init.php';


if (!isUserLoggedIn()) {
    redirectHome();
}
$title = 'readme: моя лента';
$getTab = $_GET['tab'];
$getBlock = $_GET['block'];
$myUserId = $_SESSION['user-id'];

$cards = db_read_users_sub_posts($con, $getTab, $myUserId);



$content = include_template('feed.php', [
    'getTab' => $getTab,
    'cards'  => $cards,
    'con'    => $con,
]);
$html = include_template('layout.php', [
    'getBlock'   => $getBlock,
    'content'    => $content,
    'title'      => $title,
]);

echo $html;
