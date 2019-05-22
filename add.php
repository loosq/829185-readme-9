<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$getTab = $_GET['tab'];
$content = validForm($con, $getTab);
$title = 'readme: публикация';
$userSession = $_SESSION;
$html = include_template('layout.php', [
    'userSession' => $userSession,
    'content'     => $content,
    'title'       => $title,
]);

echo $html;
