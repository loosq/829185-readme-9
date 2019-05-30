<?php
/**
 * @var string $adrName имя пользователя кому оптравляется шаблон эмейла
 * @var string $userName имя пользователя который опубликовал пост
 * @var string $postTitle заголовок поста
 * @var string $userId id пользователя который опубликовал пост
 */

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h1>Новый пост!</h1>
<p>Здравствуйте, <?= $adrName ?>. Пользователь <?= $userName ?> только что опубликовал новую запись
    <?= $postTitle ?>. <a href="http://readme.loc/profile.php?user=<?= $userId ?>&tab=posts">Посмотрите её на странице пользователя</a>.</p>
</body>
</html>
