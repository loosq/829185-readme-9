<?php

include_once 'init.php';

if (isUserLoggedIn()) {
    redirectHome();
}

$title = 'Registration';
$userSession = $_SESSION;
$content = regFormValidation($con);
$html = include_template('layout.php', [
    'userSession' => $userSession,
    'getTab'  => $getTab,
    'content' => $content,
    'title'   => $title,
]);
echo $html;
