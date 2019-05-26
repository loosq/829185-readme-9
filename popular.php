<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: популярное';
$getBlock = $_GET['block'];
$getTab = $_GET['tab'] ?? 'all';
$itemCount = db_read_users_posts_by_tab($con, $getTab);
$curPage = $_GET['page'] ?? 1;
$pageItems = 6;
$pagesCount = ceil($itemCount / $pageItems);
$offset = ($curPage - 1) * $pageItems;
$pages = range(1, $pagesCount);
$cards = db_read_users_posts($con, $getTab, 'number_of_views', $pageItems, $offset);

if (isset($getTab)) {

    $content = include_template('popular.php', [
        'con'             => $con,
        'cards'           => $cards,
        'getTab'          => $getTab,
        'pagesCount'      => $pagesCount,
        'curPage'         => $curPage,
    ]);

    $html = include_template('layout.php', [
        'content' => $content,
        'getBlock'  => $getBlock,
        'title'   => $title,
    ]);

    echo $html;
}
