<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: личные сообщения';
$userSession = $_SESSION;

$userMe = $_SESSION['user-id'];
$userOther = $_GET['user'] ?? '';
$msgText = $_POST['msg'] ?? '';
$getBlock = $_GET['block'] ?? '';
$search = $_GET['q'] ?? '';
$allChats = dbGetAllChatsData($con, $userMe);
if ($userOther) {
    $currentChat = array_reverse(dbGetCurChat($con, $userMe, $userOther));
} else {
    $currentChat = '';
}
dbUpdateMsg($con, $userOther, $userMe);
msgFormValidation($con, $userMe, $userOther, $msgText);

$content = include_template('messages.php', [
    'userMe'      => $userMe,
    'userSession' => $userSession,
    'userOther'   => $userOther,
    'currentChat' => $currentChat,
    'allChats'    => $allChats,
    'con'         => $con,
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
