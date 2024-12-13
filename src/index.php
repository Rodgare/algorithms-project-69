<?php

namespace App;

function search(array $arr, string $word): array
{
    $res = [];
    foreach ($arr as $v) {
        $arrWords = explode(' ', $v['text']);
        foreach ($arrWords as $w) {
            if (trim($w, '!`') === trim($word, '!`')) {
                $res[] = $v['id'];
            }
        }
    }

    return array_unique($res);
}
