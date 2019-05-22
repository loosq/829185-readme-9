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
 * @var string $currentChatKey ['name'] имя пользователя
 * @var string $currentChatKey ['date_of_origin'] дата сообщения
 * @var string $currentChatKey ['text'] текст сообщения
 * @var string $currentChatKey ['users_id_get'] id пользователя
 * @var string $currentChatKey ['avatar'] ссылка на автар пользователя
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
                        <a class="messages__contacts-tab tabs__item tabs__item--active<?= $allChatsVal['userGet'] === (int)$userOther ? ' messages__contacts-tab--active' : '' ?>"
                           href="messages.php?block=messages&user=<?= $allChatsVal['userGet'] ?>">
                            <div class="messages__avatar-wrapper">
                                <img class="messages__avatar" src="<?= $allChatsVal['avatar'] ?>"
                                     alt="">
                            </div>
                            <div class="messages__info">
                  <span class="messages__contact-name">
                    <?= $allChatsVal['name'] ?>
                  </span>
                                <div class="messages__preview">
                                    <p class="messages__preview-text">
                                        <?= msgTextCut($allChatsVal['text'], 15) ?>
                                    </p>
                                    <time class="messages__preview-time" datetime="<?= date('d.m.Y H:i',
                                        strtotime($allChatsVal['date'])) ?>"><?= showTimeOfMsg($allChatsVal['date']) ?></time>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
<?php endif ?>

        <div class="messages__chat">
            <?php if (isset($userOther) && is_numeric($userOther)): ?>
                <div class="messages__chat-wrapper">
                    <ul class="messages__list tabs__content tabs__content--active">
                        <?php foreach ($currentChat as $currentChatKey): ?>
                            <li class="messages__item<?= ($currentChatKey['users_id_send'] === $userMe) ? '' : ' messages__item--my' ?>">
                                <div class="messages__info-wrapper">
                                    <div class="messages__item-avatar">
                                        <a class="messages__author-link"
                                           href="profile.php?user=<?= $currentChatKey['users_id_get'] ?>">
                                            <img class="messages__avatar" src="<?= $currentChatKey['avatar'] ?>" alt="">
                                        </a>
                                    </div>
                                    <div class="messages__item-info">
                                        <a class="messages__author"
                                           href="profile.php?user=<?= $currentChatKey['users_id_get'] ?>">
                                            <?= $currentChatKey['name'] ?>
                                        </a>
                                        <time class="messages__time" datetime="<?= date('d.m.Y H:i',
                                            strtotime($currentChatKey['date_of_origin'])) ?>">
                                            <?= showTimeGap($currentChatKey['date_of_origin']) . ' назад' ?>
                                        </time>
                                    </div>
                                </div>
                                <p class="messages__text">
                                    <?= $currentChatKey['text'] ?>
                                </p>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>
            <div class="comments">
                <form class="comments__form form" action="messages.php?user=<?= $userOther ?>" method="post">
                    <div class="comments__my-avatar">
                        <img class="comments__picture" src="<?= $currentChatKey['avatar'] ?>" alt="">
                    </div>
                    <textarea class="comments__textarea form__textarea" placeholder="" name="msg"></textarea>
                    <label class="visually-hidden">Ваше сообщение</label>
                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                </form>
            </div>
        </div>
    </section>
</main>
