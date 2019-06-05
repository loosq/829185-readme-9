<?php

/**
 * Отправляет эмейл пользователю на которого опдписались
 *
 * @param mysqli $con ресурс соединения
 * @param int $idUser id пользователя который подписывается
 * @param string $nameUser имя пользователя который подписывается
 * @param int $idUserToSub id пользователя на которого подписываются
 */
function sendEmailSub($con, $idUser, $nameUser, $idUserToSub)
{
    $transport = new Swift_SmtpTransport('phpdemo.ru', 25);
    $transport->setUsername('keks@phpdemo.ru');
    $transport->setPassword('htmlacademy');
    $mailer = new Swift_Mailer($transport);
    $logger = new Swift_Plugins_Loggers_ArrayLogger();
    $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
    $emailRes = mysqli_query($con, 'SELECT email FROM users WHERE users_id=' . $idUserToSub);
    $nameUserToSubRes = mysqli_query($con, 'SELECT name FROM users WHERE users_id=' . $idUserToSub);
    $emailUserToSub = mysqli_fetch_row($emailRes);
    $nameUserToSub = implode(mysqli_fetch_row($nameUserToSubRes));
    if ($idUser && $idUserToSub && $emailUserToSub) {
        $message = new Swift_Message();
        $message->setSubject('У вас новый подписчик');
        $message->setFrom(['no-reply@readme.loc' => 'Readme']);
        $message->setBcc($emailUserToSub, 'keks@phpdemo.ru');
        $msgContent = include_template('emailNewSub.php', [
            'idUser'        => $idUser,
            'nameUser'      => $nameUser,
            'nameUserToSub' => $nameUserToSub,
        ]);
        $message->setBody($msgContent, 'text/html');
        $result = $mailer->send($message);
        if (!$result) {
            echo 'не удалось отправить ' . $logger->dump();
        }
    }
}

/**
 * Отправляет сообщение подписчикам(если таковые имеются) о новом посте пользователя.
 *
 * @param mysqli $con ресурс соединения
 * @param int $userId id пользователя на которого подписаны
 * @param string $userName имя пользователя опубликовавшего пост
 * @param string $postTitle заголовок нового поста
 */
function sendEmailNewPost($con, $userId, $userName, $postTitle)
{
    $subs = dbGetAllSubs($con, $userId);
    if ($subs) {
        $transport = new Swift_SmtpTransport('phpdemo.ru', 25);
        $transport->setUsername('keks@phpdemo.ru');
        $transport->setPassword('htmlacademy');
        $mailer = new Swift_Mailer($transport);
        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
        foreach ($subs as $sub) {
            $message = new Swift_Message();
            $message->setSubject('Новая публикация от пользователя ' . $userName);
            $message->setFrom(['no-reply@readme.loc' => 'Readme']);
            $message->setTo($sub['email']);
            $adrName = $sub['name'];
            $msgContent = include_template('emailNewPost.php', [
                'adrName'   => $adrName,
                'userName'  => $userName,
                'userId'    => $userId,
                'postTitle' => $postTitle,
            ]);
            $message->setBody($msgContent, 'text/html');
            $mailer->send($message);
        }
    }
}
