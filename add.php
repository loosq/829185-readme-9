<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$getTab = $_GET['tab'];
$content = validForm($con, $getTab);
$title = 'readme: публикация';
$html = include_template('layout.php', [
    'content' => $content,
    'title'   => $title,
]);

echo $html;
