<?php

//В сценарии главной страницы выполните подключение к MySQL;
$con = mysqli_connect('localhost', 'root', '', 'readme');
mysqli_set_charset($con, 'utf8');

if (!$con) {
    echo 'Ошибка соединения ' . mysqli_connect_error();
} else {
    //Отправьте SQL-запрос для получения типок контента;
    $sqlContentTypes = 'SELECT id, name FROM content_types';
    //Отправьте SQL-запрос для получения списка постов, объединенных с пользователями и отсортированный по популярности;
    $sqlPostsUsersMostViewed = 'SELECT title, text, number_of_views, u.name FROM posts p
      LEFT JOIN users u
      ON p.users_id = u.id
      ORDER BY number_of_views DESC';

    $resultContent = mysqli_query($con, $sqlContentTypes);
    $resultPosts = mysqli_query($con, $sqlPostsUsersMostViewed);
    $sqlError = mysqli_error($con);
    if ($resultContent) {
        $rows = mysqli_fetch_all($resultContent, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных' . $sqlError;
    }
    if ($resultPosts) {
        $postsRows = mysqli_fetch_all($resultPosts, MYSQLI_ASSOC);
    } else {
        echo 'Ошибка базы данных ' . $sqlError;
    }
}

//Используйте эти данные для показа списка постов и списка типов контента на главной странице.
echo 'Типы постов: <br>';
foreach ($rows as $row){
    echo '<br>Тип поста: ' . $row['name'];
}

echo '<br><br><br>Посты по популярности: ';
foreach ($postsRows as $postRow){
    echo '<br><br>Пользователь: ' . $postRow['name'];
    echo '<br>Заголовок: ' . $postRow['title'];
    echo '<br>Пост: ' .$postRow['text'];
    echo '<br>Количество просмотров ' . $postRow['number_of_views'];
}
