<?php

include_once 'init.php';
include_once 'helpers.php';
include_once 'functions.php';

$content = regFormValidation($con);

$title = 'Registration';

$html = include_template('layout.php', [
    'content' => $content,
    'title'   => $title,
]);

echo $html;