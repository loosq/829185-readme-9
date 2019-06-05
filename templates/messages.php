<?php
/**
 * @var array $allChats Все чаты
 * @var string $allChats ['name'] имя пользователя
 * @var string $allChats ['avatat'] ссылка на автар пользователя
 * @var string $allChats ['date'] дата сообщения
 * @var string $allChats ['userGet'] получатель сообщения
 *
 * @var array $currentChat сообщения
 * @var int $userMe пользователь - хозяин странички
 * @var int $userOther пользователь с кем чат
 * @var string $currentChat ['name'] имя пользователя
 * @var string $currentChat ['date_of_origin'] дата сообщения
 * @var string $currentChat ['text'] текст сообщения
 * @var string $currentChat ['users_id_get'] id пользователя
 * @var string $currentChat ['avatar'] ссылка на автар пользователя
 */
?>
<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <?php if ($allChats): ?>
            <div class="messages__contacts">
                <ul class="messages__contacts-list tabs__list">
                    <?php foreach ($allChats as $allChatsVal): ?>
                        <li class="messages__contacts-item">
                            <a class="messages__contacts-tab tabs__item tabs__item--active <?= dbGetUserChatToId($con,
                                $allChatsVal['users_id_send'],
                                $allChatsVal['users_id_get']) === $userOther ? ' messages__contacts-tab--active' : '' ?>"
                               href="messages.php?block=messages&user=<?= dbGetUserChatToId($con,
                                   $allChatsVal['users_id_send'], $allChatsVal['users_id_get']) ?>"
                               data-user-caht-to="<?= dbGetUserChatToId($con, $allChatsVal['users_id_send'],
                                   $allChatsVal['users_id_get']) ?>">
                                <div class="messages__avatar-wrapper">
                                    <img class="messages__avatar"
                                         src="<?= dbGetUserChatToAva($con, $allChatsVal['users_id_get'],
                                             $allChatsVal['users_id_send']) ?>"
                                         alt="">
                                    <?php if (dbGetUnreadMsgFromUser($con,
                                            dbGetUserChatToId($con, $allChatsVal['users_id_send'],
                                                $allChatsVal['users_id_get']),
                                            $userSession['user-id']) && !(dbGetUserChatToId($con,
                                                $allChatsVal['users_id_send'],
                                                $allChatsVal['users_id_get']) === $userOther)): ?>
                                        <i class="messages__indicator"><?= dbGetUnreadMsgFromUser($con,
                                                dbGetUserChatToId($con, $allChatsVal['users_id_send'],
                                                    $allChatsVal['users_id_get']), $userSession['user-id']) ?></i>
                                    <?php endif ?>
                                </div>
                                <div class="messages__info">
                  <span class="messages__contact-name">
                    <?= dbGetUserChatToName($con, $allChatsVal['users_id_send'], $allChatsVal['users_id_get']) ?>
                  </span>
                                    <div class="messages__preview">
                                        <p class="messages__preview-text">
                                            <?= (int)$allChatsVal['users_id_send'] === $userMe ? 'Вы: ' . msgTextCut($allChatsVal['text'],
                                                    15) : msgTextCut($allChatsVal['text'], 15) ?>
                                        </p>
                                        <time class="messages__preview-time" datetime="<?= date('d.m.Y H:i',
                                            strtotime($allChatsVal['date_of_origin'])) ?>"><?= showTimeOfMsg($allChatsVal['date_of_origin']) ?></time>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>
        <div class="messages__chat">
            <div class="messages__chat-wrapper">
                <ul class="messages__list tabs__content tabs__content--active">
                    <?php if ($userOther): ?>
                        <?php foreach ($currentChat as $chat): ?>
                            <?php if ((int)$chat['users_id_send'] === $userMe): ?>
                                <li class="messages__item messages__item--my">
                                    <div class="messages__info-wrapper">
                                        <div class="messages__item-avatar">
                                            <a class="messages__author-link"
                                               href="profile.php?user=<?= $userSession['user-id'] ?>">
                                                <img class="messages__avatar" src="<?= $userSession['user-ava'] ?>"
                                                     alt="">
                                            </a>
                                        </div>
                                        <div class="messages__item-info">
                                            <a class="messages__author"
                                               href="profile.php?user=<?= $userSession['user-id'] ?>">
                                                <?= $userSession['user-name'] ?>
                                            </a>
                                            <time class="messages__time" datetime="<?= date('d.m.Y H:i',
                                                strtotime($chat['date_of_origin'])) ?>">
                                                <?= showTimeGap($chat['date_of_origin']) . ' назад' ?>
                                            </time>
                                        </div>
                                    </div>
                                    <p class="messages__text">
                                        <?= $chat['text'] ?>
                                    </p>
                                </li>
                            <?php else: ?>
                                <li class="messages__item">
                                    <div class="messages__info-wrapper">
                                        <div class="messages__item-avatar">
                                            <a class="messages__author-link"
                                               href="profile.php?user=<?= $userOther ?>">
                                                <img class="messages__avatar"
                                                     src="<?= dbGetUserAva($con, $userOther) ?>" alt="">
                                            </a>
                                        </div>
                                        <div class="messages__item-info">
                                            <a class="messages__author"
                                               href="profile.php?user=<?= $userOther ?>">
                                                <?= dbGetUserName($con, $userOther) ?>
                                            </a>
                                            <time class="messages__time" datetime="<?= date('d.m.Y H:i',
                                                strtotime($chat['date_of_origin'])) ?>">
                                                <?= showTimeGap($chat['date_of_origin']) . ' назад' ?>
                                            </time>
                                        </div>
                                    </div>
                                    <p class="messages__text">
                                        <?= $chat['text'] ?>
                                    </p>
                                </li>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                </ul>
            </div>
            <div class="comments">
                <form class="comments__form form" action="messages.php?user=<?= $userOther ?>" method="post">
                    <div class="comments__my-avatar">
                        <img class="comments__picture" src="<?= $userSession['user-ava'] ?>" alt="">
                    </div>
                    <textarea
                            class="comments__textarea form__textarea<?= isset($msgError) ? ' form__textarea--error' : '' ?>"
                            placeholder="" name="msg"></textarea>
                    <?php if (isset($msgError)): ?>
                        <p class="msg-error">Сообщение не может быть пустым</p>
                    <?php endif ?>
                    <label class="visually-hidden">Ваше сообщение</label>
                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                </form>
            </div>
        </div>
    </section>
</main>
