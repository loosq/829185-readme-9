<?php
/**
 * Здесь собраны sql запросы величиной более 2х строк.
 */

$sqlReadUsersPosts = 'SELECT p.posts_id, p.post_date, p.title, p.content, p.quote_author, p.img_url, p.video_url,
                      p.users_site_url, p.number_of_views, p.users_id, p.content_types_id, u.users_id,
                      u.registration_date, u.email, u.name, u.password, u.avatar, u.contact_info, c.type,
                      (SELECT COUNT(l.post_id) FROM likes l WHERE l.post_id = posts_id) AS likes
                      FROM posts p 
                      LEFT JOIN users u
                      ON p.users_id = u.users_id
                      LEFT JOIN content_types c
                      ON p.content_types_id = c.content_types_id
                      WHERE p.isrepost = 0 AND ';

$sqlReadUsersPostsByTabType = 'SELECT COUNT(*) AS cnt FROM posts p 
                               LEFT JOIN users u
                               ON p.users_id = u.users_id
                               LEFT JOIN content_types c
                               ON p.content_types_id = c.content_types_id
                               WHERE p.content_types_id = (?) AND isrepost = 0';

$sqlReadUsersPostsByTab = 'SELECT COUNT(*) AS cnt FROM posts p 
                           LEFT JOIN users u
                           ON p.users_id = u.users_id
                           LEFT JOIN content_types c
                           ON p.content_types_id = c.content_types_id WHERE isrepost = 0';

$sqlReadUsersSubPostsType = 'SELECT * FROM posts p 
                             LEFT JOIN users u
                             ON p.users_id = u.users_id
                             LEFT JOIN content_types c
                             ON p.content_types_id = c.content_types_id
                             LEFT JOIN subscribers s
                             ON p.users_id = s.users_subscribe_id
                             WHERE s.users_id = ? AND p.content_types_id = ?
                             ORDER BY p.post_date';

$sqlReadUsersSubPosts = 'SELECT * FROM posts p  
                         LEFT JOIN users u
                         ON p.users_id = u.users_id
                         LEFT JOIN content_types c
                         ON p.content_types_id = c.content_types_id
                         LEFT JOIN subscribers s
                         ON p.users_id = s.users_subscribe_id
                         WHERE s.users_id = ?
                         ORDER BY p.post_date';

$sqlReadPostsId = 'SELECT posts_id, u.name, avatar, users_site_url, title, quote_author, p.content, 
                   c.type, p.img_url, p.video_url, u.users_id, p.isrepost 
                   FROM posts p 
                   LEFT JOIN users u
                   ON p.users_id = u.users_id
                   LEFT JOIN content_types c
                   ON p.content_types_id = c.content_types_id 
                   WHERE p.posts_id = ';

$sqlGetCommentsToPost = 'SELECT * FROM comments c
                         JOIN users u
                         ON c.users_id = u.users_id
                         WHERE post_id = ? 
                         ORDER BY c.data_time_of_origin DESC ';

$sqlGetAllChatsData = 'SELECT date_of_origin, text, users_id_send, users_id_get,  chat_hash
                       FROM messages 
                       WHERE messages_id IN(
                       SELECT max(messages_id) FROM messages 
                       WHERE users_id_send = (?) OR users_id_get = (?) 
                       GROUP BY chat_hash) 
                       ORDER BY chat_hash';

$sqlGetCurChat = 'SELECT * FROM messages WHERE 
                  (users_id_send = (?) AND users_id_get = (?)) 
                   OR 
                  (users_id_get= (?) AND users_id_send = (?))
                  ORDER BY date_of_origin DESC
                  LIMIT 3';

$sqlGetUsersByLike = 'SELECT p.posts_id, p.content, u.users_id, u.name, l.likes_date, ct.type, u.avatar, p.video_url FROM posts p
                      LEFT JOIN likes l ON
                      p.posts_id = l.post_id
                      LEFT JOIN users u ON
                      u.users_id = l.user_id
                      LEFT JOIN content_types ct ON 
                      p.content_types_id = ct.content_types_id
                      WHERE p.users_id = ? 
                      AND u.users_id IS NOT NULL 
                      ORDER BY likes_date DESC';

$sqlNewRepost = 'INSERT INTO posts (post_date, title, content, quote_author, img_url, video_url,
                 users_site_url, number_of_views, users_id, content_types_id, isrepost, user_author_id, post_origin_id)
                 VALUES (NOW(), ?, ?, ?, ?, ?, ?, 0, ?, ?, 1, ?, ?)';

$sqlmultySearchLongQ = 'SELECT * FROM posts p
                        LEFT JOIN users u ON p.users_id = u.users_id
                        LEFT JOIN content_types c ON p.content_types_id = c.content_types_id
                        WHERE (MATCH(p.content, p.title) AGAINST(?))';

$sqlmultySearchShortQ = 'SELECT * FROM posts p
                         LEFT JOIN users u ON p.users_id = u.users_id
                         LEFT JOIN content_types c ON p.content_types_id = c.content_types_id
                         WHERE p.content LIKE (?) OR p.title LIKE (?)';

$sqlmultySearchLongQTag = 'SELECT * FROM posts p 
                           LEFT JOIN posts_has_hashtags phh ON phh.posts_to_hashtags_id = p.posts_id
                           LEFT JOIN hashtags h ON phh.hashtags_to_posts_id = h.hashtags_id
                           LEFT JOIN content_types t ON p.content_types_id = t.content_types_id
                           LEFT JOIN users u ON p.users_id = u.users_id
                           WHERE MATCH(h.name) AGAINST(?) ORDER BY p.post_date DESC';

$sqlmultySearchShortQTag = 'SELECT * FROM posts p 
                            LEFT JOIN posts_has_hashtags phh ON phh.posts_to_hashtags_id = p.posts_id
                            LEFT JOIN hashtags h ON phh.hashtags_to_posts_id = h.hashtags_id
                            LEFT JOIN content_types t ON p.content_types_id = t.content_types_id
                            LEFT JOIN users u ON p.users_id = u.users_id
                            WHERE h.name LIKE (?) ORDER BY p.post_date DESC';
