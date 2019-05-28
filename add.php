<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$getTab = $_GET['tab'] ?? '';
$getBlock = $_GET['block'] ?? '';
$search = $_GET['q'] ?? '';

$content = validForm($con, $getTab);

$title = 'readme: публикация';
$userSession = $_SESSION;
$html = include_template('layout.php', [
    'userSession' => $userSession,
    'content'     => $content,
    'title'       => $title,
    'search'      => $search,
    'con'         => $con,
    'getBlock'    => $getBlock,
]);

echo $html;
