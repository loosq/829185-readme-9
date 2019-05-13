<?php

include_once 'init.php';
include_once 'helpers.php';
include_once 'functions.php';
session_start();

if (!$_SESSION['user-name']) {

    $content = regFormValidation($con);

    $title = 'Registration';

    $html = include_template('layout.php', [
        'content' => $content,
        'title'   => $title,
    ]);

    echo $html;
} else {
    header('Location:/');
}
