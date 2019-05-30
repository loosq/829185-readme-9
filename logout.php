<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$_SESSION = [];
redirectHome();
