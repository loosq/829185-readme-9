<?php
/**
 * @var string $nameUserToSub имя пользователя кому оптравляется шаблон эмейла
 * @var string $nameUser      имя пользователя который подписался
 * @var string $idUser        id пользователя который подписался
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
<p>Здравствуйте, <?= $nameUserToSub ?>. На вас подписался новый пользователь
    <?= $nameUser ?>. Вот <a href="http://readme.loc/profile.php?user=<?= $idUser ?>&tab=posts">ссылка</a> на его профиль.</p>
</body>
</html>
