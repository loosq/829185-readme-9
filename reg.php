<?php

include_once 'init.php';

if (!$_SESSION['user-name']) {

    $content = regFormValidation($con);

    $title = 'Registration';

    $html = include_template('layout.php', [
        'getTab'  => $getTab,
        'content' => $content,
        'title'   => $title,
    ]);

    echo $html;
} else {
    redirectHome();
}
