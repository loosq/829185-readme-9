
<div class="modal modal--adding modal--active">
    <div class="modal__wrapper">
        <button class="modal__close-button button" type="button">
            <svg class="modal__close-icon" width="18" height="18">
                <use xlink:href="#icon-close"></use>
            </svg>
            <span class="visually-hidden"><?= $modalTitle ?></span></button>
        <div class="modal__content">
            <h1 class="modal__title">Пост добавлен</h1>
            <p class="modal__desc">
                <?= $modalcontent ?>
            </p>
            <div class="modal__buttons">
                <a class="modal__button button button--main" href="#"><?= $modalleftBtn ?></a>
                <a class="modal__button button button--gray" href="#"><?= $modalRightBtn ?></a>
            </div>
        </div>
    </div>
</div>
