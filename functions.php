<?php
/**
 * Проверяет текст на количество символов, если символов больше, то
 * подставляет ссылку "Читать далее" с открытием по клику.
 * @param string $text текст
 * @param int $maxLength количество символов (по умолчанию 300)
 *
 * @return string текст с/без блоком-ссылкой "Читать далее"
 */
function cutText ($text, $maxLength = 300)
{
    if (strlen($text) > $maxLength) {
        $textArr = explode(' ', $text);
        $countLetters = 0;
        $j = 0;
        foreach ($textArr as $word) {
            $countLetters += strlen($word);
            $j++;
            if ($countLetters > $maxLength) {
                //Блоку "Читать далее" добавил левый отступ 0, потому что не получилось выровнить по умолчанию.

                return implode(' ', array_slice($textArr, 0, $j)) . '...' . '<br/><a class="post-text__more-link" href="#" style="margin-left: 0">Читать далее</a>';
            }
        }
    }

    return $text;
}
