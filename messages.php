<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: личные сообщения';
$userSession = $_SESSION;

$userMe = $_SESSION['user-id'];
$userOther = $_GET['user'] ?? '';
$msgText = trim($_POST['msg']);
$getBlock = $_GET['block'] ?? '';
$allChats = dbGetAllChats($con, $userMe);

msgFormValidation($con, $userMe, $userOther, $msgText);

if ($userOther) {
    $arr = dbGetChat($con, $userMe, $userOther);
    $currentChat = array_reverse($arr);
} else {
    $currentChat = '';
}

$content = include_template('messages.php', [
    'userMe'      => $userMe,
    'userOther'   => $userOther,
    'currentChat' => $currentChat,
    'allChats'    => $allChats,
]);


$html = include_template('layout.php', [
    'userSession' => $userSession,
    'getBlock'    => $getBlock,
    'content'     => $content,
    'title'       => $title,
]);

echo $html;
