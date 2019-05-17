<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'Registration';
$content = regFormValidation($con);
$html = include_template('layout.php', [
    'getTab'  => $getTab,
    'content' => $content,
    'title'   => $title,
]);
echo $html;
