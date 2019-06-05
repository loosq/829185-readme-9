<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}
$title = 'readme: популярное';
$getBlock = $_GET['block'] ?? '';
$getTab = $_GET['tab'] ?? '';
$curPage = $_GET['page'] ?? 1;
$search = $_GET['q'] ?? '';
$getSort = $_GET['sort'] ?? '';
$userSession = $_SESSION;
$itemCount = dbReadUsersPostsByTab($sqlReadUsersPostsByTabType, $sqlReadUsersPostsByTab, $con, $getTab);
$pageItems = 9;
$pagesCount = ceil($itemCount / $pageItems);
$offset = ($curPage - 1) * $pageItems;
$pages = range(1, $pagesCount);
$cards = dbReadUsersPosts($sqlReadUsersPosts, $con, $getTab, $getSort, $pageItems, $offset);
$content = include_template('popular.php', [
    'con'         => $con,
    'cards'       => $cards,
    'getTab'      => $getTab,
    'getBlock'    => $getBlock,
    'getSort'     => $getSort,
    'pagesCount'  => $pagesCount,
    'curPage'     => $curPage,
    'userSession' => $userSession,
]);
$html = include_template('layout.php', [
    'userSession' => $userSession,
    'content'     => $content,
    'getBlock'    => $getBlock,
    'title'       => $title,
    'con'         => $con,
    'search'      => $search,
]);
echo $html;
