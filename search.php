<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$title = 'readme: страница результатов поиска';

$search = $_GET['q'] ?? '';

if ($search) {
    $sql = 'SELECT * FROM posts p
    LEFT JOIN users u ON p.users_id = u.users_id
    LEFT JOIN content_types c ON p.content_types_id = c.content_types_id
    WHERE MATCH(p.content, p.title) AGAINST(?)';
    $stmt = db_get_prepare_stmt($con, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cards = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (!$cards) {
        $content = include_template('search-no-results.php', [
            'search' => $search,
        ]);
    } else {

        $content = include_template('search.php', [
            'cards'  => $cards,
            'search' => $search,
            'con'    => $con,
        ]);
    }

} else {
    $content = include_template('search-no-results.php', [
        'search' => $search,
    ]);
}


$html = include_template('layout.php', [
    'content' => $content,
    'title'   => $title,
    'search'  => $search,
    'con'     => $con,
]);

echo $html;