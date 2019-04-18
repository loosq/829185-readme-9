<?php

function cutText ($text, $maxLength = 300)
{
    if (strlen($text) > $maxLength) {
        $textArr = explode(' ', $text);
        $i = 0;
        $j = 0;
        foreach ($textArr as $word) {
            $i += strlen($word);
            $j++;
            if ($i > $maxLength) {
                //Блоку "Читать далее" добавил левый отступ 0, потому что не получилось выровнить по умолчанию.
                echo implode(' ', array_slice($textArr, 0, $j)) . '...' . '<br/><a class="post-text__more-link" href="#" style="margin-left: 0">Читать далее</a>';
                break;
            }
        }
    } else {
        echo $text;
    }
}

?>