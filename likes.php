<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$userId = $_SESSION['user-id'];
$postId = $_GET['postId'];
$getLike = dbGetLike($con, $postId, $userId);

if ($getLike) {
    dbDelLike($con, $postId, $userId);
} else {
    dbAddLike($con, $postId, $userId);
}
