<?php

include_once 'init.php';
include_once 'helpers.php';
include_once 'functions.php';

$content = validForm($con, $_GET['tab']);

$title = 'readme: публикация';

$html = include_template('layout.php', [
    'content' => $content,
    'title'   => $title,
]);

echo $html;
