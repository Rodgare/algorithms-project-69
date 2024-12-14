<?php

namespace App;

function tokenize(string $text): array
{
    return preg_split('/\W+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
}

function buildIndexAndIDF(array $docs): array
{
    $index = [];
    $idf = [];
    $docCount = count($docs);

    foreach ($docs as $doc) {
        $docTokens = tokenize($doc['text']);
        $uniqueTokens = array_unique($docTokens);

        foreach ($docTokens as $token) {
            if (!isset($index[$doc['id']][$token])) {
                $index[$doc['id']][$token] = 0;
            }
            $index[$doc['id']][$token] += 1;
        }

        foreach ($uniqueTokens as $token) {
            if (!isset($idf[$token])) {
                $idf[$token] = 0;
            }
            $idf[$token] += 1;
        }
    }

    foreach ($idf as $token => &$value) {
        $value = log($docCount / $value);
    }

    return [$index, $idf];
}


function search(array $docs, string $query): array
{
    [$tfIndex, $idf] = buildIndexAndIDF($docs);
    $queryTokens = tokenize($query);

    $queryTF = array_count_values($queryTokens);
    $totalQueryTokens = count($queryTokens);
    foreach ($queryTF as &$freq) {
        $freq /= $totalQueryTokens;
    }

    $queryTFIDF = [];
    foreach ($queryTokens as $token) {
        $queryTFIDF[$token] = ($queryTF[$token] ?? 0) * ($idf[$token] ?? 0);
    }

    $docScores = [];
    foreach ($tfIndex as $docId => $docTokens) {
        $docTFIDF = [];
        foreach ($docTokens as $token => $tf) {
            $docTFIDF[$token] = $tf * ($idf[$token] ?? 0);
        }

        $dotProduct = 0;
        $normQuery = 0;
        $normDoc = 0;

        foreach ($queryTFIDF as $token => $queryWeight) {
            $docWeight = $docTFIDF[$token] ?? 0;
            $dotProduct += $queryWeight * $docWeight;
            $normQuery += $queryWeight ** 2;
            $normDoc += $docWeight ** 2;
        }

        if ($normQuery > 0 && $normDoc > 0) {
            $docScores[$docId] = $dotProduct / (sqrt($normQuery) * sqrt($normDoc));
        }
    }

    arsort($docScores);

    return array_keys($docScores);
}
