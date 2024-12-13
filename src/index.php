<?php

namespace App;

function search(array $arr, string $word): array
{
    $res = [];
    foreach ($arr as $v) {
        $arrWords = explode(' ', $v['text']);
        if (in_array($word, $arrWords)) {
            $res[] = $v['id'];
        }
    }

    return $res;
}
