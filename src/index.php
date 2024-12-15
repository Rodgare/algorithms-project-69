<?php

namespace App;

function tokenize(string $text): array
{
    return preg_split('/\W+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
}

function buildIndex($docs, $q)
{
    $index = [];
    $tokQ = tokenize($q);
    foreach ($docs as $doc) {
        $count = array_count_values(tokenize($doc['text']));
        foreach ($tokQ as $token) {
            if (isset($count[$token]) && isset($index[$doc['id']])) {
                $index[$doc['id']] += $count[$token];
            } elseif (isset($count[$token])) {
                $index[$doc['id']] = $count[$token];
            }
        }
    }

    return $index;
}

function calcIDF($docs, $q)
{
    $index = buildIndex($docs, $q);
    if ($index === []) {
        return [];
    }
    $idf = log10(count($docs) / count($index));
    foreach ($index as $docId => $tf) {
        $index[$docId] = $tf * $idf;
    }
    arsort($index);
    return array_keys($index);
}

function search($docs, $q)
{
    if ($q === '') {
        return [];
    }
    return calcIDF($docs, $q);
}
