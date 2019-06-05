<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}
$title = 'readme: личные сообщения';
$userSession = $_SESSION;
$userMe = $_SESSION['user-id'];
$userOther = $_GET['user'] ?? '';
$msg = $_POST['msg'] ?? '';
$getBlock = $_GET['block'] ?? '';
$search = $_GET['q'] ?? '';
$allChats = dbGetAllChatsData($sqlGetAllChatsData, $con, $userMe);
if ($userOther) {
    $currentChat = array_reverse(dbGetCurChat($sqlGetCurChat, $con, $userMe, $userOther));
} else {
    $currentChat = '';
}
dbUpdateMsg($con, $userOther, $userMe);
$content = include_template('messages.php', [
    'userMe'      => $userMe,
    'userSession' => $userSession,
    'userOther'   => $userOther,
    'currentChat' => $currentChat,
    'allChats'    => $allChats,
    'con'         => $con,
]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msgError = '';
    if (!$msg) {
        $msgError = 'Минимальный комментарий 4 символа';
    }
    if ($msgError) {
        $content = include_template('messages.php', [
            'userMe'      => $userMe,
            'userSession' => $userSession,
            'userOther'   => $userOther,
            'currentChat' => $currentChat,
            'allChats'    => $allChats,
            'con'         => $con,
            'msgError'    => $msgError,
        ]);
    } else {
        $msgFunc = msgFormValidation($con, $userMe, $userOther, $msg);
        $currentChat = array_reverse(dbGetCurChat($sqlGetCurChat, $con, $userMe, $userOther));
        redirectBack();
        $content = include_template('messages.php', [
            'userMe'      => $userMe,
            'userSession' => $userSession,
            'userOther'   => $userOther,
            'currentChat' => $currentChat,
            'allChats'    => $allChats,
            'con'         => $con,
        ]);
    }
}
$html = include_template('layout.php', [
    'userSession' => $userSession,
    'getBlock'    => $getBlock,
    'content'     => $content,
    'title'       => $title,
    'con'         => $con,
    'search'      => $search,
]);
echo $html;
