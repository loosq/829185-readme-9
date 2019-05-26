<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: страница результатов поиска';
$userSession = $_SESSION;

$search = $_GET['q'] ?? '';

$content = multySearch($con, $search);

$html = include_template('layout.php', [
    'userSession' => $userSession,
    'content' => $content,
    'title'   => $title,
    'search'  => $search,
    'con'     => $con,
]);

echo $html;
