-- Добавляем новых пользователей
-- -----------------------------------------------------
-- Data for table `readme`.`users`
-- -----------------------------------------------------
START TRANSACTION;
USE `readme`;
INSERT INTO `readme`.`users` (`id`, `registration_date`, `email`, `name`, `password`, `avatar`, `contact_info`) VALUES (1, '2019-01-04 14:32:01', 'larisa@mail.ru', 'Лариса', 'qwerty', 'userpic-larisa-small.jpg', '');
INSERT INTO `readme`.`users` (`id`, `registration_date`, `email`, `name`, `password`, `avatar`, `contact_info`) VALUES (2, '2012-01-01 13:31:49', 'vlados666@bk.ru', 'Владик', 'lth;bvtyzrhtgxt1488', 'userpic.jpg', NULL);
INSERT INTO `readme`.`users` (`id`, `registration_date`, `email`, `name`, `password`, `avatar`, `contact_info`) VALUES (3, '2019-04-01 18:32:01', 'victorian@gmail.com', 'Виктор', 'jj~lLasldla341kdlaj', 'userpic-mark.jpg', NULL);

COMMIT;

-- Добавляем список постов
-- -----------------------------------------------------
-- Data for table `readme`.`posts`
-- -----------------------------------------------------
START TRANSACTION;
USE `readme`;
INSERT INTO `readme`.`posts` (`id`, `post_date`, `title`, `text`, `quote_author`, `img_url`, `video_url`, `users_site_url`, `number_of_views`, `users_id`) VALUES (1, '2019-04-24 16:00:53', 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', NULL, NULL, NULL, NULL, 1213, 1);
INSERT INTO `readme`.`posts` (`id`, `post_date`, `title`, `text`, `quote_author`, `img_url`, `video_url`, `users_site_url`, `number_of_views`, `users_id`) VALUES (2, '2019-04-23 03:00:23', 'Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', NULL, NULL, NULL, NULL, 1, 2);
INSERT INTO `readme`.`posts` (`id`, `post_date`, `title`, `text`, `quote_author`, `img_url`, `video_url`, `users_site_url`, `number_of_views`, `users_id`) VALUES (3, '2019-04-22 11:20:43', 'Наконец, обработал фотки!', '', NULL, 'rock-medium.jpg', NULL, '', 22, 3);
INSERT INTO `readme`.`posts` (`id`, `post_date`, `title`, `text`, `quote_author`, `img_url`, `video_url`, `users_site_url`, `number_of_views`, `users_id`) VALUES (4, '2019-04-23 19:42:21', 'Моя мечта', NULL, NULL, 'coast-medium.jpg', NULL, NULL, 3123, 1);
INSERT INTO `readme`.`posts` (`id`, `post_date`, `title`, `text`, `quote_author`, `img_url`, `video_url`, `users_site_url`, `number_of_views`, `users_id`) VALUES (5, '2019-04-24 01:21:19', 'Лучшие курсы', 'www.htmlacademy.ru', NULL, NULL, NULL, NULL, 100, 2);

COMMIT;

-- Добавляем комментарии
-- -----------------------------------------------------
-- Data for table `readme`.`comments`
-- -----------------------------------------------------
START TRANSACTION;
USE `readme`;
INSERT INTO `readme`.`comments` (`id`, `data_time_of_origin`, `text`, `users_id`, `post_id`) VALUES (1, '2019-04-24 14:00:03', 'LOL!', 2, 1);
INSERT INTO `readme`.`comments` (`id`, `data_time_of_origin`, `text`, `users_id`, `post_id`) VALUES (2, '2019-04-22 14:00:03', '<3', 1, 2);

COMMIT;

-- Добавляем лайки
-- -----------------------------------------------------
-- Data for table `readme`.`likes`
-- -----------------------------------------------------
START TRANSACTION;
USE `readme`;
INSERT INTO `readme`.`likes` (`id`, `posts_id`, `users_id`, `likes`) VALUES (1, 1, 1, 0);
INSERT INTO `readme`.`likes` (`id`, `posts_id`, `users_id`, `likes`) VALUES (2, 2, 2, 0);

COMMIT;

-- получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента
SELECT * FROM posts ORDER BY number_of_views DESC;
-- получить список постов для конкретного пользователя
SELECT * FROM posts WHERE users_id=1;
-- получить список комментариев для одного поста, в комментариях должен быть логин пользователя
SELECT u.name, text FROM comments c INNER JOIN users u ON c.users_id = u.id WHERE post_id=2;
-- добавить лайк к посту
UPDATE likes SET likes = likes + 1 WHERE posts_id=2;
-- подписаться на пользователя
INSERT INTO `readme`.`subscribers` (`users_id`, `users_subscribe_id`) VALUES (1, 2);
